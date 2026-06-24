<?php
require_once("core/database.php");
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$pdo = $db->createConnection();

$stmt = $pdo->prepare("SELECT Email FROM users WHERE User_ID = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestand uploaden</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/script.js" defer></script>
</head>

<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="brand">FileSave <span>Dashboard</span></div>
            <div class="user-menu">
                <span class="user-email"><?php echo htmlspecialchars($user_data['Email']); ?></span>
                <a href="app/services/logout.php" class="logout-link">Log Out</a>
            </div>
        </header>

        <main class="workspace">
            <?php
            // STEP 4: Fetch only files where the logged-in user is the intended receiver
            $file_stmt = $pdo->prepare("SELECT u.Upload_ID, u.Title, u.Created_at 
                                        FROM upload u 
                                        WHERE u.Receiver_ID = :user_id 
                                        ORDER BY u.Created_at DESC");
            $file_stmt->execute(['user_id' => $_SESSION['user_id']]);
            $user_files = $file_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <section class="panel download-panel">
                <h2>Your Received Files</h2>
                <p class="panel-subtitle">Manage files received from other users and download them securely.</p>

                <div class="file-list-container">
                    <table class="file-table">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Size</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($user_files)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #888;">No files received yet.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($user_files as $file): ?>
                            <?php
                            $filePath = dirname(__DIR__) . '/public/uploads/' . $file['Title'];
                            $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) . ' KB' : 'Unknown';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($file['Title']); ?></td>
                                <td><?php echo $fileSize; ?></td>
                                <td>
                                    <a href="app/controllers/downloadController.php?id=<?php echo $file['Upload_ID']; ?>"
                                        class="download-btn-link"
                                        style="color: #007bff; text-decoration: none; font-weight: bold;">Download</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel upload-panel">
                <h2>Upload & Send New Files</h2>
                <p class="panel-subtitle">Enter receiver username and choose a zip file to send.</p>
                <form action="app/controllers/uploadController.php" method="POST" enctype="multipart/form-data"
                    class="upload-form">

                    <div style="margin-bottom: 20px; text-align: left;">
                        <label for="receiver_email"
                            style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Send to
                            (Email):</label>
                        <input type="email" id="receiver_email" name="receiver_email" required
                            placeholder="Enter receiver email..."
                            style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px;">
                    </div>

                    <div class="drop-zone">
                        <div id="preview-container"></div>
                        <span class="drop-zone-text">Drag & Drop files here</span>
                        <span class="drop-zone-or">or</span>
                        <label for="file-input" class="file-input-label">Browse Files</label>
                        <input type="file" id="file-input" name="bestand" accept=".zip" required style="display: none;">
                    </div>
                    <button type="submit" class="upload-btn">Upload & Send</button>
                </form>
            </section>

        </main>
    </div>
</body>

</html>