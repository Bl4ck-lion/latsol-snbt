<?php

require __DIR__ . '/../vendor/autoload.php'; // Assuming composer is used for autoloading

use app\Services\QuestionGenerator;
use app\Models\Subtest;

// This script should be protected (e.g., run from CLI or with a secret key)
if (php_sapi_name() !== 'cli' && ($_GET['secret'] ?? '') !== 'your-secret-key') {
    die('Access denied');
}

$subtestModel = new Subtest();
$activeSubtests = $subtestModel->getActiveSubtests();
$questionGenerator = new QuestionGenerator();
$date = date('Y-m-d');

foreach ($activeSubtests as $subtest) {
    echo "Generating questions for subtest: {$subtest['name']}...\n";

    // Generate for both modes
    $questionGenerator->generateDaily($subtest['id'], $date, 'kejar_waktu');
    $questionGenerator->generateDaily($subtest['id'], $date, 'santai');

    echo "Done.\n";
}