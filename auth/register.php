
<?php
session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: ../dashboard.php");
    exit();
}

$page_title = "Inscription";
$pathPrefix = "..";
$use_layout = false;

require "../config/db.php";
require "../includes/header.php";

$message = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_boutique = trim($_POST["nom_boutique"]);
    $telephone_boutique = trim($_POST["telephone_boutique"]);
    $adresse_boutique = trim($_POST["adresse_boutique"]);

    $nom_admin = trim($_POST["nom_admin"]);
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);
    $confirmation = trim($_POST["confirmation"]);

    if (
        empty($nom_boutique) ||
        empty($telephone_boutique) ||
        empty($adresse_boutique) ||
        empty($nom_admin) ||
        empty($email) ||
        empty($mot_de_passe) ||
        empty($confirmation)
    ) {
        $message = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse email invalide.";
    } elseif ($mot_de_passe != $confirmation) {
        $message = "La confirmation du mot de passe est incorrecte.";
    } elseif (strlen($mot_de_passe) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        try {
            $sqlCheck = "SELECT id FROM admins WHERE email = :email LIMIT 1";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(":email", $email);
            $stmtCheck->execute();

            if ($stmtCheck->fetch()) {
                $message = "Cet email est déjà utilisé.";
            } else {
                $pdo->beginTransaction();

                $sqlBoutique = "INSERT INTO boutiques (nom_boutique, adresse, telephone)
                                VALUES (:nom_boutique, :adresse, :telephone)";
                $stmtBoutique = $pdo->prepare($sqlBoutique);
                $stmtBoutique->bindParam(":nom_boutique", $nom_boutique);
                $stmtBoutique->bindParam(":adresse", $adresse_boutique);
                $stmtBoutique->bindParam(":telephone", $telephone_boutique);
                $stmtBoutique->execute();

                $boutique_id = $pdo->lastInsertId();

                $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                $sqlAdmin = "INSERT INTO admins (boutique_id, nom, email, mot_de_passe)
                             VALUES (:boutique_id, :nom, :email, :mot_de_passe)";
                $stmtAdmin = $pdo->prepare($sqlAdmin);
                $stmtAdmin->bindParam(":boutique_id", $boutique_id);
                $stmtAdmin->bindParam(":nom", $nom_admin);
                $stmtAdmin->bindParam(":email", $email);
                $stmtAdmin->bindParam(":mot_de_passe", $mot_de_passe_hache);
                $stmtAdmin->execute();

                $pdo->commit();

                $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-wrapper auth-wrapper-register">
        <div class="auth-left">
            <img src="../images/login-shop.jpg" alt="Boutique">
            <div class="auth-overlay">
                <h2>Créez votre espace boutique</h2>
                <p>Inscrivez votre boutique et commencez à gérer vos clients, produits et crédits.</p>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-box">
                <h1>Créer un compte</h1>
                <p class="auth-subtitle">Un seul admin par boutique.</p>

                <?php if (!empty($message)) { ?>
                    <div class="auth-message auth-error">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } ?>

                <?php if (!empty($success)) { ?>
                    <div class="auth-message auth-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php } ?>

                <form action="/dashboard.php" method="POST" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom_boutique">Nom de la boutique</label>
                            <input
                                type="text"
                                id="nom_boutique"
                                name="nom_boutique"
                                placeholder="Ex : Boutique Atlas"
                                value="<?php if (isset($nom_boutique)) { echo htmlspecialchars($nom_boutique); } ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="telephone_boutique">Téléphone</label>
                            <input
                                type="text"
                                id="telephone_boutique"
                                name="telephone_boutique"
                                placeholder="Ex : 0612345678"
                                value="<?php if (isset($telephone_boutique)) { echo htmlspecialchars($telephone_boutique); } ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adresse_boutique">Adresse de la boutique</label>
                        <input
                            type="text"
                            id="adresse_boutique"
                            name="adresse_boutique"
                            placeholder="Ex : Fès, centre ville"
                            value="<?php if (isset($adresse_boutique)) { echo htmlspecialchars($adresse_boutique); } ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="nom_admin">Nom de l’admin</label>
                        <input
                            type="text"
                            id="nom_admin"
                            name="nom_admin"
                            placeholder="Entrer votre nom"
                            value="<?php if (isset($nom_admin)) { echo htmlspecialchars($nom_admin); } ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email admin</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Entrer votre email"
                            value="<?php if (isset($email)) { echo htmlspecialchars($email); } ?>"
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mot_de_passe">Mot de passe</label>
                            <input
                                type="password"
                                id="mot_de_passe"
                                name="mot_de_passe"
                                placeholder="Minimum 6 caractères"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="confirmation">Confirmation</label>
                            <input
                                type="password"
                                id="confirmation"
                                name="confirmation"
                                placeholder="Confirmer le mot de passe"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Créer le compte</button>
                </form>

                <p class="auth-switch">
                    Vous avez déjà un compte ?
                    <a href="login.php">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>