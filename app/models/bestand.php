<?php
// app/models/bestand.php

class Bestand {
    // Core file properties
    private $filename;
    private $filesize;
    private $filetype;
    private $tmp_name;
    private $error;
    
    // Security constraints (Day 2 - Step 4: Basisbeveiliging)
    private $allowed_types = [
        'application/pdf', 
        'image/jpeg', 
        'image/png', 
        'application/zip',
        'application/x-zip-compressed',
        'application/x-zip',
        'application/x-rar-compressed',
        'application/x-rar',
        'application/x-7z-compressed',
        'application/x-7z',
        'application/x-tar',
        'application/x-tar-compressed',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ]; // Allowed: PDF, JPG, PNG, ZIP, RAR, DOCX
    
    private $max_size = 5242880; // Maximum file size limit: 5MB (in bytes)

    public function __construct($file_data = null) {
        if ($file_data) {
            $this->filename = $file_data['name'] ?? '';
            $this->filesize = $file_data['size'] ?? 0;
            $this->filetype = $file_data['type'] ?? '';
            $this->tmp_name = $file_data['tmp_name'] ?? '';
            $this->error    = $file_data['error'] ?? UPLOAD_ERR_NO_FILE;
        }
    }

    /**
     * Advanced Cybersecurity Validation: Inspects the real binary structure (Magic Bytes)
     * instead of trusting the user-tamperable frontend Content-Type.
     */
    public function isValidZip(): bool {
        // 1. Check if the PHP upload process itself encountered any system errors
        if ($this->error !== UPLOAD_ERR_OK) {
            return false;
        }

        // 2. Validate the file size against the defined maximum 5MB limit
        if ($this->filesize > $this->max_size) {
            return false;
        }

        // 3. Prevent Extension Bypass (Double extensions like file.php.zip)
        $extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'zip', 'rar', '7z', 'tar', 'docx'], true)) {
            return false;
        }

        // 4. Server-Side Deep Content Inspection (MIME-Type validation using finfo)
        if (file_exists($this->tmp_name)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $realMimeType = $finfo->file($this->tmp_name);

            // Verify if the real inspected MIME-type exists in our whitelist array
            return in_array($realMimeType, $this->allowed_types, true);
        }

        return false;
    }

    // Getters for controller and processor model access
    public function getName(): string {
        return $this->filename;
    }

    public function getTmpName(): string {
        return $this->tmp_name;
    }

    public function getSize(): int {
        return $this->filesize;
    }

    public function getError(): int {
        return $this->error;
    }
}
?>