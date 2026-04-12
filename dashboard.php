<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: auth/login.php");
    exit();
}

$page_title = "Dashboard";
$current_page = "dashboard";
$pathPrefix = ".";
$use_layout = true;

require "config/db.php";
require "includes/header.php";
require "includes/sidebar.php";
require "includes/navbar.php";

$boutique_id = $_SESSION["boutique_id"];

/* Total clients */
$sqlClients = "SELECT COUNT(*) AS total_clients
               FROM clients
               WHERE boutique_id = :boutique_id";
$stmtClients = $pdo->prepare($sqlClients);
$stmtClients->bindParam(":boutique_id", $boutique_id);
$stmtClients->execute();
$total_clients = $stmtClients->fetch()["total_clients"];


/* Total produits */
$sqlProduits = "SELECT COUNT(*) AS total_produits
                FROM produits
                WHERE boutique_id = :boutique_id";
$stmtProduits = $pdo->prepare($sqlProduits);
$stmtProduits->bindParam(":boutique_id", $boutique_id);
$stmtProduits->execute();
$total_produits = $stmtProduits->fetch()["total_produits"];


/* Crédits en cours */
$sqlCredits = "SELECT COUNT(*) AS credits_en_cours
               FROM client_produits cp
               INNER JOIN clients c ON cp.client_id = c.id
               WHERE c.boutique_id = :boutique_id
               AND cp.etat_credit = 'en cours'";
$stmtCredits = $pdo->prepare($sqlCredits);
$stmtCredits->bindParam(":boutique_id", $boutique_id);
$stmtCredits->execute();
$credits_en_cours = $stmtCredits->fetch()["credits_en_cours"];


/* Montant total crédits en cours */
$sqlMontant = "SELECT COALESCE(SUM(cp.total), 0) AS montant_total
               FROM client_produits cp
               INNER JOIN clients c ON cp.client_id = c.id
               WHERE c.boutique_id = :boutique_id
               AND cp.etat_credit = 'en cours'";
$stmtMontant = $pdo->prepare($sqlMontant);
$stmtMontant->bindParam(":boutique_id", $boutique_id);
$stmtMontant->execute();
$montant_total = $stmtMontant->fetch()["montant_total"];

/* Derniers clients */
$sqlDerniersClients = "SELECT id, nom, telephone, adresse, created_at
                       FROM clients
                       WHERE boutique_id = :boutique_id
                       ORDER BY id DESC
                       LIMIT 5";
$stmtDerniersClients = $pdo->prepare($sqlDerniersClients);
$stmtDerniersClients->bindParam(":boutique_id", $boutique_id);
$stmtDerniersClients->execute();
$derniers_clients = $stmtDerniersClients->fetchAll();

/* Dernières opérations */
$sqlOperations = "SELECT cp.id, cp.quantite, cp.total, cp.etat_credit, cp.date_ajout,
                         c.nom AS nom_client,
                         p.nom_produit
                  FROM client_produits cp
                  INNER JOIN clients c ON cp.client_id = c.id
                  INNER JOIN produits p ON cp.produit_id = p.id
                  WHERE c.boutique_id = :boutique_id
                  ORDER BY cp.id DESC
                  LIMIT 5";
$stmtOperations = $pdo->prepare($sqlOperations);
$stmtOperations->bindParam(":boutique_id", $boutique_id);
$stmtOperations->execute();
$operations = $stmtOperations->fetchAll();
?>

<div class="page-content">
    <div class="page-header">
        <div>
            <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION["admin_nom"]); ?></h2>
            <p>Résumé général de votre boutique.</p>
        </div>
    </div>

    <div class="card-container">
        <div class="card">
            <h3>Total clients</h3>
            <p><?php echo $total_clients; ?></p>
        </div>

        <div class="card">
            <h3>Total produits</h3>
            <p><?php echo $total_produits; ?></p>
        </div>

        <div class="card">
            <h3>Crédits en cours</h3>
            <p><?php echo $credits_en_cours; ?></p>
        </div>

        <div class="card">
            <h3>Montant total</h3>
            <p><?php echo number_format($montant_total, 2); ?> DH</p>
        </div>
    </div>

    <div class="dashboard-section">
        <div class="page-header">
            <h2>Derniers clients</h2>
            <a href="clients/index.php" class="btn btn-secondary">Voir tout</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($derniers_clients) { ?>
                    <?php foreach ($derniers_clients as $client) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client["nom"]); ?></td>
                            <td><?php echo htmlspecialchars($client["telephone"]); ?></td>
                            <td><?php echo htmlspecialchars($client["adresse"]); ?></td>
                            <td><?php echo htmlspecialchars($client["created_at"]); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4">Aucun client trouvé.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="dashboard-section">
        <div class="page-header">
            <h2>Dernières opérations</h2>
            <a href="clients/index.php" class="btn btn-secondary">Voir clients</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>État</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($operations) { ?>
                    <?php foreach ($operations as $operation) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($operation["nom_client"]); ?></td>
                            <td><?php echo htmlspecialchars($operation["nom_produit"]); ?></td>
                            <td><?php echo htmlspecialchars($operation["quantite"]); ?></td>
                            <td><?php echo number_format($operation["total"], 2); ?> DH</td>
                            <td>
                                <?php if ($operation["etat_credit"] == "regle") { ?>
                                    <span class="status-badge status-paid">Réglé</span>
                                <?php } else { ?>
                                    <span class="status-badge status-pending">En cours</span>
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($operation["date_ajout"]); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">Aucune opération trouvée.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</body>
</html>