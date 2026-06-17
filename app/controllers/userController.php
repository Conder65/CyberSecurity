<?php
require_once __DIR__ . '/../../app/models/users.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../app/services/AuthService.php';

$pdo      = Database::getInstance();
$users    = new Users($pdo);
$authService = new AuthService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $email    = $_POST['email'] ?? '';

        $result = $authService->register($username, $email, $password);
        echo $result['message'];

    } elseif ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $authService->login($username, $password);
        echo $result['message'];

        if ($result['success']) {
            header('Location: ../../index.php');
            exit();
        }
    }
}
?>