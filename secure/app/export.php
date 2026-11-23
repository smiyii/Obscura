<?php
require_once __DIR__ . '/includes/auth.php';

require_login();

// SECURITY FIX: Accept only simple .txt filenames.
$filename = $_GET['file'] ?? 'welcome.txt';

if (!is_string($filename) ||
    !preg_match('/\A[a-zA-Z0-9_-]+\.txt\z/D', $filename)) {
    http_response_code(400);
    exit('Invalid filename.');
}

$exportDirectory = realpath(__DIR__ . '/exports');

if ($exportDirectory === false) {
    http_response_code(500);
    exit('Export directory unavailable.');
}

$filePath = realpath($exportDirectory . DIRECTORY_SEPARATOR . $filename);

// SECURITY FIX: Resolve the path and ensure it remains inside exports/.
if ($filePath === false ||
    dirname($filePath) !== $exportDirectory ||
    !is_file($filePath)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

readfile($filePath);
exit;
