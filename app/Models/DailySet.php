<?php
namespace app\Models;

use app\Core\Database;

class DailySet
{
    public function findBySubtestAndDate($subtestId, $date, $mode)
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('
            SELECT id
            FROM daily_sets
            WHERE subtest_id = ? AND for_date = ? AND mode = ?
            LIMIT 1
        ');
        $stmt->execute([$subtestId, $date, $mode]);
        return $stmt->fetch();
    }
}