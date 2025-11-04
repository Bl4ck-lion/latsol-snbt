<?php
namespace app\Controllers;

use app\Models\Attempt;
use app\Models\Subtest;

class ScoreboardController
{
    public function index()
    {
        $subtestModel = new Subtest();
        $subtests = $subtestModel->getActiveSubtests();

        require __DIR__ . '/../Views/scoreboard.php';
    }
}