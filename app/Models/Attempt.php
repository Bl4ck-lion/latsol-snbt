<?php
namespace app\Models;

use app\Core\Database;
use PDO;

class Attempt {
    public function find($id) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM attempts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUserAndDate($userId, $date) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM attempts WHERE user_id = ? AND for_date = ?");
        $stmt->execute([$userId, $date]);
        return $stmt->fetch();
    }

    public function create($userId, $subtestId, $mode, $deviceHash) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("INSERT INTO attempts (user_id, subtest_id, for_date, mode, device_hash) VALUES (?, ?, CURDATE(), ?, ?)");
        $stmt->execute([$userId, $subtestId, $mode, $deviceHash]);
        return $pdo->lastInsertId();
    }

    public function getScoreboard($subtestId, $mode, $date) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('
            SELECT u.username, a.score, a.duration_seconds
            FROM attempts a
            JOIN users u ON u.id = a.user_id
            WHERE a.subtest_id = ? AND a.for_date = ? AND a.mode = ? AND a.status = "submitted"
            ORDER BY a.score DESC, a.duration_seconds ASC
            LIMIT 50
        ');
        $stmt->execute([$subtestId, $date, $mode]);
        return $stmt->fetchAll();
    }

    public function getAttemptDetails(int $attemptId): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('
            SELECT
                q.number,
                q.stem,
                q.explanation,
                q.correct_choice,
                ai.chosen as user_choice,
                (q.correct_choice = ai.chosen) as is_correct,
                (SELECT JSON_OBJECTAGG(label, content) FROM choices c WHERE c.question_id = q.id) as choices
            FROM attempt_items ai
            JOIN questions q ON ai.question_id = q.id
            WHERE ai.attempt_id = ?
            ORDER BY q.number
        ');
        $stmt->execute([$attemptId]);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Decode the JSON choices string into an array
        foreach ($results as &$result) {
            if (isset($result['choices'])) {
                $result['choices'] = json_decode($result['choices'], true);
            }
        }

        return $results;
    }
}