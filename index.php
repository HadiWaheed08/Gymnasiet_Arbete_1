<?php
require_once 'Assets/sessioncheck.php';
require_once 'Assets/functions.php';
require_once 'Assets/config/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Kläduthyrning</title>
</head>

<body>
    <header>
        <div>
            <nav>
                <ul>
                    <li><a href="kläder.php">Kläder</a></li>
                    <li><a href="uthyrda.php">Uthyrda-kläder</a></li>
                    <li><a href="inlämnade.php">Inlämnade-kläder</a></li>
                    <li><a href="kund.php">Alla-kunder</a></li>
                    <li><a href="logout.php" class="logout" onclick="return confirm('Är du säker på att du vill logga ut?')">Logga ut</a></li>
                </ul>
            </nav>
        </div>
        <h1>Kläduthyrning</h1>
    </header>
</body>

</html>