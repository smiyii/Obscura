<?php
// helpers.php - defines e(), which Andria's auth.php doesn't have
if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}