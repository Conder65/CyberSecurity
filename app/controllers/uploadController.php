<?php
// app/controllers/uploadController.php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../models/bestand.php';
require_once __DIR__ . '/../models/upload.php';

class UploadController {
    
    /**
     * Handles the incoming file upload request from the web page (View)
     */
    public function handleUpload(): void {
        // Day 3 - Step 1 Guard: Block any user who is not logged in
        AuthService::checkAccess();

        // Check if the request method is POST and a file is actually sent
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploaded_file'])) {
            
            // 1. Initialize the Bestand model with the uploaded file data
            $bestand = new Bestand($_FILES['uploaded_file']);
            
            // 2. Get the logged-in User_ID from the secure session
            $user_id = (int)$_SESSION['user_id'];

            // 3. Initialize the Upload model to process the database insertion and file movement
            $uploadModel = new Upload();
            $result = $uploadModel->saveBestand($bestand, $user_id);

            // 4. Return the outcome to the view (as a JSON response for frontend or redirect)
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            
            if ($result['success']) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            }
            exit();
        } else {
            // Bad request handling if accessed directly without a file
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Fout: Geen bestand ontvangen.']);
            exit();
        }
    }
}