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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['andra_status'])) {
    $plaggID = (int)($_POST['plaggID'] ?? 0);
    $nyStatus = $_POST['nyStatus'] ?? '';
    $tillatna = ['Ledig', 'Tvätt', 'Trasig', 'Borttagen'];

    if ($plaggID && in_array($nyStatus, $tillatna, true) && updatePlaggStatus($plaggID, $nyStatus)) {
        if ($nyStatus === 'Ledig') {
            $meddelande = "Plagget är godkänt och ligger nu ute som ledigt i lagret igen!";
        } elseif ($nyStatus === 'Tvätt') {
            $meddelande = "Plagget har skickats till tvätt!";
        } elseif ($nyStatus === 'Trasig') {
            $meddelande = "Plagget har markerats som trasigt och tagits ur bruk.";
        } elseif ($nyStatus === 'Borttagen') {
            $meddelande = "Plagget har tagits bort från systemet.";
        }
    }
}

$inlamnadePlagg = getPlaggByStatus('Inlämnad');
$tvattPlagg = getPlaggByStatus('Tvätt');
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
        <h1>Inlämnade kläder & Tvätt</h1>
    </header>

    <main>
        <?php if ($meddelande): ?>
            <section class="card success-message">
                <p><?= htmlspecialchars($meddelande) ?></p>
            </section>
        <?php endif; ?>

        <!-- 1. NYSS INLÄMNADE PLAGG -->
        <h2>Nyss inlämnade plagg för granskning (<?= count($inlamnadePlagg) ?>)</h2>
        <?php if (empty($inlamnadePlagg)): ?>
            <section class="card card-form">
                <p>Det finns inga nyss inlämnade kläder att granska.</p>
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

                        <form method="POST" class="card-actions">
                            <input type="hidden" name="plaggID" value="<?= $plagg['plaggID'] ?>">
                            <input type="hidden" name="nyStatus" value="">

                            <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Ledig'" class="btn">
                                Godkänn direkt &rarr; Gör ledig
                            </button>
                            <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Tvätt'" class="btn btn-secondary">
                                Måste tvättas
                            </button>
                            <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Trasig'" class="btn btn-danger">
                                Markera som trasig
                            </button>
                            <button type="submit" name="andra_status" value="1" onclick="if(!confirm('Ta bort plagget helt från lagret?')) return false; this.form.nyStatus.value='Borttagen';" class="btn btn-danger">
                                Ta bort plagg
                            </button>
                        </form>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 2. PLAGG SOM LIGGER PÅ TVÄTT -->
        <h2>Plagg på tvätt (<?= count($tvattPlagg) ?>)</h2>
        <?php if (empty($tvattPlagg)): ?>
            <section class="card">
                <p>Inga plagg ligger på tvätt just nu.</p>
            </section>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($tvattPlagg as $plagg): ?>
                    <section class="card">
                        <div>
                            <h3><?= htmlspecialchars($plagg['namnPlagg']) ?> (<?= htmlspecialchars($plagg['storlek'] ?? '-') ?>)</h3>
                            <p>
                                <strong>Kategori:</strong> <?= htmlspecialchars($plagg['namnKategori'] ?? 'Ingen') ?><br>
                                <strong>Status:</strong> På tvätt<br>
                                <strong>Beskrivning:</strong> <?= htmlspecialchars($plagg['beskrivning'] ?? '-') ?>
                            </p>
                        </div>

                        <form method="POST" class="card-actions">
                            <input type="hidden" name="plaggID" value="<?= $plagg['plaggID'] ?>">
                            <input type="hidden" name="nyStatus" value="">

                            <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Ledig'" class="btn">
                                Tvätt klar &rarr; Gör ledig i lager
                            </button>
                            <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Trasig'" class="btn btn-danger">
                                Markera som trasig
                            </button>
                        </form>
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