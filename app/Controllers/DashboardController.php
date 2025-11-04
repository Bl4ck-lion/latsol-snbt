<?php
namespace app\Controllers;

use app\Core\Auth;
use app\Models\Subtest;

class DashboardController
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $subtestModel = new Subtest();
        $subtests = $subtestModel->getActiveSubtests();

        require __DIR__ . '/../Views/dashboard.php';
    }
}