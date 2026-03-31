<?php
namespace app\Models;

use app\Core\Database;

class Question
{
    public function getQuestionsForDailySet($dailySetId)
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('
            SELECT q.id, q.number, q.stem, q.correct_choice, c.label, c.content
            FROM questions q
            JOIN choices c ON c.question_id = q.id
            WHERE q.daily_set_id = ?
            ORDER BY q.number, c.label
        ');
        $stmt->execute([$dailySetId]);
        $rows = $stmt->fetchAll();

        $questions = [];
        foreach ($rows as $row) {
            $questionId = $row['id'];
            if (!isset($questions[$questionId])) {
                $questions[$questionId] = [
                    'id' => $row['id'],
                    'number' => $row['number'],
                    'stem' => $row['stem'],
                    'correct_choice' => $row['correct_choice'],
                    'choices' => [],
                ];
            }
            $questions[$questionId]['choices'][$row['label']] = $row['content'];
        }

        return array_values($questions);
    }
}