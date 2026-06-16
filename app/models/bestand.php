<?php
// app/models/bestand.php

class Bestand {
    // Core file properties
    private $filename;
    private $filesize;
    private $filetype;
    private $tmp_name;
    
    // Security constraints (Day 2 - Step 4: Basisbeveiliging)
    private $allowed_types = [
        'application/pdf', 
        'image/jpeg', 
        'image/png', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ]; // Allowed: PDF, JPG, PNG, DOCX
    
    private $max_size = 5242880; // Maximum file size limit: 5MB (in bytes)

    public function __construct($file_data = null) {
        if ($file_data) {
            $this->filename = $file_data['name'];
            $this->filesize = $file_data['size'];
            $this->filetype = $file_data['type'];
            $this->tmp_name = $file_data['tmp_name'];
        }
    }

    // Validates the file type to prevent malicious uploads (e.g., .php, .exe)
    public function validateType() {
        return in_array($this->filetype, $this->allowed_types);
    }

    // Validates the file size against the defined maximum limit
    public function validateSize() {
        return $this->filesize <= $this->max_size;
    }

    // Getters for controller access
    public function getFilename() {
        return $this->filename;
    }

    public function getTmpName() {
        return $this->tmp_name;
    }
}
?>