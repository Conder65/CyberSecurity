<?php
declare(strict_types=1);

$action = $_GET['action'] ?? ($_POST['action'] ?? null);

// If the upload form was submitted, handle it.
if ($action === null) {
  $action = isset($_FILES['bestand']) ? 'upload' : 'home';
}

switch ($action) {
  case 'upload':
    require_once __DIR__ . '/app/controllers/uploadController.php';

    $controller = new UploadController();
    $controller->handle();
    break;

  default:
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Home. Submit the upload form from index.html.';
    break;
}

?>