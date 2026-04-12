<?php
// $host = "localhost";
// $dbname = "boutique";
// $username = "root";
// $password = "";


$host = "sql306.infinityfree.com";
$dbname = "if0_41643909_boutique";
$username = "if0_41643909";
$password = "mohsine123SEG";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>