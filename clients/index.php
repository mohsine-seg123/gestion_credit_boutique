

<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Clients";
$current_page = "clients";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$boutique_id = $_SESSION["boutique_id"];

$sql = "SELECT clients.*,
               COALESCE(SUM(CASE WHEN client_produits.etat_credit = 'en cours' THEN client_produits.total ELSE 0 END), 0) AS total_credit
        FROM clients
        LEFT JOIN client_produits ON clients.id = client_produits.client_id
        WHERE clients.boutique_id = :boutique_id
        GROUP BY clients.id, clients.nom, clients.telephone, clients.adresse, clients.created_at
        ORDER BY clients.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":boutique_id", $boutique_id);
$stmt->execute();
$clients = $stmt->fetchAll();
?>


<div class="page-content">
    <div class="page-header">
        <h2>Liste des clients</h2>
        <a href="create.php" class="btn">Ajouter un client</a>
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
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Total crédit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($clients) { ?>
                <?php foreach ($clients as $client) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($client["id"]); ?></td>
                        <td><?php echo htmlspecialchars($client["nom"]); ?></td>
                        <td><?php echo htmlspecialchars($client["telephone"]); ?></td>
                        <td><?php echo htmlspecialchars($client["adresse"]); ?></td>
                        <td><?php echo number_format($client["total_credit"], 2); ?> DH</td>
                        <td>
                            <div class="table-actions">
                                <a href="show.php?id=<?php echo $client["id"]; ?>" class="table-link">Voir</a>
                                <a href="edit.php?id=<?php echo $client["id"]; ?>" class="table-link">Modifier</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6">Aucun client trouvé.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>
</body>
</html>