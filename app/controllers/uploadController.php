<?php
// app/controllers/uploadController.php

// FIXED: Using absolute path routing via dirname to guarantee error-free inclusions
require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/services/JsonLogger.php';
require_once dirname(__DIR__) . '/models/bestand.php';
require_once dirname(__DIR__) . '/models/upload.php';

class UploadController {
    
    /**
     * Handles the incoming file upload request from the web page (View)
     */
    public function handleUpload(): void {
        // Day 3 - Step 1 Guard: Block any user who is not logged in
        AuthService::checkAccess();

        // Check if the file field exists in the POST request context
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bestand'])) {
            
            // 1. Initialize the Bestand model with the uploaded file data
            $bestand = new Bestand($_FILES['bestand']);
            
            // 2. Get the logged-in User_ID from the secure session
            $user_id = (int)($_SESSION['user_id'] ?? 0);

            // 3. Extract the optional comment/note to use it as the dynamic 'Title' field in the database
            $title = isset($_POST['opmerking']) ? trim($_POST['opmerking']) : '';
            if (empty($title)) {
                $title = $_FILES['bestand']['name']; // Fallback to original file name if empty
            }

            // 4. Initialize the Upload model to process the database insertion and file movement
            $uploadModel = new Upload();
            $result = $uploadModel->saveBestand($bestand, $user_id, $title);

            // 5. Return the outcome to the view (as a JSON response)
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            
            $logger = new JsonLogger();
            if ($result['success']) {
                $logger->log([
                    'event' => 'upload_success',
                    'success' => true,
                    'user_id' => $user_id,
                    'filename' => $title,
                ]);

                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message']
                ]);
            } else {
                $logger->log([
                    'event' => 'upload_failed',
                    'success' => false,
                    'user_id' => $user_id,
                    'filename' => $title,
                    'reason' => $result['message'],
                ]);

                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            }
            exit();
        } else {
            http_response_code(400);
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode(['status' => 'error', 'message' => 'Fout: Geen bestand ontvangen.']);
            exit();
        }
    }
}

// Instantiate and trigger the handler automatically when the controller script is invoked by HTML action
$controller = new UploadController();
$controller->handleUpload();