<?php
// app/controllers/downloadController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/models/download.php';

// Route Guard: Restrict access to logged in users only
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die("Toegang geweigerd. Log eerst in.");
}

if (isset($_GET['id'])) {
    $uploadId = (int)$_GET['id'];
    
    $downloadModel = new Download();
    $fileRecord = $downloadModel->getFileRecord($uploadId);

    if (!$fileRecord) {
        die("Fout: Record niet gevonden.");
    }

    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
    $physicalPath = $uploadDir . $fileRecord['Title']; 

    // Step 3 Integrity Check: Recalculate and match SHA-256 signatures
    $integrity = $downloadModel->verifyIntegrity($physicalPath, $fileRecord['Title']);

    if (!$integrity['success']) {
        http_response_code(400);
        // Terminate execution and trigger the required security alert block
        die("<div style='color:red; font-weight:bold; padding:20px; border:2px solid red; background:#fdd;'>" . $integrity['message'] . "</div>");
    }

    // Stream verified safe binary payload to the client browser
    if (file_exists($physicalPath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileRecord['Title']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($physicalPath));
        
        readfile($physicalPath);
        exit();
    }
} else {
    http_response_code(400);
    echo "Bad Request: Missing file ID.";
}