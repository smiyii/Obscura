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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Obscura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #10131c;
            color: #f5f5f5;
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
        .success { color: #81d9a0; }
    </style>
</head>
<body>
    <h1>OBSCURA</h1>
    <h2>Create an account</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" required maxlength="100">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required maxlength="255">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required minlength="8">

        <label for="confirm_password">Confirm password</label>
        <input id="confirm_password" type="password" name="confirm_password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>
