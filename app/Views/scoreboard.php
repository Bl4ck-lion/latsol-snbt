<!DOCTYPE html>
<html>
<head>
    <title>Scoreboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <h1>Scoreboard</h1>
            <div class="filters bento-grid">
                <select id="subtest-filter">
                    <?php foreach ($subtests as $subtest): ?>
                        <option value="<?= $subtest['id'] ?>"><?= htmlspecialchars($subtest['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="mode-filter">
                    <option value="kejar_waktu">Kejar Waktu</option>
                    <option value="santai">Santai</option>
                </select>
                <input type="date" id="date-filter" value="<?= date('Y-m-d') ?>">
            </div>
            <table id="scoreboard-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Score</th>
                        <th>Duration (s)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="/assets/js/scoreboard.js"></script>
</body>
</html>