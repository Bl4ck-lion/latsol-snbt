<?php
namespace app\Controllers;

use app\Core\CSRF;
use app\Services\QuestionGenerator;
use app\Models\Subtest;
use function app\jsonResponse;

class AdminController {

    /**
     * This method is a web-based trigger for the daily question generation script.
     * It should be protected by authentication and authorization in a real application.
     */
    public function generateDaily() {
        CSRF::mustValidate();
        try {
            $subtestModel = new Subtest();
            $activeSubtests = $subtestModel->getActiveSubtests();
            $questionGenerator = new QuestionGenerator();
            $date = date('Y-m-d');

            foreach ($activeSubtests as $subtest) {
                $questionGenerator->generateDaily($subtest['id'], $date, 'kejar_waktu');
                $questionGenerator->generateDaily($subtest['id'], $date, 'santai');
            }

            return jsonResponse(200, ['status' => 'success', 'message' => 'Daily question generation process completed.']);
        } catch (\Exception $e) {
            error_log('Error in AdminController::generateDaily: ' . $e->getMessage());
            return jsonResponse(500, ['status' => 'error', 'message' => 'An internal server error occurred during generation.']);
        }
    }
}
