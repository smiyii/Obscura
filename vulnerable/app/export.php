<?php
require_once __DIR__ . '/includes/auth.php';

require_login();

// Intentionally vulnerable: user-controlled file path.
$filename = $_GET['file'] ?? 'welcome.txt';

$exportDirectory = __DIR__ . '/exports/';
$filePath = $exportDirectory . $filename;

if (!is_file($filePath)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="export.txt"');

readfile($filePath);
exit;
