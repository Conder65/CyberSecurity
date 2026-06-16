<?php
declare(strict_types=1);

// Central PDO bootstrap.
// IMPORTANT: no output (no echo) so JSON responses aren't corrupted.

$dsn = "mysql:host=localhost;dbname=filetransfer;charset=utf8mb4";
$user = "root";
$pass = "";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Fail safely without echoing DB errors to the client.
    http_response_code(500);
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

?>