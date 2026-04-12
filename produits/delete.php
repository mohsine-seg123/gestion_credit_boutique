<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/db.php";

$boutique_id = $_SESSION["boutique_id"];

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET["id"];

$sqlCheck = "SELECT * FROM produits WHERE id = :id AND boutique_id = :boutique_id LIMIT 1";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->bindParam(":id", $id);
$stmtCheck->bindParam(":boutique_id", $boutique_id);
$stmtCheck->execute();
$produit = $stmtCheck->fetch();

if (!$produit) {
    header("Location: index.php?success=Produit introuvable");
    exit();
}

$sqlDelete = "DELETE FROM produits WHERE id = :id AND boutique_id = :boutique_id";
$stmtDelete = $pdo->prepare($sqlDelete);
$stmtDelete->bindParam(":id", $id);
$stmtDelete->bindParam(":boutique_id", $boutique_id);

if ($stmtDelete->execute()) {
    header("Location: index.php?success=Produit supprimé avec succès");
    exit();
} else {
    header("Location: index.php?success=Erreur lors de la suppression");
    exit();
}
?>