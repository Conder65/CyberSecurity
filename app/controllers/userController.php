<?php
require_once __DIR__ . '/../../app/models/users.php';
require_once __DIR__ . '/../../core/database.php';
$db = new Database();
$users = new Users($db);
$users->getUserById($_GET['id']);

?>