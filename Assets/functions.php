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

    /*if (password_verify($password, $user['password'])) {
        return $user;
    }*/
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

// Räknar totalt antal registrerade kunder
function countKunder()
{
    global $hyrning;

    $sql = "SELECT COUNT(*) FROM kund";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchColumn();
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

// Hämtar alla uthyrda plagg tillsammans med kundinfo och hyresdatum
function getUthyrdaPlagg()
{
    global $hyrning;

    $sql = "SELECT plagg.*, Kategori.namnKategori, Uthyrning.hyrningID, Uthyrning.startdatum, Uthyrning.slutdatum, kund.name AS kundNamn, kund.email AS kundEmail
            FROM plagg
            LEFT JOIN Kategori ON plagg.kategoriPlaggID = Kategori.kategoriID
            LEFT JOIN uthyrning_plagg ON plagg.plaggID = uthyrning_plagg.utplaggID
            LEFT JOIN Uthyrning ON uthyrning_plagg.uthyrningID = Uthyrning.hyrningID
            LEFT JOIN kund ON Uthyrning.hyrningKundID = kund.kundID
            WHERE plagg.statusPlagg = 'Uthyrd'
            ORDER BY Uthyrning.slutdatum ASC";
    $stmt = $hyrning->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
