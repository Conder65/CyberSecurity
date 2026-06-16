<?php

declare(strict_types=1);

class UploadController
{
    public function handle(): void
    {
        if (!isset($_FILES['bestand'])) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Missing file field: bestand']);
            return;
        }

        $file = $_FILES['bestand'];
        $opmerking = isset($_POST['opmerking']) ? trim((string)$_POST['opmerking']) : null;

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Upload error', 'code' => $file['error'] ?? null]);
            return;
        }

        $originalName = (string)($file['name'] ?? 'upload');
        $tmpPath = (string)($file['tmp_name'] ?? '');
        $fileSize = (int)($file['size'] ?? 0);
        $fileError = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

        // Hardening settings (align with app/models/bestand.php: max 5MB, allowed types)
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if ($fileError !== UPLOAD_ERR_OK) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Upload error', 'code' => $fileError]);
            return;
        }

        if ($fileSize <= 0 || $fileSize > $maxSize) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Fout: Bestand is te groot! Maximaal 5MB.']);
            return;
        }

        if (!is_uploaded_file($tmpPath)) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Fout: Onveilige upload (tmp)']);
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($tmpPath) ?: '';
        if (!in_array($detectedMime, $allowedMimeTypes, true)) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Fout: Dit bestandstype is niet toegestaan!', 'mime' => $detectedMime]);
            return;
        }

        // Map mime type -> extension (so we don't trust the original filename)
        $extByMime = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];
        $ext = $extByMime[$detectedMime] ?? 'bin';

        // Ensure unique server-side filename
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $serverToken = bin2hex(random_bytes(16));
        $serverFilename = $serverToken . '_' . ($safeOriginalName !== '' ? $safeOriginalName : 'upload') . '.' . $ext;

        // Store outside web root in production; for now keep existing uploads folder.
        $uploadsDir = __DIR__ . '/../../uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0775, true);
        }

        // Final absolute file path
        $targetPath = $uploadsDir . '/' . $serverFilename;

        if (!move_uploaded_file($tmpPath, $targetPath)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Fout: Er is iets misgegaan tijdens het uploaden.']);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'filename' => $originalName,
            'server_filename' => $serverFilename,
            'opmerking' => $opmerking,
            'saved_path' => $targetPath,
            'mime' => $detectedMime,
            'db' => 'Not implemented yet (no INSERT yet)'
        ]);

    }
}
?>
