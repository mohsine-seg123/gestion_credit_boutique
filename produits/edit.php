<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Modifier un produit";
$current_page = "produits";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$message = "";
$boutique_id = $_SESSION["boutique_id"];

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET["id"];

$sqlProduit = "SELECT * FROM produits WHERE id = :id AND boutique_id = :boutique_id LIMIT 1";
$stmtProduit = $pdo->prepare($sqlProduit);
$stmtProduit->bindParam(":id", $id);
$stmtProduit->bindParam(":boutique_id", $boutique_id);
$stmtProduit->execute();
$produit = $stmtProduit->fetch();

if (!$produit) {
    header("Location: index.php");
    exit();
}

$nom_produit = $produit["nom_produit"];
$prix = $produit["prix"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_produit = trim($_POST["nom_produit"]);
    $prix = trim($_POST["prix"]);

    if (empty($nom_produit) || empty($prix)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        $sqlUpdate = "UPDATE produits
                      SET nom_produit = :nom_produit, prix = :prix
                      WHERE id = :id AND boutique_id = :boutique_id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(":nom_produit", $nom_produit);
        $stmtUpdate->bindParam(":prix", $prix);
        $stmtUpdate->bindParam(":id", $id);
        $stmtUpdate->bindParam(":boutique_id", $boutique_id);

        if ($stmtUpdate->execute()) {
            header("Location: index.php?success=Produit modifié avec succès");
            exit();
        } else {
            $message = "Erreur lors de la modification.";
        }
    }
}
?>

<div class="page-content">
    <div class="page-header">
        <h2>Modifier un produit</h2>
        <a href="index.php" class="btn btn-secondary">Retour</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="alert error-alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form action="" method="POST" class="custom-form">
        <div class="form-group">
            <label for="nom_produit">Nom du produit</label>
            <input
                type="text"
                id="nom_produit"
                name="nom_produit"
                value="<?php echo htmlspecialchars($nom_produit); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="prix">Prix</label>
            <input
                type="number"
                step="0.01"
                id="prix"
                name="prix"
                value="<?php echo htmlspecialchars($prix); ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Mettre à jour</button>
    </form>
</div>

</div>
</body>
</html>