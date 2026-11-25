<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash, role
         FROM users WHERE username = :username LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('Location: /dashboard.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>

<!DOCTYPE html>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Obscura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/obscura.css" rel="stylesheet">
</head>
<body>
<div class="auth-layout">
    <div class="auth-card">
        <h1>OBSCURA</h1>
        <h2>Welcome back</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input class="form-control" id="username"
                   type="text" name="username" required>

            <label for="password">Password</label>
            <input class="form-control" id="password"
                   type="password" name="password" required>

            <button class="btn btn-accent" type="submit">
                Log in
            </button>
        </form>

        <p class="form-footer">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>
    </div>
</div>
</body>
</html>
