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
                <a href="index.html" class="logout-link">Log Out</a>
            </div>
        </header>

        <main class="workspace">

            <section class="panel download-panel">
                <h2>Your Files & Downloads</h2>
                <p class="panel-subtitle">Manage your uploaded files and generate download links.</p>

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
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel upload-panel">
                <h2>Upload New Files</h2>
                <p class="panel-subtitle">Drag and drop your files here or browse your device.</p>
                <form action="app/controllers/uploadController.php" method="POST" enctype="multipart/form-data"
                    class="upload-form">
                    <div class="drop-zone">
                        <div id="preview-container"></div>
                        <span class="drop-zone-text">Drag & Drop files here</span>
                        <span class="drop-zone-or">or</span>
                        <label for="file-input" class="file-input-label">Browse Files</label>
                        <input type="file" id="file-input" name="bestand" required style="display: none;">
                    </div>
                    <button type="submit" class="upload-btn">Upload</button>
                </form>
            </section>

        </main>
    </div>
</body>

</html>