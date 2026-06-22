<?php
require_once dirname(dirname(__DIR__)) . '/core/database.php';

class Upload {
private $db;

public function __construct() {
// FIXED: Using your exact Database class instantiation from the frontend
$database = new Database();
$this->db = $database->createConnection();
}

/**
* Secures the file, generates SHA-256 hash, and saves metadata to DB
*/
public function saveBestand(Bestand $bestand, int $userId): array {
// 1. CyberSecurity Check: Validate extension and real MIME-type
if (!$bestand->isValidZip()) {
return ['success' => false, 'message' => 'Fout: Ongeldig bestandstype of grootte.'];
}

// 2. INTEGRITY LAYER: Compute SHA-256 hash before moving the file
$fileHash = hash_file('sha256', $bestand->getTmpName());
if (!$fileHash) {
return ['success' => false, 'message' => 'Fout: Hash berekening mislukt.'];
}

$uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
if (!is_dir($uploadDir)) {
mkdir($uploadDir, 0755, true);
}

// 3. Obfuscate file name to prevent direct execution / path traversal
$safeUniqueName = bin2hex(random_bytes(16)) . '.zip';
$destinationPath = $uploadDir . $safeUniqueName;

try {
if (move_uploaded_file($bestand->getTmpName(), $destinationPath)) {

// 4. DB Insert matching your schema: User_ID, Title (as dynamic name), FileHash, Created_at
$query = "INSERT INTO upload (Upload_ID, User_ID, Title, Created_at) VALUES (:user_id, :title, :hash, NOW())";
$stmt = $this->db->prepare($query);

$success = $stmt->execute([
':user_id' => $userId,
':title' => htmlspecialchars($safeUniqueName, ENT_QUOTES, 'UTF-8'), // Save secure name
':hash' => $fileHash
]);

if ($success) {
return ['success' => true, 'message' => 'Succes: Bestand veilig geüpload met SHA-256 hash!'];
}
}
return ['success' => false, 'message' => 'Fout: Kon bestand niet verplaatsen.'];

} catch (PDOException $e) {
if (file_exists($destinationPath)) { unlink($destinationPath); }
return ['success' => false, 'message' => 'Database Fout: ' . $e->getMessage()];
}
}
}