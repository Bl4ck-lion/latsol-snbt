<!DOCTYPE html>
<html>
<head>
    <title>Attempt</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="csrf-token" content="<?= app\Core\CSRF::generateToken(); ?>">
</head>
<body>
    <div class="container">
        <div class="glass-card" id="quiz-container">
            <!-- Quiz content will be dynamically inserted here -->
            <h1>Loading Quiz...</h1>
        </div>
    </div>

    <script src="/assets/js/attempt.js"></script>
</body>
</html>