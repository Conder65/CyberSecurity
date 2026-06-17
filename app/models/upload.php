<?php
// app/models/upload.php

// Automatically include your existing PDO configuration
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/bestand.php';
class Upload {
    // Directory where uploaded files will be securely stored
   
    private $db;
    private $target_dir = __DIR__ . "/../../storage/uploads/"; 

    public function __construct() {
        // Use the global $pdo instance from your database.php
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Secures, validates, and saves the file metadata into the 'upload' table.
     * @param Bestand $bestand
     * @param int $user_id The ID of the currently logged-in user
     * @return array
     */
    public function saveBestand(Bestand $bestand, int $user_id): array {
        // 1. Security Check: Validate file type (Day 3 - Step 4)
        if (!$bestand->validateType()) {
            return [
                'success' => false,
                'message' => 'Fout: Dit bestandstype is niet toegestaan!'
            ];
        }

       // 2. Security Check: Validate file size (Day 3 - Step 4)
        if (!$bestand->validateSize()) {
            return [
                'success' => false,
                'message' => 'Fout: Bestand is te groot! Maximaal 5MB.'
            ];
        }

        // Sanitize the filename to prevent Directory Traversal attacks
        $safe_filename = basename($bestand->getFilename());
        $target_file = $this->target_dir . $safe_filename;

        try {
            // 3. Database Execution: Insert records into your actual 'upload' table
            $query = "INSERT INTO upload (User_ID, Title, Created_at) VALUES (:user_id, :title, NOW())";
            $stmt = $this->db->prepare($query);
            
            $db_success = $stmt->execute([
                ':user_id' => $user_id, // Tied to the logged-in user session
                ':title'   => $safe_filename
            ]);

            // 4. Physical File Transfer: Move file only if DB insertion succeeds
            if ($db_success && move_uploaded_file($bestand->getTmpName(), $target_file)) {
                return [
                    'success' => true,
                    'message' => 'Succes: Bestand is veilig opgeslagen in de database en opslag!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Fout: Er is iets misgegaan tijdens het verplaatsen van het bestand.'
                ];
            }

        } catch (PDOException $e) {
            // Log the error securely
            error_log("Upload DB Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Fout: Databasefout opgetreden tijdens het uploaden.'
            ];
        }
    }
}
?>