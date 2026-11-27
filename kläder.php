<?php
require_once 'Assets/sessioncheck.php';
require_once 'Assets/config/db.php';
require_once 'Assets/functions.php';

$grupp = $_SESSION['grupp'] ?? '';
if ($grupp !== 'admin' && $grupp !== 'lager') {
    header('Location: index.php');
    exit();
}

$ledigaPlagg = getPlaggByStatus('Ledig');
?>
<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Kläder</title>
</head>

<body>
    <header>
        <div>
            <nav>
                <ul>
                    <a href="index.php"><img src="../Gymnasiet_Arbete/Bilder/Logo.png" alt="Logo" class="logo"></a>
                    <li><a href="index.php">Hem</a></li>

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
        <h1>Lediga kläder</h1>
    </header>

    <main>
        <h2>Tillgängliga plagg i lager (<?= count($ledigaPlagg) ?>)</h2>

        <?php if (empty($ledigaPlagg)): ?>
            <section class="card">
                <p>Det finns inga lediga kläder just nu.</p>
            </section>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($ledigaPlagg as $plagg): ?>
                    <section class="card">
                        <div>
                            <h3><?= htmlspecialchars($plagg['namnPlagg']) ?> (<?= htmlspecialchars($plagg['storlek'] ?? '-') ?>)</h3>
                            <p>
                                <strong>Kategori:</strong> <?= htmlspecialchars($plagg['namnKategori'] ?? 'Ingen') ?><br>
                                <strong>Pris:</strong> <?= htmlspecialchars($plagg['pris'] ?? '0') ?> kr/dag<br>
                                <strong>Status:</strong> <?= htmlspecialchars($plagg['statusPlagg'] ?? 'Ledig') ?><br>
                                <strong>Beskrivning:</strong> <?= htmlspecialchars($plagg['beskrivning'] ?? '-') ?>
                            </p>
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