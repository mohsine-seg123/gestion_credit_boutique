<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Modifier la ligne de crédit";
$current_page = "clients";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$boutique_id = $_SESSION["boutique_id"];
$message = "";

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: ../clients/index.php");
    exit();
}

$id = (int) $_GET["id"];

$sqlLigne = "SELECT cp.*, c.nom AS nom_client, c.id AS client_id_verif
             FROM client_produits cp
             INNER JOIN clients c ON cp.client_id = c.id
             WHERE cp.id = :id AND c.boutique_id = :boutique_id
             LIMIT 1";
$stmtLigne = $pdo->prepare($sqlLigne);
$stmtLigne->bindParam(":id", $id);
$stmtLigne->bindParam(":boutique_id", $boutique_id);
$stmtLigne->execute();
$ligne = $stmtLigne->fetch();

if (!$ligne) {
    header("Location: ../clients/index.php");
    exit();
}

$client_id = $ligne["client_id"];
$quantite = $ligne["quantite"];
$produit_id = $ligne["produit_id"];

$sqlProduits = "SELECT * FROM produits
                WHERE boutique_id = :boutique_id
                ORDER BY nom_produit ASC";
$stmtProduits = $pdo->prepare($sqlProduits);
$stmtProduits->bindParam(":boutique_id", $boutique_id);
$stmtProduits->execute();
$produits = $stmtProduits->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produit_id = (int) $_POST["produit_id"];
    $quantite = (int) $_POST["quantite"];

    if (empty($produit_id) || empty($quantite) || $quantite <= 0) {
        $message = "Veuillez remplir correctement tous les champs.";
    } else {
        $sqlProduit = "SELECT * FROM produits
                       WHERE id = :produit_id AND boutique_id = :boutique_id
                       LIMIT 1";
        $stmtProduit = $pdo->prepare($sqlProduit);
        $stmtProduit->bindParam(":produit_id", $produit_id);
        $stmtProduit->bindParam(":boutique_id", $boutique_id);
        $stmtProduit->execute();
        $produit = $stmtProduit->fetch();

        if (!$produit) {
            $message = "Produit introuvable.";
        } else {
            $prix_unitaire = $produit["prix"];
            $total = $prix_unitaire * $quantite;

            $sqlUpdate = "UPDATE client_produits
                          SET produit_id = :produit_id,
                              quantite = :quantite,
                              prix_unitaire = :prix_unitaire,
                              total = :total
                          WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":produit_id", $produit_id);
            $stmtUpdate->bindParam(":quantite", $quantite);
            $stmtUpdate->bindParam(":prix_unitaire", $prix_unitaire);
            $stmtUpdate->bindParam(":total", $total);
            $stmtUpdate->bindParam(":id", $id);

            if ($stmtUpdate->execute()) {
                header("Location: ../clients/show.php?id=" . $client_id . "&success=Ligne modifiée avec succès");
                exit();
            } else {
                $message = "Erreur lors de la modification.";
            }
        }
    }
}
?>

<div class="page-content">
    <div class="page-header">
        <h2>Modifier la ligne de crédit</h2>
        <a href="../clients/show.php?id=<?php echo $client_id; ?>" class="btn btn-secondary">Retour</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="alert error-alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <div class="info-card">
        <p><strong>Client :</strong> <?php echo htmlspecialchars($ligne["nom_client"]); ?></p>
    </div>

    <form action="" method="POST" class="custom-form">
        <div class="form-group">
            <label for="produit_id">Produit</label>
            <select name="produit_id" id="produit_id" required>
                <option value="">-- Choisir un produit --</option>
                <?php foreach ($produits as $produit) { ?>
                    <option value="<?php echo $produit["id"]; ?>"
                        <?php if ($produit_id == $produit["id"]) { echo "selected"; } ?>>
                        <?php echo htmlspecialchars($produit["nom_produit"]); ?> - <?php echo number_format($produit["prix"], 2); ?> DH
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantite">Quantité</label>
            <input
                type="number"
                name="quantite"
                id="quantite"
                min="1"
                value="<?php echo htmlspecialchars($quantite); ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Mettre à jour</button>
    </form>
</div>

</div>
</body>
</html>