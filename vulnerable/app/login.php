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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Obscura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #10131c;
            color: white;
            max-width: 440px;
            margin: 80px auto;
            padding: 20px;
        }
        input, button {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
        }
        button {
            background: #536dfe;
            color: white;
            border: none;
            cursor: pointer;
        }
        a { color: #9baaff; }
        .error { color: #ff8585; }
    </style>
</head>
<body>
    <h1>OBSCURA</h1>
    <h2>Log in</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <button type="submit">Log in</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>
