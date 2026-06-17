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
    /**

     * SYSTEM 1: USER REGISTRATION 

     * Inserts a new user into your actual 'users' table securely.

     */

    public function register(string $name, string $email, string $password, int $role = 0): array {

        // 1. Basic validation

        if (empty($name) || empty($email) || empty($password)) {

            return ['success' => false, 'message' => 'Fout: Alle velden zijn verplicht!'];

        }



        // 2. Security Check: Enforce strong password policy (Day 3 Requirement)

        $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        if (!preg_match($password_regex, $password)) {

            return [

                'success' => false,

                'message' => 'Fout: Wachtwoord is te zwak! Minimaal 8 tekens, 1 hoofdletter, 1 cijfer en 1 speciaal teken.'

            ];

        }



        try {

            // 3. Security Check: Check if Email or Name already exists using your safe $pdo instance

            $check_query = "SELECT 1 FROM users WHERE Email = :email OR Name = :name LIMIT 1";

            $stmt = $this->db->prepare($check_query);

            $stmt->execute([':email' => $email, ':name' => $name]);

            

            if ($stmt->fetch()) {

                return ['success' => false, 'message' => 'Fout: Gebruikersnaam of e-mail bestaat al!'];

            }



            // 4. Cryptography: Securely hash the password using Bcrypt

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);



            // 5. Database Execution: Insert into your specific table structure

            $insert_query = "INSERT INTO users (Name, Password, Email, Role) VALUES (:name, :password, :email, :role)";

            $insert_stmt = $this->db->prepare($insert_query);

            

            $success = $insert_stmt->execute([

                ':name'     => $name,

                ':password' => $hashed_password,

                ':email'    => $email,

                ':role'     => $role // Default is 0 (regular user)

            ]);



            if ($success) {

                return ['success' => true, 'message' => 'Succes: Account succesvol aangemaakt!'];

            }



        } catch (PDOException $e) {

            error_log("Registration DB Error: " . $e->getMessage());

            return ['success' => false, 'message' => 'Fout: Er is een databasefout opgetreden.'];

        }



        return ['success' => false, 'message' => 'Fout: Registratie mislukt.'];

    }
    /**

     * SYSTEM 2: USER LOGIN

     * Authenticates a user against your actual 'users' table columns.

     */

    public function login(string $email_or_name, string $password): array {

        if (empty($email_or_name) || empty($password)) {

            return ['success' => false, 'message' => 'Fout: Vul alle velden in!'];

        }



        try {

            // Fetch user by either Name or Email using your global safe database connection

            $query = "SELECT * FROM users WHERE Email = :input OR Name = :input LIMIT 1";

            $stmt = $this->db->prepare($query);

            $stmt->execute([':input' => $email_or_name]);

            $user = $stmt->fetch();



            // Verify if user exists and if the Bcrypt hash matches the plain text password

            if ($user && password_verify($password, $user['Password'])) {

                // Set secure session variables to track user state

                $_SESSION['is_logged_in'] = true;

                $_SESSION['user_id']      = $user['User_ID'];

                $_SESSION['username']     = $user['Name'];

                $_SESSION['user_role']    = $user['Role'];

                $_SESSION['last_activity']= time();



                return ['success' => true, 'message' => 'Succes: Succesvol ingelogd!'];

            }



        } catch (PDOException $e) {

            error_log("Login DB Error: " . $e->getMessage());

        }



        // Generic error message for security (prevents enumeration attacks)

        return ['success' => false, 'message' => 'Fout: Ongeldige inloggegevens!'];

    }

    /**
     * ROUTE GUARD: Call this at the top of protected pages to block non-logged-in users
     */
    public static function checkAccess(): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            header('HTTP/1.1 403 Forbidden');
            echo "Toegang geweigerd. U moet eerst inloggen.";
            exit();
        }
    }
}