<?php
require_once __DIR__ . '/../../app/models/bestand.php';
require_once __DIR__ . '/../../app/models/upload.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['bestand']) && $_FILES['bestand']['error'] === UPLOAD_ERR_OK) {
        $bestand = new Bestand($_FILES['bestand']);
        $upload = new Upload();
        $upload->saveBestand($bestand);
        header('Location: ../../index.php'); 
        exit;
}
}
?>