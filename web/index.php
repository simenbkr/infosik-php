<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');


/*
if (! extension_loaded('openssl')) {
    die('You must enable the openssl extension.');
}
*/


setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');
ini_set('memory_limit', '256M');

ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie.lifetime', 0);
ini_set('session.use_strict_mode',1);
ini_set('session.sid_length',48);
ini_set('session.hash_function', "sha256");

ini_set('session.gc_maxlifetime', 36000000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);
session_save_path(__DIR__ . '/../sessions');

session_cache_limiter(false);
session_start();

# Generating CSRF token
$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));


if (preg_match('/\.(?:png|jpg|jpeg|gif|txt|css|js)$/', filter_var($_SERVER["REQUEST_URI"], FILTER_VALIDATE_URL)))
    return false; // serve the requested resource as-is.
else {
    $app = require __DIR__ . '/../src/app.php';
    $app->run();
}

/*

TODO:
- Egen mappe for sessions
- Cookie-flagg


*/