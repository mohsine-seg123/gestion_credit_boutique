<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/db.php";

$boutique_id = $_SESSION["boutique_id"];

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: ../clients/index.php");
    exit();
}

$id = (int) $_GET["id"];

$sqlCheck = "SELECT cp.id, cp.client_id
             FROM client_produits cp
             INNER JOIN clients c ON cp.client_id = c.id
             WHERE cp.id = :id AND c.boutique_id = :boutique_id
             LIMIT 1";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->bindParam(":id", $id);
$stmtCheck->bindParam(":boutique_id", $boutique_id);
$stmtCheck->execute();
$ligne = $stmtCheck->fetch();

if (!$ligne) {
    header("Location: ../clients/index.php");
    exit();
}

$client_id = $ligne["client_id"];

$sqlDelete = "DELETE FROM client_produits WHERE id = :id";
$stmtDelete = $pdo->prepare($sqlDelete);
$stmtDelete->bindParam(":id", $id);

if ($stmtDelete->execute()) {
    header("Location: ../clients/show.php?id=" . $client_id . "&success=Ligne supprimée avec succès");
    exit();
} else {
    header("Location: ../clients/show.php?id=" . $client_id . "&success=Erreur lors de la suppression");
    exit();
}
?>