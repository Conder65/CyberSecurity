<?php
class AuthService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db->createConnection();
    }

    public function login(string $email, string $password): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['user_name'] = $user['Name'];
            return true;
        }
        return false;
    }

    public function register(string $name, string $email, string $password): bool
    {
        
        // Requires: Minimum 8 characters, at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.
        $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        if (!preg_match($password_regex, $password)) {
            return false; // Password fails security requirements
        }

        try {
            $stmt = $this->db->prepare("SELECT User_ID FROM users WHERE Email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                return false; // Email already taken
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("INSERT INTO users (Name, Email, Password, Role) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$name, $email, $hashedPassword, 0]);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /");
        exit;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
}
?>