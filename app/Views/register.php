<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <h1>Register</h1>
            <form action="/register" method="post">
                <input type="hidden" name="csrf_token" value="<?= app\Core\CSRF::generateToken(); ?>">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <button type="submit">Register</button>
            </form>
        </div>
    </div>
</body>
</html>