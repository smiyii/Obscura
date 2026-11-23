<?php
require_once __DIR__ . '/includes/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? null;
    if (!isset($_SESSION['logout_token']) || !is_string($token)
        || !hash_equals($_SESSION['logout_token'], $token)) {
        http_response_code(403);
        exit('Invalid logout request.');
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $cookie['path'],
            $cookie['domain'], $cookie['secure'], $cookie['httponly']);
    }
    session_destroy();
    header('Location: /login.php', true, 303);
    exit;
}

$_SESSION['logout_token'] ??= bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log out | Obscura</title>
    <style>
        body { background: #10131c; color: white; font: 1rem Arial, sans-serif; max-width: 440px; margin: 5rem auto; padding: 1rem; }
        button { background: #536dfe; color: white; border: 0; border-radius: 6px; padding: 12px 24px; cursor: pointer; }
        a { color: #9baaff; margin-left: 1rem; }
    </style>
</head>
<body>
    <main>
        <h1>Log out of Obscura?</h1>
        <form method="post">
            <input type="hidden" name="token" value="<?= $_SESSION['logout_token'] ?>">
            <button type="submit">Log out</button>
            <a href="/profile.php">Back to profile</a>
        </form>
    </main>
</body>
</html>
