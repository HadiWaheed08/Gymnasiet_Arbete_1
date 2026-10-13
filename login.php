<?php
require_once 'assets/functions.php';
//Initierar sessionshantering
session_start();
//Kontrollerar om logga in-knappen har tryckts
if (isset($_POST['submit'])) {
    //Deklarerar en vektor för att spara felmeddelanden
    //Kontrollerar om fälten användarnamn och lösenord är toma
    $kund_id = $_POST['kund_id'];
    $password = $_POST['password'];
    $user = login($kund_id, $password);
    if ($user) {
        $_SESSION['kund_id'] = $user['kund_id'];
        $_SESSION['password'] = $password;
        $_SESSION['email'] = $user['email'];
        $_SESSION['namn'] = $user['namn'];
        header('Location: index.php');
        exit();
    } else {
        echo "Invalid ID or password";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <form method="post" action="">
        <label for="kund_id">Kund ID:</label>
        <input type="text" id="kund_id" name="kund_id" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" name="submit" value="Login">
    </form>
    <p><a href="signup.php">Saknar du konto?</a></p>
</body>

</html>