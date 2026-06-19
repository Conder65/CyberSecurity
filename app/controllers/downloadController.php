<?php
// app/controllers/downloadController.php

require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/services/JsonLogger.php';
require_once dirname(__DIR__) . '/models/download.php';

// Route Guard: Enforce authentication
AuthService::checkAccess();

$logger = new JsonLogger();

if (isset($_GET['id'])) {
    $uploadId = (int)$_GET['id'];
    
    $downloadModel = new Download();
    $fileRecord = $downloadModel->getFileRecord($uploadId);

    if (!$fileRecord) {
        $logger->log([
            'event' => 'download_record_not_found',
            'success' => false,
            'user_id' => (int)($_SESSION['user_id'] ?? 0),
            'upload_id' => $uploadId,
            'reason' => 'record_not_found',
        ]);
        die("Fout: Record niet gevonden.");
    }

    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
    $physicalPath = $uploadDir . $fileRecord['Title']; 

    // Run integrity validation check
    $integrity = $downloadModel->verifyIntegrity($physicalPath, $fileRecord['FileHash']);

    if (!$integrity['success']) {
        $logger->log([
            'event' => 'download_integrity_failed',
            'success' => false,
            'user_id' => (int)($_SESSION['user_id'] ?? 0),
            'upload_id' => $uploadId,
            'filename' => $fileRecord['Title'],
            'reason' => 'integrity_failed',
            'message' => $integrity['message'],
        ]);

        http_response_code(400);
        die("<div style='color:red; font-weight:bold; padding:20px; border:2px solid red; background:#fdd;'>" . $integrity['message'] . "</div>");
    }

    // Stream the safe file to user
    if (file_exists($physicalPath)) {
        $logger->log([
            'event' => 'download_success',
            'success' => true,
            'user_id' => (int)($_SESSION['user_id'] ?? 0),
            'upload_id' => $uploadId,
            'filename' => $fileRecord['Title'],
        ]);

        // Stream file


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

    // physical file missing (integrity passed but file not on disk)
    $logger->log([
        'event' => 'download_physical_missing',
        'success' => false,
        'user_id' => (int)($_SESSION['user_id'] ?? 0),
        'upload_id' => $uploadId,
        'filename' => $fileRecord['Title'],
        'reason' => 'file_not_on_disk',
    ]);

} else {
    http_response_code(400);
    echo "Bad Request: Missing file ID.";
}