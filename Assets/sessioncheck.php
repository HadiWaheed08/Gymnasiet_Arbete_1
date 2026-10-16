<?php
// Initierar sessionhantering
session_start();
// Kontrollerar om sessionvariabel existerar 
if (!isset($_SESSION['username'])) {
    // Skicka en användare till startsidan
    header('Location: login.php');
    exit();
}
