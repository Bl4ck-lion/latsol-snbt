<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <h1>Login</h1>
            <?php if (isset($_GET['error'])): ?>
                <p class="error">Invalid email or password.</p>
            <?php endif; ?>
            <form action="/login" method="post">
                <input type="hidden" name="csrf_token" value="<?= app\Core\CSRF::generateToken(); ?>">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>