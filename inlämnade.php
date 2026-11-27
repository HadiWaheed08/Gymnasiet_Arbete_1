<?php
require_once 'Assets/sessioncheck.php';
require_once 'Assets/config/db.php';
require_once 'Assets/functions.php';

$grupp = $_SESSION['grupp'] ?? '';
if ($grupp !== 'admin' && $grupp !== 'inlämnad') {
    header('Location: index.php');
    exit();
}

$meddelande = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plaggID = (int)($_POST['plaggID'] ?? 0);

    if (isset($_POST['godkann_plagg']) && $plaggID) {
        updatePlaggStatus($plaggID, 'Ledig');
        $meddelande = "Plagget är godkänt och ligger nu ute som ledigt i lagret igen!";
    } elseif (isset($_POST['kassera_plagg']) && $plaggID) {
        updatePlaggStatus($plaggID, 'Trasig');
        $meddelande = "Plagget har markerats som trasigt/kasserat och tas bort från uthyrning.";
    }
}

$inlamnadePlagg = getPlaggByStatus('Inlämnad');
?>
<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Inlämnade-kläder</title>
</head>

<body>
    <header>
        <div>
            <nav>
                <ul>
                    <a href="index.php"><img src="../Gymnasiet_Arbete/Bilder/Logo.png" alt="Logo" class="logo"></a>
                    <li><a href="index.php">Hem</a></li>

                    <?php if ($grupp === 'admin' || $grupp === 'lager'): ?>
                        <li><a href="kläder.php">Kläder</a></li>
                    <?php endif; ?>

                    <?php if ($grupp === 'admin' || $grupp === 'uthyrda' || $grupp === 'kundmot' || $grupp === 'lager'): ?>
                        <li><a href="uthyrda.php">Uthyrda-kläder</a></li>
                    <?php endif; ?>

                    <?php if ($grupp === 'admin' || $grupp === 'kundmot'): ?>
                        <li><a href="kund.php">Alla-kunder</a></li>
                    <?php endif; ?>

                    <li><a href="logout.php" class="logout" onclick="return confirm('Är du säker på att du vill logga ut?')">Logga ut</a></li>
                </ul>
            </nav>
        </div>
        <br>
        <h1>Inlämnade kläder</h1>
    </header>

    <main>
        <?php if ($meddelande): ?>
            <section class="card success-message">
                <p><?= htmlspecialchars($meddelande) ?></p>
            </section>
        <?php endif; ?>

        <h2>Plagg för granskning & tvätt (<?= count($inlamnadePlagg) ?>)</h2>

        <?php if (empty($inlamnadePlagg)): ?>
            <section class="card">
                <p>Det finns inga inlämnade kläder just nu.</p>
            </section>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($inlamnadePlagg as $plagg): ?>
                    <section class="card">
                        <div>
                            <h3><?= htmlspecialchars($plagg['namnPlagg']) ?> (<?= htmlspecialchars($plagg['storlek'] ?? '-') ?>)</h3>
                            <p>
                                <strong>Kategori:</strong> <?= htmlspecialchars($plagg['namnKategori'] ?? 'Ingen') ?><br>
                                <strong>Pris:</strong> <?= htmlspecialchars($plagg['pris'] ?? '0') ?> kr/dag<br>
                                <strong>Status:</strong> <?= htmlspecialchars($plagg['statusPlagg'] ?? 'Inlämnad') ?><br>
                                <strong>Beskrivning:</strong> <?= htmlspecialchars($plagg['beskrivning'] ?? '-') ?>
                            </p>
                        </div>

                        <div class="card-actions">
                            <form method="POST">
                                <input type="hidden" name="plaggID" value="<?= $plagg['plaggID'] ?>">
                                <button type="submit" name="godkann_plagg" class="btn">
                                    Godkänn tvätt/skick &rarr; Gör ledig
                                </button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Är du säker på att plagget är trasigt och ska tas ur bruk?');">
                                <input type="hidden" name="plaggID" value="<?= $plagg['plaggID'] ?>">
                                <button type="submit" name="kassera_plagg" class="btn btn-danger">
                                    Markera som trasig
                                </button>
                            </form>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Kläduthyrning. Alla rättigheter förbehållna.</p>
    </footer>
</body>

</html>