<?php

function login($name, $password)
{
    global $hyrning;

    $sql = "SELECT * FROM kund WHERE name = :name";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->execute();

    $kund = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kund) {
        return false;
    }

    if (password_verify($password, $kund['password'])) {
        return $kund;
    }

    return false;
}
function addUser($felt)
{
    global $hyrning;

    $sql = "INSERT INTO kund (name, email, password)
            VALUES (:name, :email, :password)";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':name', $felt['name']);
    $stmt->bindValue(':email', $felt['email']);
    $stmt->bindValue(':password', password_hash($felt['password'], PASSWORD_DEFAULT));

    try {
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

function updateUser($newPassword, $newEmail, $newName)
{
    global $hyrning;

    $sql = "UPDATE kund
            SET password = :password, email = :email, name = :name
            WHERE kund = :kund";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':password', password_hash($newPassword, PASSWORD_DEFAULT));
    $stmt->bindValue(':email', $newEmail);
    $stmt->bindValue(':name', $newName);

    return $stmt->execute();
}
function getUser($kund)
{
    global $hyrning;

    $sql = "SELECT * FROM kund WHERE kund = :kund";
    $stmt = $hyrning->prepare($sql);
    $stmt->bindValue(':kund', $kund);
    $stmt->execute();

    return $stmt;
}
