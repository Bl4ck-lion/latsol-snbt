<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="csrf-token" content="<?= app\Core\CSRF::generateToken(); ?>">
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <h1>Welcome, <?= htmlspecialchars($user['username']); ?></h1>
            <h2>Subtests</h2>
            <div class="bento-grid">
                <?php foreach ($subtests as $subtest): ?>
                    <div class="bento-item">
                        <h3><?= htmlspecialchars($subtest['name']); ?></h3>
                        <p><?= htmlspecialchars($subtest['description']); ?></p>
                        <button class="start-attempt" data-subtestid="<?= $subtest['id'] ?>" data-mode="kejar_waktu">Kejar Waktu</button>
                        <button class="start-attempt" data-subtestid="<?= $subtest['id'] ?>" data-mode="santai">Santai</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="/assets/js/dashboard.js"></script>
</body>
</html>