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
}