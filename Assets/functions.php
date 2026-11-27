<?php
require_once 'Assets/config/db.php';

function getUser($username, $password)
{
    global $hyrning;

    $sql = "SELECT * FROM user WHERE username = :username";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    if ($password === $user['password']) {
        return $user;
    }

    return false;
}

function countPlaggByStatus($status)
{
    global $hyrning;

    $sql = "SELECT COUNT(*) FROM plagg WHERE statusPlagg = :status";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':status', $status);
    $stmt->execute();

    return $stmt->fetchColumn();
}

function countKunder()
{
    global $hyrning;

    $sql = "SELECT COUNT(*) FROM kund";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchColumn();
}

// Hämtar alla kategorier (för formulär)
function getAllKategorier()
{
    global $hyrning;

    $sql = "SELECT * FROM Kategori ORDER BY namnKategori ASC";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hämtar alla plagg med en viss status (t.ex. 'Ledig' eller 'Inlämnad') inklusive kategori
function getPlaggByStatus($status)
{
    global $hyrning;

    $sql = "SELECT plagg.*, Kategori.namnKategori 
            FROM plagg 
            LEFT JOIN Kategori ON plagg.kategoriPlaggID = Kategori.kategoriID 
            WHERE plagg.statusPlagg = :status 
            ORDER BY plagg.plaggID DESC";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':status', $status);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// NY: Hämtar lagerstatus per modell & storlek (visar antal lediga, uthyrda och när nästa beräknas komma tillbaka)
function getLagerStatusPerModell()
{
    global $hyrning;

    $sql = "SELECT 
                p.namnPlagg,
                p.storlek,
                k.namnKategori,
                SUM(CASE WHEN p.statusPlagg = 'Ledig' THEN 1 ELSE 0 END) AS antalLediga,
                SUM(CASE WHEN p.statusPlagg = 'Uthyrd' THEN 1 ELSE 0 END) AS antalUthyrda,
                SUM(CASE WHEN p.statusPlagg IN ('Inlämnad', 'Tvätt') = 'Inlämnad', 'Tvätt' THEN 1 ELSE 0 END) AS antalInlamnade,
                (
                    SELECT MIN(u.slutdatum)
                    FROM plagg p2
                    JOIN uthyrning_plagg up ON p2.plaggID = up.utplaggID
                    JOIN Uthyrning u ON up.uthyrningID = u.hyrningID
                    WHERE p2.namnPlagg = p.namnPlagg
                      AND COALESCE(p2.storlek, '') = COALESCE(p.storlek, '')
                      AND p2.statusPlagg = 'Uthyrd'
                ) AS nastaReturDatum
            FROM plagg p
            LEFT JOIN Kategori k ON p.kategoriPlaggID = k.kategoriID
            GROUP BY p.namnPlagg, p.storlek, k.namnKategori
            ORDER BY antalLediga ASC, p.namnPlagg ASC";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// NY: Lägger till ett nytt plagg i databasen
function addNyttPlagg($namn, $beskrivning, $storlek, $pris, $kategoriID)
{
    global $hyrning;

    $sql = "INSERT INTO plagg (namnPlagg, beskrivning, storlek, pris, statusPlagg, kategoriPlaggID)
            VALUES (:namn, :beskrivning, :storlek, :pris, 'Ledig', :kategoriID)";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':namn', $namn);
    $stmt->bindValue(':beskrivning', $beskrivning);
    $stmt->bindValue(':storlek', $storlek);
    $stmt->bindValue(':pris', $pris);
    $stmt->bindValue(':kategoriID', $kategoriID ?: null);

    return $stmt->execute();
}

// NY: Skapar en ny uthyrning och ändrar plaggets status till 'Uthyrd'
function skapaUthyrning($plaggID, $kundID, $startdatum, $slutdatum)
{
    global $hyrning;

    try {
        $hyrning->beginTransaction();

        $sql1 = "INSERT INTO Uthyrning (hyrningKundID, startdatum, slutdatum, status)
                 VALUES (:kundID, :startdatum, :slutdatum, 'Aktiv')";
        $stmt1 = $hyrning->prepare($sql1);
        $stmt1->bindValue(':kundID', $kundID);
        $stmt1->bindValue(':startdatum', $startdatum);
        $stmt1->bindValue(':slutdatum', $slutdatum);
        $stmt1->execute();

        $hyrningID = $hyrning->lastInsertId();

        $sql2 = "INSERT INTO uthyrning_plagg (uthyrningID, utplaggID) VALUES (:hyrningID, :plaggID)";
        $stmt2 = $hyrning->prepare($sql2);
        $stmt2->bindValue(':hyrningID', $hyrningID);
        $stmt2->bindValue(':plaggID', $plaggID);
        $stmt2->execute();

        $sql3 = "UPDATE plagg SET statusPlagg = 'Uthyrd' WHERE plaggID = :plaggID";
        $stmt3 = $hyrning->prepare($sql3);
        $stmt3->bindValue(':plaggID', $plaggID);
        $stmt3->execute();

        $hyrning->commit();
        return true;
    } catch (Exception $e) {
        $hyrning->rollBack();
        return false;
    }
}

// Hämtar alla uthyrda plagg tillsammans med kundinfo och hyresdatum (senaste aktiva uthyrningen per plagg)
function getUthyrdaPlagg()
{
    global $hyrning;

    $sql = "SELECT plagg.*, Kategori.namnKategori, Uthyrning.hyrningID, Uthyrning.startdatum, Uthyrning.slutdatum, 
                   kund.name AS kundNamn, kund.email AS kundEmail,
                   DATEDIFF(CURDATE(), Uthyrning.slutdatum) AS dagarForsenad
            FROM plagg
            LEFT JOIN Kategori ON plagg.kategoriPlaggID = Kategori.kategoriID
            LEFT JOIN uthyrning_plagg ON plagg.plaggID = uthyrning_plagg.utplaggID
            LEFT JOIN Uthyrning ON uthyrning_plagg.uthyrningID = Uthyrning.hyrningID
            LEFT JOIN kund ON Uthyrning.hyrningKundID = kund.kundID
            WHERE plagg.statusPlagg = 'Uthyrd'
              AND (Uthyrning.hyrningID IS NULL OR Uthyrning.hyrningID = (
                  SELECT MAX(up2.uthyrningID) 
                  FROM uthyrning_plagg up2 
                  WHERE up2.utplaggID = plagg.plaggID
              ))
            ORDER BY Uthyrning.slutdatum ASC";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// NY: Uppdaterar status på ett plagg (t.ex. Uthyrd -> Inlämnad, eller Inlämnad -> Ledig)
function updatePlaggStatus($plaggID, $nyStatus, $hyrningID = null)
{
    global $hyrning;

    $sql = "UPDATE plagg SET statusPlagg = :status WHERE plaggID = :plaggID";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':status', $nyStatus);
    $stmt->bindValue(':plaggID', $plaggID);
    $ok = $stmt->execute();

    if ($ok && $nyStatus === 'Inlämnad' && $hyrningID) {
        $sqlU = "UPDATE Uthyrning SET status = 'Avslutad' WHERE hyrningID = :hyrningID";
        $stmtU = $hyrning->prepare($sqlU);
        $stmtU->bindValue(':hyrningID', $hyrningID);
        $stmtU->execute();
    }

    return $ok;
}

// Hämtar alla kunder i kundregistret
function getAllKunder()
{
    global $hyrning;

    $sql = "SELECT kundID, name, adress, email FROM kund ORDER BY kundID DESC";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// NY: Hämtar alla plagg en specifik kund hyr just nu eller har hyrt tidigare
function getKundUthyrningar($kundID)
{
    global $hyrning;

    $sql = "SELECT plagg.namnPlagg, plagg.storlek, plagg.statusPlagg, 
                   Uthyrning.hyrningID, Uthyrning.startdatum, Uthyrning.slutdatum, Uthyrning.status AS hyrStatus,
                   DATEDIFF(CURDATE(), Uthyrning.slutdatum) AS dagarForsenad
            FROM Uthyrning
            JOIN uthyrning_plagg ON Uthyrning.hyrningID = uthyrning_plagg.uthyrningID
            JOIN plagg ON uthyrning_plagg.utplaggID = plagg.plaggID
            WHERE Uthyrning.hyrningKundID = :kundID
            ORDER BY Uthyrning.startdatum DESC";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':kundID', $kundID);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
