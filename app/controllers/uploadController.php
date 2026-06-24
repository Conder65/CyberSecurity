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
    
    // Set headers early to ensure standard JSON response for any output
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }

    $fileTmpPath = $_FILES['bestand']['tmp_name'];
    $fileName    = $_FILES['bestand']['name'];

    // Validate extension (Must be .zip)
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileExtension !== 'zip') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Fout: Alleen .zip bestanden zijn toegestaan.']);
        exit();
    }

    // Validate strict MIME-type to prevent executable file masks
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fileTmpPath);
    
    $allowedMimeTypes = [
        'application/zip', 
        'application/x-zip-compressed', 
        'multipart/x-zip', 
        'application/x-compress'
    ];

    if (!in_array($mimeType, $allowedMimeTypes)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Beveiligingsfout: Ongeldig bestandstype gedetecteerd.']);
        exit();
    }

    // Capture receiver email from POST request
    $receiverEmail = isset($_POST['receiver_email']) ? trim($_POST['receiver_email']) : '';
    if (empty($receiverEmail)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Fout: Geen ontvanger email opgegeven.']);
        exit();
    }

    // Instantiate and pass all 3 required arguments to saveBestand
    $bestand = new Bestand($_FILES['bestand']);
    $user_id = (int)$_SESSION['user_id'];

    $uploadModel = new Upload();
    $result = $uploadModel->saveBestand($bestand, $user_id, $receiverEmail);

    // Return response based on business logic result
    if ($result['success']) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => $result['message']]);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
    exit();
} else {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Fout: Geen bestand ontvangen.']);
    exit();
}