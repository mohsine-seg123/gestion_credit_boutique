<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Ajouter un produit au client";
$current_page = "clients";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$boutique_id = $_SESSION["boutique_id"];
$message = "";

if (!isset($_GET["client_id"]) || empty($_GET["client_id"])) {
    header("Location: ../clients/index.php");
    exit();
}

$client_id = (int) $_GET["client_id"];

$sqlClient = "SELECT * FROM clients
              WHERE id = :client_id AND boutique_id = :boutique_id
              LIMIT 1";
$stmtClient = $pdo->prepare($sqlClient);
$stmtClient->bindParam(":client_id", $client_id);
$stmtClient->bindParam(":boutique_id", $boutique_id);
$stmtClient->execute();
$client = $stmtClient->fetch();

if (!$client) {
    header("Location: ../clients/index.php");
    exit();
}

$sqlProduits = "SELECT * FROM produits
                WHERE boutique_id = :boutique_id
                ORDER BY nom_produit ASC";
$stmtProduits = $pdo->prepare($sqlProduits);
$stmtProduits->bindParam(":boutique_id", $boutique_id);
$stmtProduits->execute();
$produits = $stmtProduits->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = (int) $_POST["client_id"];
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

            $sqlInsert = "INSERT INTO client_produits
                          (client_id, produit_id, quantite, prix_unitaire, total, etat_credit)
                          VALUES
                          (:client_id, :produit_id, :quantite, :prix_unitaire, :total, 'en cours')";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(":client_id", $client_id);
            $stmtInsert->bindParam(":produit_id", $produit_id);
            $stmtInsert->bindParam(":quantite", $quantite);
            $stmtInsert->bindParam(":prix_unitaire", $prix_unitaire);
            $stmtInsert->bindParam(":total", $total);

            if ($stmtInsert->execute()) {
                header("Location: ../clients/show.php?id=" . $client_id . "&success=Produit ajouté avec succès");
                exit();
            } else {
                $message = "Erreur lors de l'ajout.";
            }
        }
    }
}
?>

<div class="page-content">
    <div class="page-header">
        <h2>Ajouter un produit au client</h2>
        <a href="../clients/show.php?id=<?php echo $client["id"]; ?>" class="btn btn-secondary">Retour</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="alert error-alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <div class="info-card">
        <p><strong>Client :</strong> <?php echo htmlspecialchars($client["nom"]); ?></p>
        <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($client["telephone"]); ?></p>
    </div>

    <form action="" method="POST" class="custom-form">
        <input type="hidden" name="client_id" value="<?php echo $client["id"]; ?>">

        <div class="form-group">
            <label for="produit_id">Produit</label>
            <select name="produit_id" id="produit_id" required>
                <option value="">-- Choisir un produit --</option>
                <?php foreach ($produits as $produit) { ?>
                    <option value="<?php echo $produit["id"]; ?>"
                        <?php if (isset($produit_id) && $produit_id == $produit["id"]) { echo "selected"; } ?>>
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
                value="<?php if (isset($quantite)) { echo htmlspecialchars($quantite); } else { echo 1; } ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>

</div>
</body>
</html>