<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Ajouter un produit";
$current_page = "produits";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_produit = trim($_POST["nom_produit"]);
    $prix = trim($_POST["prix"]);
    $boutique_id = $_SESSION["boutique_id"];

    if (empty($nom_produit) || empty($prix)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        $sql = "INSERT INTO produits (boutique_id, nom_produit, prix)
                VALUES (:boutique_id, :nom_produit, :prix)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":boutique_id", $boutique_id);
        $stmt->bindParam(":nom_produit", $nom_produit);
        $stmt->bindParam(":prix", $prix);

        if ($stmt->execute()) {
            header("Location: index.php?success=Produit ajouté avec succès");
            exit();
        } else {
            $message = "Erreur lors de l'ajout du produit.";
        }
    }
}
?>

<div class="page-content">
    <div class="page-header">
        <h2>Ajouter un produit</h2>
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
                value="<?php if (isset($nom_produit)) { echo htmlspecialchars($nom_produit); } ?>"
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
                value="<?php if (isset($prix)) { echo htmlspecialchars($prix); } ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>

</div>
</body>
</html>