<?php
// app/models/upload.php

require_once dirname(dirname(__DIR__)) . '/core/database.php';

class Upload {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->createConnection();
    }

public function saveBestand(Bestand $bestand, int $userId, string $receiverEmail): array {
    // 1. Validate receiver email and fetch User_ID
    $stmtUser = $this->db->prepare("SELECT User_ID FROM users WHERE Email = :email LIMIT 1");
    $stmtUser->execute([':email' => $receiverEmail]);
    $receiver = $stmtUser->fetch();

    if (!$receiver) {
        return ['success' => false, 'message' => 'Fout: Ontvanger bestaat niet.'];
    }
    
    $receiverId = $receiver['User_ID'];

    // 2. Validate file type
    if (!$bestand->isValidZip()) {
        return ['success' => false, 'message' => 'Fout: Ongeldig bestandstype.'];
    }

    // 3. Compute SHA-256 hash for secure unique naming
    $fileHash = hash_file('sha256', $bestand->getTmpName());
    if (!$fileHash) {
        return ['success' => false, 'message' => 'Fout: Hash berekening mislukt.'];
    }

    $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeUniqueName = $fileHash . '.zip';
    $destinationPath = $uploadDir . $safeUniqueName;

    try {
        if (move_uploaded_file($bestand->getTmpName(), $destinationPath)) {
            
            // 4. Insert record with Receiver_ID linked to users table
            $query = "INSERT INTO upload (User_ID, Receiver_ID, Title, Created_at) VALUES (:user_id, :receiver_id, :title, NOW())";
            $stmt = $this->db->prepare($query);
            
            $success = $stmt->execute([
                ':user_id'     => $userId,
                ':receiver_id' => $receiverId,
                ':title'       => $safeUniqueName
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'Succes: Bestand veilig verzonden!'];
            }
        }
        return ['success' => false, 'message' => 'Fout: Kon bestand niet verplaatsen.'];

    } catch (PDOException $e) {
        if (file_exists($destinationPath)) { unlink($destinationPath); }
        return ['success' => false, 'message' => 'Database Fout: ' . $e->getMessage()];
    }
}
    
}