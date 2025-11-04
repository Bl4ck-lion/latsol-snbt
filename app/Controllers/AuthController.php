<?php
namespace app\Controllers;

use app\Core\Auth;
use app\Core\CSRF;
use app\Models\User;
use app\Helpers;

class AuthController {
    public function showRegistrationForm() {
        require __DIR__ . '/../Views/register.php';
    }

    public function register() {
        CSRF::mustValidate();
        $user = new User();
        $user->create($_POST['username'], $_POST['email'], $_POST['password']);
        header('Location: /login');
    }

    public function showLoginForm() {
        require __DIR__ . '/../Views/login.php';
    }

    public function login() {
        CSRF::mustValidate();
        if (Auth::login($_POST['email'], $_POST['password'])) {
            header('Location: /dashboard');
        } else {
            header('Location: /login?error=1');
        }
    }

    public function logout() {
        Auth::logout();
        header('Location: /');
    }
}