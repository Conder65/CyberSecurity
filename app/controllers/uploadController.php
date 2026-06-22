<?php
// app/controllers/uploadController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/models/bestand.php';
require_once dirname(__DIR__) . '/models/upload.php';

// Route Guard: Verify if session has user_id from login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Toegang geweigerd. Log eerst in.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bestand'])) {
    
    $bestand = new Bestand($_FILES['bestand']);
    $user_id = (int)$_SESSION['user_id'];

    $uploadModel = new Upload();
    $result = $uploadModel->saveBestand($bestand, $user_id);

    // Return response
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }

    if ($result['success']) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => $result['message']]);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
    exit();
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Fout: Geen bestand ontvangen.']);
    exit();
}