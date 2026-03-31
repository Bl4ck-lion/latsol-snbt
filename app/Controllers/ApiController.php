<?php
namespace app\Controllers;

use app\Models\Attempt;
use function app\jsonResponse;

class ApiController
{
    public function scoreboard()
    {
        $subtest_id = (int)($_GET['subtest_id'] ?? 0);
        $mode = $_GET['mode'] ?? 'kejar_waktu';
        $date = $_GET['date'] ?? date('Y-m-d');

        $attemptModel = new Attempt();
        $scoreboard = $attemptModel->getScoreboard($subtest_id, $mode, $date);

        return jsonResponse(200, ['top' => $scoreboard]);
    }
}