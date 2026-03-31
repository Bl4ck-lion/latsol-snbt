<?php
namespace app\Models;

use app\Core\Database;
use PDO;

class User {
    public function find($id) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($username, $email, $password) {
        $pdo = Database::pdo();
        $password_hash = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $password_hash]);
    }
}