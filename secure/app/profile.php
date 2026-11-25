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

                // Refresh the session role after updating the database.
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
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Obscura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/obscura.css" rel="stylesheet">
</head>
<body>
<div class="auth-layout">
    <div class="auth-card profile-card">
        <h1>OBSCURA</h1>
        <h2>My Profile</h2>

        <p>
            <a href="dashboard.php">&larr; Back to Dashboard</a>
        </p>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <p>
            Current role:
            <strong>
                <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </p>

        <form method="POST">
            <label for="username">Username</label>
            <input class="form-control"
                   id="username"
                   name="username"
                   value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>"
                   required maxlength="100">

            <label for="email">Email</label>
            <input class="form-control"
                   id="email"
                   type="email"
                   name="email"
                   value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
                   required maxlength="255">

            <button class="btn btn-accent" type="submit">
                Save Changes
            </button>
        </form>
    </div>
</div>
</body>
</html>
