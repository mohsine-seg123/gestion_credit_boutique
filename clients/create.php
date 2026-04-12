<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = "Ajouter un client";
$current_page = "clients";
$pathPrefix = "..";
$use_layout = true;

require_once "../config/db.php";
require_once "../includes/header.php";
require_once "../includes/sidebar.php";
require_once "../includes/navbar.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $telephone = trim($_POST["telephone"]);
    $adresse = trim($_POST["adresse"]);
    $boutique_id = $_SESSION["boutique_id"];

    if (empty($nom) || empty($telephone) || empty($adresse)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        $sql = "INSERT INTO clients (boutique_id, nom, telephone, adresse)
                VALUES (:boutique_id, :nom, :telephone, :adresse)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":boutique_id", $boutique_id);
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":telephone", $telephone);
        $stmt->bindParam(":adresse", $adresse);

        if ($stmt->execute()) {
            header("Location: index.php?success=Client ajouté avec succès");
            exit();
        } else {
            $message = "Erreur lors de l'ajout du client.";
        }
    }
}
?>

<div class="page-content">
    <div class="page-header">
        <h2>Ajouter un client</h2>
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
            <input
                type="text"
                id="nom"
                name="nom"
                value="<?php if (isset($nom)) { echo htmlspecialchars($nom); } ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input
                type="text"
                id="telephone"
                name="telephone"
                value="<?php if (isset($telephone)) { echo htmlspecialchars($telephone); } ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="adresse">Adresse</label>
            <input
                type="text"
                id="adresse"
                name="adresse"
                value="<?php if (isset($adresse)) { echo htmlspecialchars($adresse); } ?>"
                required
            >
        </div>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>

</div>
</body>
</html>