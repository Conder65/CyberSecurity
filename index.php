<?php
session_start();
$isAuthenticated = isset($_SESSION['user']);
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
  <script>
    window.APP_AUTHENTICATED = <?php echo $isAuthenticated ? 'true' : 'false'; ?>;
  </script>


  <main class="login_homepage">
    <h1 id="intro"> Welkom op ons bestanden verwissel platform! </h1>

    <div class="auth-buttons">
      <button class="button" id="register-button" type="button">Registreren</button>
      <button class="button" id="login-button" type="button">Inloggen</button>
    </div>
    <div id="register-form" class="hidden">
        <form action="app/controllers/registerController.php" method="post">

            <label for="new-username">Gebruikersnaam:</label>
            <input type="text" id="new-username" name="username" required>

            <label for="new-password">Wachtwoord:</label>
            <input type="password" id="new-password" name="password" required>

            <label for="confirm-password">Bevestig wachtwoord:</label>
            <input type="password" id="confirm-password" name="confirm_password" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <p>Upload of download:</p>
            <input type="radio" id="upload" name="role" value="upload" required>
            <label for="upload">Uploaden</label>
            <input type="radio" id="download" name="role" value="download" required>
            <label for="download">Downloaden</label>

            <button type="submit" class="button" style="width: 100%;">Registreren</button>
        </form>
    </div>

    <div id="login-form" class="hidden">

            <form action="app/controllers/loginController.php" method="post">
                <label for="username">Gebruikersnaam:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Inloggen</button>
            </form>
</main>
  <main class="page">
    <section id="upload-section" class="card" aria-labelledby="page-title">

      <header class="card__header">
        <h1 id="page-title">Bestand uploaden</h1>
        <p class="muted">Kies een bestand en voeg eventueel een korte opmerking toe.</p>
      </header>

      <form class="form" action="app/controllers/uploadController.php" method="post" enctype="multipart/form-data">
        <div class="field">
          <label for="bestand">Kies een bestand</label>
          <input type="file" id="bestand" name="bestand" accept=".zip,application/zip" required>
        </div>

        <div class="field">
          <label for="opmerking">Opmerking <span class="muted">(optioneel)</span></label>
          <textarea name="opmerking" id="opmerking" rows="3" placeholder="Bijv. wat dit bestand bevat…"></textarea>
        </div>

        <div class="actions">
          <button class="button" type="submit">Uploaden</button>
        </div>
      </form>
    </section>
  </main>
</body>

</html>