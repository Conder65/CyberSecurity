<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['bestand']) && $_FILES['bestand']['error'] === UPLOAD_ERR_OK) {
        //upload_err_ok betekent dat de file geen error heeft
        $fileTmpPath = $_FILES['bestand']['tmp_name'];
        $fileName = $_FILES['bestand']['name'];
        $fileType = $_FILES['bestand']['type'];
        
        //file naam maar beter
        $cleanFileName = basename($fileName);


        $targetDir = __DIR__ . '/../../public/uploads/';
        
        // Combine directory and filename
        $targetFilePath = $targetDir . $cleanFileName;
        
        //extension check voor safety zodat er geen php code wordt geexecute
        $fileExtension = pathinfo($cleanFileName, PATHINFO_EXTENSION);
        if (strtolower($fileExtension) === 'zip') {
            
           //move naar uploads
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                echo "File successfully uploaded to public/uploads/" . htmlspecialchars($cleanFileName);
                
            } else {
                echo "Error: There was an issue moving the uploaded file.";
            }
            
        } else {
            echo "Error: Only ZIP files are allowed.";
        }
        
    } else {
        echo "Error: No file uploaded or there was an upload error. Code: " . $_FILES['bestand']['error'];
    }
}