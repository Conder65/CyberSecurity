<?php
// app/models/download.php

class Download {
    private $target_dir = __DIR__ . "/../../storage/uploads/";

    // Verifies the token and prepares the file for transfer
    public function verifyAndDownload($secret_code) {
        // PLACEHOLDER: Simulating that code 'XYZ123' matches an existing file named 'document.pdf'.
        // This logic will query the Database via the secret code in the future.
        if ($secret_code === 'XYZ123') {
            $filename = 'document.pdf'; 
            $file_path = $this->target_dir . $filename;

            // Ensure the physical file exists on the server before initiating download (Day 2 - Step 3)
            if (file_exists($file_path)) {
                return [
                    'success' => true,
                    'file_path' => $file_path,
                    'filename' => $filename
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Fout: Bestand bestaat niet meer op de server.' // Error: File missing on disk
                ];
            }
        }

        // Triggered if the code does not exist or is invalid
        return [
            'success' => false,
            'message' => 'Fout: Ongeldige geheime code!' // Error: Invalid secret code
        ];
    }
}
?>