<?php
require_once 'Assets/sessioncheck.php';
require_once 'Assets/config/db.php';
require_once 'Assets/functions.php';

$grupp = $_SESSION['grupp'] ?? '';
if ($grupp !== 'admin' && $grupp !== 'lager') {
    header('Location: index.php');
    exit();
}

$meddelande = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nytt_plagg'])) {
        $namn = trim($_POST['namnPlagg'] ?? '');
        $storlek = trim($_POST['storlek'] ?? '');
        $pris = $_POST['pris'] ?? 0;
        $kategoriID = $_POST['kategoriPlaggID'] ?? null;
        $beskrivning = trim($_POST['beskrivning'] ?? '');

        if ($namn !== '') {
            addNyttPlagg($namn, $beskrivning, $storlek, $pris, $kategoriID);
            $meddelande = "Plagget '$namn' har lagts till i lagret!";
        }
    } elseif (isset($_POST['boka_uthyrning'])) {
        $plaggID = $_POST['plaggID'] ?? 0;
        $kundID = $_POST['kundID'] ?? 0;
        $startdatum = $_POST['startdatum'] ?? date('Y-m-d');
        $slutdatum = $_POST['slutdatum'] ?? date('Y-m-d', strtotime('+3 days'));

        if ($plaggID && $kundID && skapaUthyrning($plaggID, $kundID, $startdatum, $slutdatum)) {
            $meddelande = "Uthyrningen är bokad och plagget är nu markerat som uthyrt!";
        }
    } elseif (isset($_POST['andra_status'])) {
        $plaggID = (int)($_POST['plaggID'] ?? 0);
        $nyStatus = $_POST['nyStatus'] ?? '';
        $tillatna = ['Tvätt', 'Trasig', 'Borttagen'];

        if ($plaggID && in_array($nyStatus, $tillatna, true) && updatePlaggStatus($plaggID, $nyStatus)) {
            if ($nyStatus === 'Tvätt') {
                $meddelande = "Plagget har skickats till tvätt/granskning!";
            } elseif ($nyStatus === 'Trasig') {
                $meddelande = "Plagget har markerats som trasigt!";
            } elseif ($nyStatus === 'Borttagen') {
                $meddelande = "Plagget har tagits bort från lagret!";
            }
        }
    }
}

$ledigaPlagg = getPlaggByStatus('Ledig');
$lagerOversikt = getLagerStatusPerModell();
$kategorier = getAllKategorier();
$kunder = getAllKunder();
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

                    <?php if ($grupp === 'admin' || $grupp === 'uthyrda' || $grupp === 'kundmot' || $grupp === 'lager'): ?>
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
        <h1>Lediga kläder & Lager</h1>
    </header>

    <main>
        <?php if ($meddelande): ?>
            <section class="card success-message">
                <p><?= htmlspecialchars($meddelande) ?></p>
            </section>
        <?php endif; ?>

        <h2>Lagerstatus per plaggmodell</h2>
        <?php if (empty($lagerOversikt)): ?>
            <section class="card card-form">
                <p>Inga plagg registrerade i systemet ännu.</p>
            </section>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($lagerOversikt as $modell): ?>
                    <?php $arSlut = ((int)$modell['antalLediga'] === 0); ?>
                    <section class="card <?= $arSlut ? 'card-warning' : '' ?>">
                        <div>
                            <h3>
                                <?= htmlspecialchars($modell['namnPlagg']) ?> (<?= htmlspecialchars($modell['storlek'] ?? '-') ?>)
                                <?php if ($arSlut): ?>
                                    <span class="status-danger">— HELT SLUT I LAGER</span>
                                <?php endif; ?>
                            </h3>
                            <p>
                                <strong>Kategori:</strong> <?= htmlspecialchars($modell['namnKategori'] ?? 'Ingen') ?><br>
                                <strong>Lediga i lager:</strong> <?= (int)$modell['antalLediga'] ?> st<br>
                                <strong>Uthyrda just nu:</strong> <?= (int)$modell['antalUthyrda'] ?> st<br>
                                <strong>På tvätt/granskning:</strong> <?= (int)$modell['antalInlamnade'] ?> st
                                <?php if ($arSlut): ?>
                                    <br><strong class="text-danger">Nästa förväntas tillbaka:</strong>
                                    <?= $modell['nastaReturDatum'] ? htmlspecialchars($modell['nastaReturDatum']) : 'Inget returdatum (ligger på tvätt/lagning)' ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2>Lägg till nytt plagg i lagret</h2>
        <section class="card card-form">
            <form method="POST" class="card-form-layout">
                <input type="text" name="namnPlagg" class="form-input" placeholder="Plaggets namn (t.ex. Smoking Svart)" required>
                <input type="text" name="storlek" class="form-input" placeholder="Storlek (t.ex. M, L, 50)" required>
                <input type="number" name="pris" class="form-input" placeholder="Pris per dag (kr)" required>
                <select name="kategoriPlaggID" class="form-input">
                    <option value="">-- Välj kategori --</option>
                    <?php foreach ($kategorier as $kat): ?>
                        <option value="<?= $kat['kategoriID'] ?>"><?= htmlspecialchars($kat['namnKategori']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="beskrivning" class="form-input" placeholder="Kort beskrivning / skick">
                <button type="submit" name="nytt_plagg" class="btn">+ Lägg till plagg</button>
            </form>
        </section>

        <h2>Tillgängliga plagg att hyra ut (<?= count($ledigaPlagg) ?>)</h2>
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
                                <strong>Beskrivning:</strong> <?= htmlspecialchars($plagg['beskrivning'] ?? '-') ?>
                            </p>
                        </div>

                        <div class="card-actions">
                            <?php if (!empty($kunder)): ?>
                                <form method="POST" class="booking-form">
                                    <input type="hidden" name="plaggID" value="<?= $plagg['plaggID'] ?>">
                                    <select name="kundID" class="form-input-sm" required>
                                        <option value="">Välj kund att hyra ut till...</option>
                                        <?php foreach ($kunder as $k): ?>
                                            <option value="<?= $k['kundID'] ?>"><?= htmlspecialchars($k['name']) ?> (#<?= $k['kundID'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="date-row">
                                        <input type="date" name="startdatum" class="form-input-sm" value="<?= date('Y-m-d') ?>" required>
                                        <input type="date" name="slutdatum" class="form-input-sm" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                                    </div>
                                    <button type="submit" name="boka_uthyrning" class="btn">Hyr ut plagg</button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" class="card-actions">
                                <input type="hidden" name="plaggID" value="<?= $plagg['plaggID'] ?>">
                                <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Tvätt'" class="btn btn-secondary">
                                    Måste tvättas
                                </button>
                                <button type="submit" name="andra_status" value="1" onclick="this.form.nyStatus.value='Trasig'" class="btn btn-danger">
                                    Markera som trasig
                                </button>
                                <button type="submit" name="andra_status" value="1" onclick="if(!confirm('Ta bort plagget från lagret?')) return false; this.form.nyStatus.value='Borttagen';" class="btn btn-danger">
                                    Ta bort plagg
                                </button>
                                <input type="hidden" name="nyStatus" value="">
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