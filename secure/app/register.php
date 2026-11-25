<?php
require_once __DIR__ . '/includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (strlen($username) > 100) {
        $error = 'Username is too long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, email, password_hash, role)
                 VALUES (:username, :email, :password_hash, :role)'
            );

            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
                'role' => 'user'
            ]);

            $success = 'Account created successfully! You can now log in.';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Username or email already exists.';
            } else {
                error_log($e->getMessage());
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Obscura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/obscura.css" rel="stylesheet">
</head>
<body>
<div class="auth-layout">
    <div class="auth-card">
        <h1>OBSCURA</h1>
        <h2>Create an account</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input class="form-control" id="username"
                   type="text" name="username" required maxlength="100">

            <label for="email">Email</label>
            <input class="form-control" id="email"
                   type="email" name="email" required maxlength="255">

            <label for="password">Password</label>
            <input class="form-control" id="password"
                   type="password" name="password" required minlength="8">

            <label for="confirm_password">Confirm password</label>
            <input class="form-control" id="confirm_password"
                   type="password" name="confirm_password" required>

            <button class="btn btn-accent" type="submit">
                Create account
            </button>
        </form>

        <p class="form-footer">
            Already have an account?
            <a href="login.php">Log in</a>
        </p>
    </div>
</div>
</body>
</html>
