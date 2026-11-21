<?php
header('Content-Type: text/plain; charset=UTF-8');

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Internal access only.');
}

echo 'OBSCURA_INTERNAL_SECRET=SSRF_SUCCESS';
