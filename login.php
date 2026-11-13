<?php
require_once 'Assets/functions.php';
//Initierar sessionshantering
session_start();
//Kontrollerar om logga in-knappen har tryckts
if (isset($_POST['submit'])) {
    //Deklarerar en vektor för att spara felmeddelanden
    //Kontrollerar om fälten användarnamn och lösenord är toma
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = getUser($username, $password);
    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['password'] = $password;
        header('Location: index.php');
        exit();
    } else {
        $error = "Fel användarnamn eller lösenord, inloggning misslyckades.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Login</title>
</head>

<body class="login-body">
    <div class="login-container">
        <h1>Kläduthyrning-backend</h1>
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <p class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>
        <form method="post" action="">

            <label for="username">Användarnamn:</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>

            <label for="password">Lösenord:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <input type="submit" name="submit" value="Login">
        </form>
    </div>
</body>

</html>