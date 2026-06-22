<?php
// app/models/download.php

require_once dirname(dirname(__DIR__)) . '/core/database.php';

class Download {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->createConnection();
    }

    public function getFileRecord(int $uploadId) {
        try {
            // Select from your exact 4-column table
            $stmt = $this->db->prepare("SELECT * FROM upload WHERE Upload_ID = :id LIMIT 1");
            $stmt->execute([':id' => $uploadId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            return null;
        }
    }

    public function verifyIntegrity(string $physicalPath, string $titleName): array {
        if (!file_exists($physicalPath)) {
            return ['success' => false, 'message' => 'Fout: Bestand niet gevonden.'];
        }

        // Extract original hash from the filename (removing the .zip extension)
        $originalHash = pathinfo($titleName, PATHINFO_FILENAME);

        // Calculate current live physical file SHA-256 hash
        $currentHash = hash_file('sha256', $physicalPath);

        // Compare both signatures to ensure data integrity
        if ($currentHash !== $originalHash) {
            return [
                'success' => false,
                'message' => 'CRITICAL SECURITY ALERT: Integriteitscontrole mislukt! Het bestand is gewijzigd.'
            ];
        }

        return ['success' => true, 'message' => 'Integrity verified.'];
    }
}