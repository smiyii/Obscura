<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

// SECURITY FIX: Only allow HTTP(S) URLs with public IP destinations.
$url = $_GET['url'] ?? '';

if (!is_string($url) || $url === '' || strlen($url) > 2048) {
    http_response_code(400);
    exit('Invalid URL.');
}

$parts = parse_url($url);

if (
    $parts === false ||
    !isset($parts['scheme'], $parts['host']) ||
    !in_array(strtolower($parts['scheme']), ['http', 'https'], true) ||
    isset($parts['user']) ||
    isset($parts['pass']) ||
    isset($parts['fragment'])
) {
    http_response_code(400);
    exit('Invalid URL.');
}

$host = $parts['host'];
$port = $parts['port'] ?? (
    strtolower($parts['scheme']) === 'https' ? 443 : 80
);

if (!in_array($port, [80, 443], true)) {
    http_response_code(400);
    exit('Port not allowed.');
}

// Resolve the destination and reject non-public IP addresses.
$addresses = [];

if (filter_var($host, FILTER_VALIDATE_IP)) {
    $addresses[] = $host;
} else {
    $records = dns_get_record($host, DNS_A + DNS_AAAA);

    foreach ($records ?: [] as $record) {
        if (isset($record['ip'])) {
            $addresses[] = $record['ip'];
        }

        if (isset($record['ipv6'])) {
            $addresses[] = $record['ipv6'];
        }
    }
}

if (!$addresses) {
    http_response_code(400);
    exit('Host could not be resolved.');
}

foreach ($addresses as $address) {
    if (!filter_var(
        $address,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    )) {
        http_response_code(403);
        exit('Internal destinations are not allowed.');
    }
}

// Pin cURL to the validated IP to reduce DNS rebinding risk.
$ip = $addresses[0];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
    CURLOPT_RESOLVE => [
        $host . ':' . $port . ':' . $ip
    ],
]);

$content = curl_exec($ch);

if ($content === false) {
    curl_close($ch);
    http_response_code(502);
    exit('Preview failed.');
}

curl_close($ch);

header('Content-Type: text/plain; charset=UTF-8');
echo $content;
