<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Latsol SNBT Harian</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?= app\Core\CSRF::generateToken(); ?>">
</head>
<body>
    <div class="background-animation"></div>
    <div class="container">
        <header class="main-header">
            <h1>Selamat Datang, <?= htmlspecialchars($user['username']); ?>!</h1>
            <p>Pilih subtes untuk memulai latihan hari ini.</p>
        </header>

        <main class="glass-card">
            <div class="bento-grid-dashboard">
                <?php if (empty($subtests)): ?>
                    <div class="bento-item-full">
                        <h3>Belum ada subtes yang tersedia.</h3>
                        <p>Silakan hubungi administrator.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($subtests as $subtest): ?>
                        <div class="bento-item-subtest">
                            <div class="bento-content">
                                <h3><?= htmlspecialchars($subtest['name']); ?></h3>
                                <p class="subtest-description"><?= htmlspecialchars($subtest['description']); ?></p>
                                <div class="button-group">
                                    <button class="start-attempt" data-subtestid="<?= $subtest['id'] ?>" data-mode="kejar_waktu">
                                        <span class="icon">⚡️</span> Kejar Waktu
                                    </button>
                                    <button class="start-attempt" data-subtestid="<?= $subtest['id'] ?>" data-mode="santai">
                                        <span class="icon">🧘</span> Santai
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
             <form action="/logout" method="post" style="margin-top: 2rem;">
                <?= app\Core\CSRF::generateInput(); ?>
                <button type="submit" class="button-logout">Logout</button>
            </form>
        </main>
    </div>

    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
