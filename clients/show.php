<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Détail du client";
$current_page = "clients";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$boutique_id = $_SESSION["boutique_id"];

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET["id"];

$sqlClient = "SELECT * FROM clients WHERE id = :id AND boutique_id = :boutique_id LIMIT 1";
$stmtClient = $pdo->prepare($sqlClient);
$stmtClient->bindParam(":id", $id);
$stmtClient->bindParam(":boutique_id", $boutique_id);
$stmtClient->execute();
$client = $stmtClient->fetch();

if (!$client) {
    header("Location: index.php");
    exit();
}

$sqlTotal = "SELECT COALESCE(SUM(total), 0) AS total_credit
             FROM client_produits
             WHERE client_id = :client_id AND etat_credit = 'en cours'";
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->bindParam(":client_id", $id);
$stmtTotal->execute();


$totalData = $stmtTotal->fetch();
$total_credit = $totalData["total_credit"];

$sqlHistorique = "SELECT client_produits.*, produits.nom_produit
                  FROM client_produits
                  INNER JOIN produits ON client_produits.produit_id = produits.id
                  WHERE client_produits.client_id = :client_id
                  ORDER BY client_produits.id DESC";
$stmtHistorique = $pdo->prepare($sqlHistorique);
$stmtHistorique->bindParam(":client_id", $id);
$stmtHistorique->execute();
$historique = $stmtHistorique->fetchAll();
?>

<div class="page-content">
    <div class="page-header">
        <h2>Détail du client</h2>
        <div class="action-group">
            <a href="index.php" class="btn btn-secondary">Retour</a>
            <a href="../client_produits/create.php?client_id=<?php echo $client["id"]; ?>" class="btn">
                Ajouter un produit
            </a>
        </div>
    </div>

    <div class="info-card">
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($client["nom"]); ?></p>
        <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($client["telephone"]); ?></p>
        <p><strong>Adresse :</strong> <?php echo htmlspecialchars($client["adresse"]); ?></p>
        <p><strong>Total crédit en cours :</strong> <?php echo number_format($total_credit, 2); ?> DH</p>
    </div>

    <h3>Historique des produits</h3>

    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
                <th>État</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($historique) { ?>
                <?php foreach ($historique as $ligne) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ligne["nom_produit"]); ?></td>
                        <td><?php echo htmlspecialchars($ligne["quantite"]); ?></td>
                        <td><?php echo number_format($ligne["prix_unitaire"], 2); ?> DH</td>
                        <td><?php echo number_format($ligne["total"], 2); ?> DH</td>
                        <td>
                            <?php if ($ligne["etat_credit"] == "regle") { ?>
                                <span class="status-badge status-paid">Réglé</span>
                            <?php } else { ?>
                                <span class="status-badge status-pending">En cours</span>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($ligne["date_ajout"]); ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="../client_produits/edit.php?id=<?php echo $ligne["id"]; ?>" class="table-link">Modifier</a>
                                <a href="../client_produits/delete.php?id=<?php echo $ligne["id"]; ?>" class="table-link danger-link" onclick="return confirm('Voulez-vous vraiment supprimer cette ligne ?');">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="7">Aucun produit lié à ce client.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>
</body>
</html>