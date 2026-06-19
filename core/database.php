<?php

class Database {
    private $host = "localhost";
    private $db_name = "filetransfer";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    private $pdo = null;

    /**
     * Creates and returns a PDO connection instance
     * @return PDO|null
     */
    public function createConnection() {
        // If a connection already exists, return it instead of creating a new one
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetches associative arrays by default
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Uses actual prepared statements
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            return $this->pdo;
        } catch (PDOException $e) {
            // In production, log this error instead of echoing it
            die("Connection failed: " . $e->getMessage());
        }
    }
}

// TUTORIAL
// $database = new Database();
// $pdo = $database->createConnection();
// $stmt = $pdo->query("SELECT * FROM users");
?>