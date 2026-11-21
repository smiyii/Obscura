<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

// Intentionally vulnerable: no URL destination validation.
$url = $_GET['url'] ?? '';

if ($url === '') {
    exit('Provide a URL using ?url=');
}

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_FOLLOWLOCATION => true,
]);

$content = curl_exec($ch);

if ($content === false) {
    http_response_code(502);
    exit('Preview failed.');
}

curl_close($ch);

header('Content-Type: text/plain; charset=UTF-8');
echo $content;
