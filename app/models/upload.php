<?php
// app/models/upload.php

// FIXED: Go up two levels (out of models, out of app) to correctly reach the root core directory
require_once dirname(dirname(__DIR__)) . '/core/database.php';

class Upload {
    private $db;

    public function __construct() {
        // Access the project's active global PDO instance
        global $pdo;
        
        // Fail-safe redundancy mapping if global scope isolation happens
        if (isset($pdo) && $pdo instanceof PDO) {
            $this->db = $pdo;
        } else {
            try {
                $this->db = new PDO("mysql:host=localhost;dbname=filetransfer;charset=utf8mb4", 'root', '', [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die("Critical Database Connection failure inside Upload Model: " . $e->getMessage());
            }
        }
    }

    /**
     * Secures the file inside the server directory and logs attributes to the 'uploads' table
     */
    public function saveBestand(Bestand $bestand, int $userId, string $title): array {
        // 1. Validate if the binary payload is a safe ZIP structure
        if (!$bestand->isValidZip()) {
            return ['success' => false, 'message' => 'Fout: Alleen legitieme ZIP-bestanden zijn toegestaan.'];
        }

        // 2. Enforce File Size Restrictions (Maximum 5MB limit allocation from Bestand model)
        if ($bestand->getSize() > 5242880) { 
            return ['success' => false, 'message' => 'Fout: Bestand is te groot. Maximaal 5MB toegestaan.'];
        }

        // 3. Hardened Target Directory Layout Construction
        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create directory safely if missing
        }

        // 4. File Cryptography Renaming: Counteracts Path Traversal and Direct Web Shell Execution
        $safeUniqueName = bin2hex(random_bytes(16)) . '.zip';
        $destinationPath = $uploadDir . $safeUniqueName;

        try {
            // 5. Transfer storage file from system temporary cache to the secure physical path
            if (move_uploaded_file($bestand->getTmpName(), $destinationPath)) {
                
                // 6. DB Execution: Inject metadata inside the specific layout schema requested by your team
                $query = "INSERT INTO uploads (User_ID, Title, Created_at) VALUES (:user_id, :title, NOW())";
                $stmt = $this->db->prepare($query);
                
                $success = $stmt->execute([
                    ':user_id' => $userId,
                    ':title'   => htmlspecialchars($title, ENT_QUOTES, 'UTF-8') // Mitigate persistent Stored XSS
                ]);

                if ($success) {
                    return ['success' => true, 'message' => 'Succes: Bestand veilig geüpload en opgeslagen!'];
                }
            }
            return ['success' => false, 'message' => 'Fout: Kon het bestand niet verplaatsen naar de server map.'];

        } catch (PDOException $e) {
            // Clean up the physically uploaded file if the database entry fails (Rollback sanity)
            if (file_exists($destinationPath)) {
                unlink($destinationPath);
            }
            error_log("Database Storage Error inside Upload Flow: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database Fout: ' . $e->getMessage()];
        }
    }
}