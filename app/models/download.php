<?php
// app/models/download.php

require_once dirname(dirname(__DIR__)) . '/core/database.php';

class Download {
    private $db;

    public function __construct() {
        global $pdo;
        if (isset($pdo) && $pdo instanceof PDO) {
            $this->db = $pdo;
        } else {
            // Fallback connection if global scope fails
            $this->db = new PDO("mysql:host=localhost;dbname=filetransfer;charset=utf8mb4", 'root', '', [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
    }

    // Fetch file record by ID
    public function getFileRecord(int $uploadId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM uploads WHERE Upload_ID = :id LIMIT 1");
            $stmt->execute([':id' => $uploadId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            return null;
        }
    }

    // Compare current file hash with database recorded hash
    public function verifyIntegrity(string $physicalPath, string $originalHash): array {
        if (!file_exists($physicalPath)) {
            return ['success' => false, 'message' => 'Fout: Bestand niet gevonden.'];
        }

        // Calculate current SHA-256 hash
        $currentHash = hash_file('sha256', $physicalPath);

        // Check for modification
        if ($currentHash !== $originalHash) {
            return [
                'success' => false,
                'message' => 'CRITICAL SECURITY ALERT: Integriteitscontrole mislukt! Het bestand is gewijzigd.'
            ];
        }

        return ['success' => true, 'message' => 'Integrity verified.'];
    }
}