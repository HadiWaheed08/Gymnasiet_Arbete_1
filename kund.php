<?php
require_once 'Assets/sessioncheck.php';
require_once 'Assets/config/db.php';
require_once 'Assets/functions.php';

$grupp = $_SESSION['grupp'] ?? '';
if ($grupp !== 'admin' && $grupp !== 'kundmot') {
    header('Location: index.php');
    exit();
}

$kunder = getAllKunder();
?>
<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Alla-kunder</title>
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

                    <?php if ($grupp === 'admin' || $grupp === 'inlämnad'): ?>
                        <li><a href="inlämnade.php">Inlämnade-kläder</a></li>
                    <?php endif; ?>

                    <li><a href="logout.php" class="logout" onclick="return confirm('Är du säker på att du vill logga ut?')">Logga ut</a></li>
                </ul>
            </nav>
        </div>
        <br>
        <h1>Alla kunder</h1>
    </header>

    <main>
        <h2>Registrerade kunder (<?= count($kunder) ?>)</h2>

        <?php if (empty($kunder)): ?>
            <section class="card">
                <p>Det finns inga registrerade kunder just nu.</p>
            </section>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($kunder as $kund): ?>
                    <?php
                    $uthyrningar = getKundUthyrningar($kund['kundID']);
                    $aktiva = array_filter($uthyrningar, fn($u) => $u['statusPlagg'] === 'Uthyrd');
                    $tidigare = array_filter($uthyrningar, fn($u) => $u['statusPlagg'] !== 'Uthyrd');
                    ?>
                    <section class="card">
                        <div>
                            <h3><?= htmlspecialchars($kund['name'] ?? 'Namnlös kund') ?></h3>
                            <p>
                                <strong>Kund-ID:</strong> #<?= htmlspecialchars($kund['kundID']) ?><br>
                                <strong>E-post:</strong> <?= htmlspecialchars($kund['email'] ?? '-') ?><br>
                                <strong>Adress:</strong> <?= htmlspecialchars($kund['adress'] ?? '-') ?>
                            </p>

                            <hr class="card-divider">

                            <p class="section-label"><strong>Hyr just nu (<?= count($aktiva) ?>):</strong></p>
                            <?php if (empty($aktiva)): ?>
                                <p class="muted-text">Inga aktiva uthyrningar.</p>
                            <?php else: ?>
                                <ul class="rental-list">
                                    <?php foreach ($aktiva as $a): ?>
                                        <?php $forsenad = ((int)($a['dagarForsenad'] ?? 0) > 0); ?>
                                        <li class="<?= $forsenad ? 'rental-overdue' : '' ?>">
                                            <?= htmlspecialchars($a['namnPlagg']) ?> (<?= htmlspecialchars($a['storlek'] ?? '-') ?>)
                                            - retur <?= htmlspecialchars($a['slutdatum'] ?? '-') ?>
                                            <?= $forsenad ? '(FÖRSENAD!)' : '' ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <p class="section-label"><strong>Tidigare hyrda plagg (<?= count($tidigare) ?>):</strong></p>
                            <?php if (empty($tidigare)): ?>
                                <p class="muted-text">Ingen historik ännu.</p>
                            <?php else: ?>
                                <ul class="rental-list rental-list-history">
                                    <?php foreach ($tidigare as $t): ?>
                                        <li>
                                            <?= htmlspecialchars($t['namnPlagg']) ?> (<?= htmlspecialchars($t['storlek'] ?? '-') ?>)
                                            - hyrdes <?= htmlspecialchars($t['startdatum'] ?? '-') ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
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