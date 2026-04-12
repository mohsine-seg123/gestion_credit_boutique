<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Modifier un client";
$current_page = "clients";
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

$nom = $client["nom"];
$telephone = $client["telephone"];
$adresse = $client["adresse"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $telephone = trim($_POST["telephone"]);
    $adresse = trim($_POST["adresse"]);

    if (empty($nom) || empty($telephone) || empty($adresse)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        $sqlUpdate = "UPDATE clients
                      SET nom = :nom, telephone = :telephone, adresse = :adresse
                      WHERE id = :id AND boutique_id = :boutique_id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(":nom", $nom);
        $stmtUpdate->bindParam(":telephone", $telephone);
        $stmtUpdate->bindParam(":adresse", $adresse);
        $stmtUpdate->bindParam(":id", $id);
        $stmtUpdate->bindParam(":boutique_id", $boutique_id);

        if ($stmtUpdate->execute()) {
            header("Location: index.php?success=Client modifié avec succès");
            exit();
        } else {
            $message = "Erreur lors de la modification.";
        }
    }
}
?>

<div class="page-content">
    <div class="page-header">
        <h2>Modifier un client</h2>
        <a href="index.php" class="btn btn-secondary">Retour</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="alert error-alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form action="" method="POST" class="custom-form">
        <div class="form-group">
            <label for="nom">Nom du client</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>" required>
        </div>

        <div class="form-group">
            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($adresse); ?>" required>
        </div>

        <button type="submit" class="btn">Mettre à jour</button>
    </form>
</div>

</div>
</body>
</html>