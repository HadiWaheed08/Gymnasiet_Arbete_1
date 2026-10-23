<?php
$hostname_hyrning = "localhost";
$database_hyrning = "hyrning";
$username_hyrning = "hyrning";
$password_hyrning = "hadi";


try {
    $hyrning = new PDO("mysql:host=$hostname_hyrning;dbname=$database_hyrning", $username_hyrning, $password_hyrning);
    // set the PDO error mode to exception
    $hyrning->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; // denna rad kan tas bort om allt fungerar
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
