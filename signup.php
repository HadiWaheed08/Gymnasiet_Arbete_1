<?php
require_once 'assets/functions.php';
session_start();
$error = false;
if (isset($_POST['submit'])) {
    if (
        isset($_POST['kund_id'])
        && isset($_POST['namn'])
        && isset($_POST['email'])
        && isset($_POST['password'])
    ) {

        $kund_id = filter_input(INPUT_POST, "kund_id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, "namn", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (addUser($_POST)) {
            $_SESSION['kund_id'] = $kund_id;
            $_SESSION['namn'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            header("Location: index.php");
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>

<body>
    <?php
    if ($error)
        echo "<p>Kontrollera dina uppgifter</p>";
    ?>
    <form method="post" action="">
        <label for="kund_id">Kund ID:</label>
        <input type="text" id="kund_id" name="kund_id" required>
        <br>
        <label for="namn">Namn:</label>
        <input type="text" id="namn" name="namn" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" name="submit" value="Login">
    </form>
    <p><a href="login.php">Har du redan ett konto?</a></p>

</body>

</html>