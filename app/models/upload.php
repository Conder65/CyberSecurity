<?php
// app/models/upload.php

class Upload {
    // Directory where uploaded files will be securely stored
    private $target_dir = __DIR__ . "/../../public/uploads/"; 

    // Handles the file upload process and moves it to the target directory
    public function saveBestand(Bestand $bestand) {
        // Enforce security checks before saving (Day 2 - Step 4)
        if (!$bestand->validateType()) {
            return [
                'success' => false,
                'message' => 'Fout: Dit bestandstype is niet toegestaan!' // Error: File type not allowed
            ];
        }

        if (!$bestand->validateSize()) {
            return [
                'success' => false,
                'message' => 'Fout: Bestand is te groot! Maximaal 5MB.' // Error: File too large
            ];
        }

        // Define the final absolute path for the file
        $target_file = $this->target_dir . basename($bestand->getFilename());

        // Execute the physical file transfer
        if (move_uploaded_file($bestand->getTmpName(), $target_file)) {
            // PLACEHOLDER: Random secret code generation and DB insertion will be implemented here later.
            return [
                'success' => true,
                'message' => 'Succes: Bestand is veilig geüpload!',
                'secret_code' => 'XYZ123' // Temporary placeholder token for frontend testing
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Fout: Er is iets misgegaan tijdens het uploaden.'
            ];
        }
    }
}
?>