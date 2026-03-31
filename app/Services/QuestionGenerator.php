<?php
namespace app\Services;

use app\Core\Database;
use app\Models\Subtest;

class QuestionGenerator
{
    private $aiClient;

    public function __construct()
    {
        $this->aiClient = new AIClient();
    }

    public function generateDaily(int $subtestId, string $date, string $mode): bool
    {
        $pdo = Database::pdo();

        $subtestModel = new Subtest();
        $subtest = $subtestModel->find($subtestId);

        if (!$subtest) {
            error_log("Subtest with ID $subtestId not found.");
            return false;
        }

        $prompt = $this->buildPrompt($subtest['name'], $subtest['description']);
        $generatedData = $this->aiClient->generateQuestions($prompt);

        if (!$this->validateGeneratedData($generatedData)) {
            error_log("Failed to generate or validate AI data for subtest $subtestId on $date.");
            return false;
        }

        $pdo->beginTransaction();
        try {
            // Create daily set
            $stmt = $pdo->prepare('INSERT INTO daily_sets (subtest_id, for_date, mode, ai_prompt, ai_model) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$subtestId, $date, $mode, $prompt, 'gpt-4o-mini']); // Model is hardcoded for now
            $dailySetId = $pdo->lastInsertId();

            // Insert questions and choices
            $questionStmt = $pdo->prepare('INSERT INTO questions (daily_set_id, number, stem, explanation, correct_choice) VALUES (?, ?, ?, ?, ?)');
            $choiceStmt = $pdo->prepare('INSERT INTO choices (question_id, label, content) VALUES (?, ?, ?)');

            foreach ($generatedData['questions'] as $q) {
                $questionStmt->execute([$dailySetId, $q['number'], $q['stem'], $q['explanation'], $q['correct']]);
                $questionId = $pdo->lastInsertId();

                foreach ($q['choices'] as $label => $content) {
                    $choiceStmt->execute([$questionId, $label, $content]);
                }
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Database error during question generation: " . $e->getMessage());
            return false;
        }
    }

    private function buildPrompt(string $subtestName, string $subtestDescription): string
    {
        return "Anda adalah generator soal SNBT untuk subtest: '{$subtestName}'.\n" .
            "Fokus utama subtest ini adalah: '{$subtestDescription}'.\n" .
            "Buat 5 soal pilihan ganda dalam Bahasa Indonesia dengan tingkat kesulitan menengah. Soal harus relevan dengan deskripsi dan kisi-kisi SNBT terbaru.\n" .
            "Pastikan setiap soal logis, jelas, dan dapat diselesaikan tanpa informasi eksternal.\n\n" .
            "Format output harus JSON yang valid, seperti contoh berikut:\n" .
            "{\n" .
            "  \"questions\": [\n" .
            "    {\n" .
            "      \"number\": 1,\n" .
            "      \"stem\": \"...\",\n" .
            "      \"choices\": {\"A\":\"...\",\"B\":\"...\",\"C\":\"...\",\"D\":\"...\",\"E\":\"...\"},\n" .
            "      \"correct\": \"C\",\n" .
            "      \"explanation\": \"...\"\n" .
            "    },\n" .
            "    ...\n" .
            "  ]\n" .
            "}\n" .
            "Tanpa teks lain di luar JSON. Pastikan tidak ambigu, logis, dan dapat dipecahkan tanpa alat eksternal.";
    }

    private function validateGeneratedData(?array $data): bool
    {
        if (empty($data) || !isset($data['questions']) || !is_array($data['questions']) || count($data['questions']) !== 5) {
            return false;
        }

        foreach ($data['questions'] as $q) {
            if (
                !isset($q['stem']) || !isset($q['choices']) || !is_array($q['choices']) || count($q['choices']) !== 5 ||
                !isset($q['correct']) || !in_array($q['correct'], ['A', 'B', 'C', 'D', 'E']) ||
                !isset($q['explanation'])
            ) {
                return false;
            }
        }
        return true;
    }
}