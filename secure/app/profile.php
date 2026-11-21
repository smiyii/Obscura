<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$userId = (int) $_SESSION['user_id'];
$message = '';
$error = '';

$stmt = $pdo->prepare(
    'SELECT id, username, email, role FROM users WHERE id = ?'
);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('User not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($username === '' || strlen($username) > 100) {
        $error = 'Invalid username.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $error = 'Invalid email.';
    } else {
        // SECURITY FIX: Only explicitly permitted profile fields can be updated.
// The user's role must never be controlled by client-submitted data.
        $allowedColumns = ['username', 'email'];
        $updates = [];
        $params = [];

        foreach ($allowedColumns as $column) {
            if (isset($_POST[$column]) && is_string($_POST[$column])) {
                $updates[] = "`$column` = ?";
                $params[] = $_POST[$column];
            }
        }

        if ($updates) {
            $params[] = $userId;

            try {
                $sql = 'UPDATE users SET ' .
                    implode(', ', $updates) .
                    ' WHERE id = ?';

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Refresh session role after the database update xD
                $stmt = $pdo->prepare(
                    'SELECT id, username, email, role FROM users WHERE id = ?'
                );
                $stmt->execute([$userId]);
                $user = $stmt->fetch();

                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $message = 'Profile updated successfully.';
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'Profile update failed.';
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
    <title>Profile | Obscura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #10131c;
            color: white;
            max-width: 500px;
            margin: 70px auto;
            padding: 20px;
        }
        input, button {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px;
            border-radius: 6px;
        }
        button {
            background: #536dfe;
            color: white;
            border: none;
            cursor: pointer;
        }
        a { color: #9baaff; }
        .success { color: #81d9a0; }
        .error { color: #ff8585; }
    </style>
</head>
<body>
    <h1>OBSCURA</h1>
    <h2>My Profile</h2>

    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <p>Current role:
        <strong><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></strong>
    </p>

    <form method="POST">
        <label for="username">Username</label>
        <input
            id="username"
            name="username"
            value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="email">Email</label>
        <input
            id="email"
            type="email"
            name="email"
            value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
