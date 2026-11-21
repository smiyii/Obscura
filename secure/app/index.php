<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Obscura</title>
    <style>
        body {
            background: #101321;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 120px;
        }
        h1 { color: #a99cff; }
    </style>
</head>
<body>
    <h1>OBSCURA</h1>
    <h2>AI Notes Security Lab</h2>
    <p>PHP + MySQL + Docker</p>
    <p>Database connected successfully!</p>
    <p>Registered users: <?= (int) $userCount ?></p>
</body>
</html>