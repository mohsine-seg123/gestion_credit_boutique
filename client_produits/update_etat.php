<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/db.php";

$boutique_id = $_SESSION["boutique_id"];

if (
    !isset($_GET["id"]) || empty($_GET["id"]) ||
    !isset($_GET["etat"]) || empty($_GET["etat"])
) {
    header("Location: ../clients/index.php");
    exit();
}

$id = (int) $_GET["id"];
$etat = $_GET["etat"];

if ($etat != "en cours" && $etat != "regle") {
    header("Location: ../clients/index.php");
    exit();
}

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

$sqlUpdate = "UPDATE client_produits SET etat_credit = :etat WHERE id = :id";
$stmtUpdate = $pdo->prepare($sqlUpdate);
$stmtUpdate->bindParam(":etat", $etat);
$stmtUpdate->bindParam(":id", $id);

if ($stmtUpdate->execute()) {
    header("Location: ../clients/show.php?id=" . $client_id . "&success=État du crédit mis à jour");
    exit();
} else {
    header("Location: ../clients/show.php?id=" . $client_id . "&success=Erreur lors de la mise à jour");
    exit();
}
?>