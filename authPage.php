<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
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
    <div class="gatekeeper-container">
        <header class="brand-header">
            <h1>FileShare</h1>
            <p>Please login or create an account to start uploading.</p>
        </header>

        <div class="auth-sections">
            <section class="auth-card login-section">
                <h2>Login</h2>
                <p>Access your dashboard and managed files.</p>

                <?php if (isset($_SESSION['login_error'])): ?>
                <div
                    style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; text-align: left;">
                    <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
                </div>
                <?php endif; ?>

                <form action="app/controllers/userController.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="input-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="email" required placeholder="you@example.com">
                    </div>
                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary">Log In</button>
                </form>
            </section>

            <section class="auth-card register-section">
                <h2>Create Account</h2>
                <p>Get your free storage space instantly.</p>

                <?php if (isset($_SESSION['register_error'])): ?>
                <div
                    style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; text-align: left;">
                    <?php echo htmlspecialchars($_SESSION['register_error']); unset($_SESSION['register_error']); ?>
                </div>
                <?php elseif (isset($_SESSION['register_success'])): ?>
                <div
                    style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; text-align: left;">
                    <?php echo htmlspecialchars($_SESSION['register_success']); unset($_SESSION['register_success']); ?>
                </div>
                <?php endif; ?>

                <form action="app/controllers/userController.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="input-group">
                        <label for="reg-name">Full Name</label>
                        <input type="text" id="reg-name" name="name" required placeholder="John Doe">
                    </div>
                    <div class="input-group">
                        <label for="reg-email">Email Address</label>
                        <input type="email" id="reg-email" name="email" required placeholder="you@example.com">
                    </div>
                    <div class="input-group">
                        <label for="reg-password">Password</label>
                        <input type="password" id="reg-password" name="password" required
                            placeholder="Min. 8 characters">
                    </div>
                    <button type="submit" class="btn btn-secondary">Sign Up</button>
                </form>
            </section>
        </div>
    </div>
</body>

</html>