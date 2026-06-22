<?php
// app/models/upload.php

require_once dirname(dirname(__DIR__)) . '/core/database.php';

class Upload {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->createConnection();
    }

    public function saveBestand(Bestand $bestand, int $userId): array {
        // 1. Validate file type
        if (!$bestand->isValidZip()) {
            return ['success' => false, 'message' => 'Fout: Ongeldig bestandstype.'];
        }

        // 2. Compute SHA-256 hash from the temporary uploaded file
        $fileHash = hash_file('sha256', $bestand->getTmpName());
        if (!$fileHash) {
            return ['success' => false, 'message' => 'Fout: Hash berekening mislukt.'];
        }

        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 3. Use the Hash as the unique safe file name (No original name leakage)
        $safeUniqueName = $fileHash . '.zip';
        $destinationPath = $uploadDir . $safeUniqueName;

        try {
            if (move_uploaded_file($bestand->getTmpName(), $destinationPath)) {
                
                // 4. INSERT strictly into your 4 existing columns from the image
                $query = "INSERT INTO upload (User_ID, Title, Created_at) VALUES (:user_id, :title, NOW())";
                $stmt = $this->db->prepare($query);
                
                $success = $stmt->execute([
                    ':user_id' => $userId,
                    ':title'   => $safeUniqueName // Title now contains the hash implicitly!
                ]);

                if ($success) {
                    return ['success' => true, 'message' => 'Succes: Bestand veilig geüpload!'];
                }
            }
            return ['success' => false, 'message' => 'Fout: Kon bestand niet verplaatsen.'];

        } catch (PDOException $e) {
            if (file_exists($destinationPath)) { unlink($destinationPath); }
            return ['success' => false, 'message' => 'Database Fout: ' . $e->getMessage()];
        }
    }
}