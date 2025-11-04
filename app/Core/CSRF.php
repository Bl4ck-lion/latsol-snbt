<?php
namespace app\Core;

class CSRF {
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function mustValidate() {
        if (!isset($_POST['csrf_token']) || !self::validateToken($_POST['csrf_token'])) {
            throw new \Exception('CSRF token mismatch');
        }
    }
}