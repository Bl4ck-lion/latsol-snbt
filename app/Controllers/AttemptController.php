<?php
namespace app\Controllers;

use app\Core\Auth;
use app\Core\CSRF;
use app\Core\Database;
use app\Models\Attempt;
use app\Models\DailySet;
use app\Models\Question;
use function app\jsonResponse;
use function app\getDeviceHash;

class AttemptController {

    public function show() {
        require __DIR__ . '/../Views/attempt.php';
    }

    public function start() {
        $user = Auth::user();
        CSRF::mustValidate();
        $subtest_id = (int)($_POST['subtest_id'] ?? 0);
        $mode = $_POST['mode'] === 'kejar_waktu' ? 'kejar_waktu' : 'santai';
        $device_hash = getDeviceHash();

        if (!$user || !$subtest_id || !$device_hash) {
            return jsonResponse(400, ['error' => 'bad_request']);
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            // lock existing attempt same day
            $st = $pdo->prepare('SELECT id FROM attempts WHERE (user_id=? OR device_hash=?) AND for_date=CURDATE() LIMIT 1 FOR UPDATE');
            $st->execute([$user['id'],$device_hash]);
            if ($st->fetch()) {
                throw new \Exception('attempt_exists');
            }

            $dailySetModel = new DailySet();
            $dailySet = $dailySetModel->findBySubtestAndDate($subtest_id, date('Y-m-d'), $mode);
            if (!$dailySet) {
                throw new \Exception('daily_not_ready');
            }

            $attemptModel = new Attempt();
            $attempt_id = $attemptModel->create($user['id'], $subtest_id, $mode, $device_hash);

            $questionModel = new Question();
            $questions = $questionModel->getQuestionsForDailySet($dailySet['id']);

            $ai = $pdo->prepare('INSERT INTO attempt_items (attempt_id, question_id) VALUES (?,?)');
            foreach ($questions as $qus) {
                $ai->execute([$attempt_id, $qus['id']]);
            }

            $pdo->commit();
            return jsonResponse(200, ['attempt_id' => $attempt_id, 'questions' => $questions, 'mode' => $mode]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            return jsonResponse(409, ['error' => $e->getMessage()]);
        }
    }

    public function answer() {
        $user = Auth::user();
        CSRF::mustValidate();
        $attempt_id = (int)($_POST['attempt_id'] ?? 0);
        $question_id = (int)($_POST['question_id'] ?? 0);
        $choice = $_POST['choice'] ?? null;

        if (!$user || !$attempt_id || !$question_id || !$choice) {
            return jsonResponse(400, ['error' => 'bad_request']);
        }

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE attempt_items SET chosen = ?, responded_at = NOW() WHERE attempt_id = ? AND question_id = ?');
        $stmt->execute([$choice, $attempt_id, $question_id]);

        return jsonResponse(200, ['saved' => true]);
    }

    public function submit() {
        $user = Auth::user();
        CSRF::mustValidate();
        $attempt_id = (int)($_POST['attempt_id'] ?? 0);

        if (!$user || !$attempt_id) {
            return jsonResponse(400, ['error' => 'bad_request']);
        }

        $pdo = Database::pdo();
        // Calculate score
        $stmt = $pdo->prepare('
            SELECT COUNT(*) as score
            FROM attempt_items ai
            JOIN questions q ON ai.question_id = q.id
            WHERE ai.attempt_id = ? AND ai.chosen = q.correct_choice
        ');
        $stmt->execute([$attempt_id]);
        $score = $stmt->fetchColumn();

        // Get duration
        $stmt = $pdo->prepare('SELECT started_at FROM attempts WHERE id = ?');
        $stmt->execute([$attempt_id]);
        $started_at = $stmt->fetchColumn();
        $duration_seconds = time() - strtotime($started_at);

        // Update attempt
        $stmt = $pdo->prepare('UPDATE attempts SET score = ?, duration_seconds = ?, finished_at = NOW(), status = "submitted" WHERE id = ?');
        $stmt->execute([$score, $duration_seconds, $attempt_id]);

        return jsonResponse(200, ['score' => $score, 'duration_seconds' => $duration_seconds]);
    }

    public function status() {
        // This can be expanded to return more detailed status for polling
        $user = Auth::user();
        $attempt_id = (int)($_GET['id'] ?? 0);
        if (!$user || !$attempt_id) {
            return jsonResponse(400, ['error' => 'bad_request']);
        }
        $attempt = (new Attempt())->find($attempt_id);
        return jsonResponse(200, ['status' => $attempt['status']]);
    }

    public function showResult($attemptId) {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $attemptModel = new Attempt();
        $attempt = $attemptModel->find((int)$attemptId);

        // Ensure the user owns this attempt
        if (!$attempt || $attempt['user_id'] !== $user['id']) {
            header('HTTP/1.0 404 Not Found');
            echo 'Attempt not found.';
            exit;
        }

        $results = $attemptModel->getAttemptDetails((int)$attemptId);

        require __DIR__ . '/../Views/result.php';
    }
}