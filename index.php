<?php
require_once 'Assets/sessioncheck.php';
require_once 'Assets/config/db.php';
require_once 'Assets/functions.php';


$antalLediga = countPlaggByStatus('Ledig');
$antalUthyrda = countPlaggByStatus('Uthyrd');
$antalInlamnade = countPlaggByStatus('Inlämnad');
$antalKunder = countKunder();
$grupp = $_SESSION['grupp'] ?? '';
?>
<!DOCTYPE html>
<html lang="sv">

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
                    <a href="index.php"><img src="../Gymnasiet_Arbete/Bilder/Logo.png" alt="Logo" class="logo"></a>
                    <?php if ($grupp === 'admin' || $grupp === 'lager'): ?>
                        <li><a href="kläder.php">Kläder</a></li>
                    <?php endif; ?>

                    <?php if ($grupp === 'admin' || $grupp === 'uthyrning' || $grupp === 'kundmot' || $grupp === 'lager'): ?>
                        <li><a href="uthyrda.php">Uthyrda-kläder</a></li>
                    <?php endif; ?>

                    <?php if ($grupp === 'admin' || $grupp === 'inlämnad'): ?>
                        <li><a href="inlämnade.php">Inlämnade-kläder</a></li>
                    <?php endif; ?>

                    <?php if ($grupp === 'admin' || $grupp === 'kundmot'): ?>
                        <li><a href="kund.php">Alla-kunder</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="logout" onclick="return confirm('Är du säker på att du vill logga ut?')">Logga ut</a></li>
                </ul>
            </nav>
        </div>
        <br>
        <h1>Kläduthyrning</h1>
    </header>
    <main>
        <h2>Översikt & Hantering</h2>
        <div class="grid">
            <?php if ($grupp === 'admin' || $grupp === 'lager'): ?>
                <section class="card">
                    <h3>Lediga kläder (<?= $antalLediga ?>)</h3>
                    <p>Hantera plagg som inte är uthyrda än, lägg till nya plagg eller boka en ny uthyrning.</p>
                    <a href="kläder.php" class="btn">Gå till lediga kläder</a>
                </section>
            <?php endif; ?>

            <?php if ($grupp === 'admin' || $grupp === 'uthyrning' || $grupp === 'kundmot' || $grupp === 'lager'): ?>
                <section class="card">
                    <h3>Uthyrda kläder (<?= $antalUthyrda ?>)</h3>
                    <p>Se aktiva uthyrningar och kontrollera om plagg är rena och fina eller om något har gått sönder.</p>
                    <a href="uthyrda.php" class="btn">Hantera uthyrda kläder</a>
                </section>
            <?php endif; ?>

            <?php if ($grupp === 'admin' || $grupp === 'inlämnad'): ?>
                <section class="card">
                    <h3>Inlämnade kläder (<?= $antalInlamnade ?>)</h3>
                    <p>Granska återlämnade plagg - avgör om de måste tvättas/lagas eller är redo att hyras ut igen.</p>
                    <a href="inlämnade.php" class="btn">Granska inlämnade kläder</a>
                </section>
            <?php endif; ?>

            <?php if ($grupp === 'admin' || $grupp === 'kundmot'): ?>
                <section class="card">
                    <h3>Alla kunder (<?= $antalKunder ?>)</h3>
                    <p>Visa kundregister, kontaktuppgifter, adresser och kundernas uthyrningshistorik.</p>
                    <a href="kund.php" class="btn">Gå till kundregister</a>
                </section>
            <?php endif; ?>

        </div>
    </main>
    <footer>
        <p>&copy; 2026 Kläduthyrning. Alla rättigheter förbehållna.</p>
    </footer>
</body>


</html>