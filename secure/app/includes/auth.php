<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ];
}

function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Access denied.');
    }
}
