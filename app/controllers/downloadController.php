<?php
// app/controllers/downloadController.php

require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/models/download.php';

// Route Guard: Enforce authentication
AuthService::checkAccess();

if (isset($_GET['id'])) {
    $uploadId = (int)$_GET['id'];
    
    $downloadModel = new Download();
    $fileRecord = $downloadModel->getFileRecord($uploadId);

    if (!$fileRecord) {
        die("Fout: Record niet gevonden.");
    }

    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
    $physicalPath = $uploadDir . $fileRecord['Title']; 

    // Run integrity validation check
    $integrity = $downloadModel->verifyIntegrity($physicalPath, $fileRecord['FileHash']);

    if (!$integrity['success']) {
        http_response_code(400);
        die("<div style='color:red; font-weight:bold; padding:20px; border:2px solid red; background:#fdd;'>" . $integrity['message'] . "</div>");
    }

    // Stream the safe file to user
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