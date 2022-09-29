<?php

declare(strict_types=1);

// Config
$secret = 'supersecrettoken';
$basePath = '';
$dataDir = '../data/';

// Get request info
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$authHeader = '';
if (function_exists('getallheaders')) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
} else {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
}

// Normalize request URI, remove leading and trailing /, remove base path
$requestUri = trim($requestUri, '/');
$basePath = trim($basePath, '/');
if (! empty($basePath)) {
    $length = strlen($basePath);
    if (substr($requestUri, 0, $length) === $basePath) {
        $requestUri = trim(substr($requestUri, $length), '/');
    }
}

// Validate file name
if (! preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_-]*$/", $requestUri)) {
    http_response_code(400);
    echo 'Bad Request!' . "\n";
    exit;
}
$filename = $dataDir . $requestUri . '.bin';

if ($method === 'GET') {
    if (! file_exists($filename)) {
        http_response_code(404);
        echo 'Not found!' . "\n";
        exit;
    }
    $file = fopen($filename, 'rb');
    fpassthru($file);
    fclose($file);
    exit;
} elseif ($method === 'PUT') {
    $auth = explode(' ', $authHeader);
    if (count($auth) !== 2 || $auth[0] !== 'Token' || $auth[1] !== $secret) {
        http_response_code(403);
        echo 'Forbidden!' . "\n";
        exit;
    }
    
    $inStream = fopen('php://input', 'rb');
    $file = fopen($filename, 'wb');
    while ($data = fread($inStream, 1024)) {
        fwrite($file, $data);
    }
    fclose($file);
    fclose($inStream);

    http_response_code(201);
    echo 'Created!' . "\n";
    exit;
}

http_response_code(405);
echo 'Method Not Allowed!' . "\n";
exit;
