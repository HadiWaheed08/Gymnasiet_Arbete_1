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
