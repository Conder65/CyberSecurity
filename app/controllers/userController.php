<?php
session_start();
require_once("../services/AuthService.php");
require_once("../../core/database.php");
$db = new Database();
$authService = new AuthService($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

        if ($authService->login($email, $password) == true) {
            echo("yes");
        } else {
            echo("no");
        }
} elseif ($action === 'register') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

        if ($authService->register($name, $email, $password) == true) {
            echo("yes");
        } else {
            echo("no");
        }
}
}
?>