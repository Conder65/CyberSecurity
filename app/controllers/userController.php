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

        if ($authService->login($email, $password) === true) {
            header("Location: ../../dashboard.php");
            exit;
        } else {
            // 🌟 Set secure generic error message for login failure
            $_SESSION['login_error'] = "Onjuiste combinatie van e-mailadres en wachtwoord.";
            header("Location: ../../authPage.php");
            exit;
        }

    } elseif ($action === 'register') {
        $name     = $_POST['name'];
        $email    = $_POST['email'];
        $password = $_POST['password'];

        if ($authService->register($name, $email, $password) === true) {
            $_SESSION['register_success'] = "Registratie succesvol! U kunt nu inloggen.";
            header("Location: ../../authPage.php");
            exit;
        } else {
            // 🌟 Set error message (Either weak password from regex or email already exists)
            $_SESSION['register_error'] = "Registratie mislukt. Wachtwoord voldoet niet aan de eisen of e-mail is al in gebruik.";
            header("Location: ../../authPage.php");
            exit;
        }
    }
}
?>