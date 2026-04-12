<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Produits";
$current_page = "produits";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$boutique_id = $_SESSION["boutique_id"];

$sql = "SELECT * FROM produits WHERE boutique_id = :boutique_id ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":boutique_id", $boutique_id);
$stmt->execute();
$produits = $stmt->fetchAll();
?>

<div class="page-content">
    <div class="page-header">
        <h2>Liste des produits</h2>
        <a href="create.php" class="btn">Ajouter un produit</a>
    </div>

    <?php if (isset($_GET["success"])) { ?>
        <div class="alert success-alert">
            <?php echo htmlspecialchars($_GET["success"]); ?>
        </div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom du produit</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($produits) { ?>
                <?php foreach ($produits as $produit) { ?>
                    <tr>
                        <td><?php echo $produit["id"]; ?></td>
                        <td><?php echo htmlspecialchars($produit["nom_produit"]); ?></td>
                        <td><?php echo number_format($produit["prix"], 2); ?> DH</td>
                        <td>
                            <div class="table-actions">
                                <a href="edit.php?id=<?php echo $produit["id"]; ?>" class="table-link">Modifier</a>
                                <a href="delete.php?id=<?php echo $produit["id"]; ?>"
                                   class="table-link danger-link"
                                   onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
                                   Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4">Aucun produit trouvé.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>
</body>
</html>