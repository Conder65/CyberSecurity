<?php
// Automatically include your existing PDO configuration
require_once __DIR__ . '/../core/database.php';

class AuthService {
    private $db;

    public function __construct() {
        // Use the global $pdo instance created in your database.php file
        global $pdo;
        $this->db = $pdo;

        // Step 1: Start secure session if not already active
        if (session_status() == PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 900, // 15 minutes timeout
                'cookie_httponly' => true, // Mitigation against XSS (cannot be read via JavaScript)
                'cookie_secure' => false,  // Set to true if your development environment uses HTTPS
                'use_strict_mode' => true
            ]);
        }
    }
}