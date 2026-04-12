<?php
session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: ../dashboard.php");
    exit();
}

$page_title = "Connexion";
$pathPrefix = "..";
$use_layout = false;

require "../config/db.php";
require "../includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);

    if (empty($email) || empty($mot_de_passe)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        try {
            $sql = "SELECT admins.*, boutiques.nom_boutique
                    FROM admins
                    INNER JOIN boutiques ON admins.boutique_id = boutiques.id
                    WHERE admins.email = :email
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $admin = $stmt->fetch();

            if ($admin) {
                $mot_de_passe_valide = false;

                if (password_verify($mot_de_passe, $admin["mot_de_passe"])) {
                    $mot_de_passe_valide = true;
                } elseif ($mot_de_passe == $admin["mot_de_passe"]) {
                    $mot_de_passe_valide = true;

                    // Migration automatique si ancien mot de passe stocké en clair
                    $nouveau_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                    $sqlUpdate = "UPDATE admins SET mot_de_passe = :mot_de_passe WHERE id = :id";
                    $stmtUpdate = $pdo->prepare($sqlUpdate);
                    $stmtUpdate->bindParam(":mot_de_passe", $nouveau_hash);
                    $stmtUpdate->bindParam(":id", $admin["id"]);
                    $stmtUpdate->execute();
                }

                if ($mot_de_passe_valide) {
                    $_SESSION["admin_id"] = $admin["id"];
                    $_SESSION["admin_nom"] = $admin["nom"];
                    $_SESSION["admin_email"] = $admin["email"];
                    $_SESSION["boutique_id"] = $admin["boutique_id"];
                    $_SESSION["nom_boutique"] = $admin["nom_boutique"];

                    header("Location: ../dashboard.php");
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            } else {
                $message = "Aucun compte trouvé avec cet email.";
            }
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-left">
            <img src="../images/login-shop.jpg" alt="Boutique">
            <div class="auth-overlay">
                <h2>Gérez votre boutique simplement</h2>
                <p>Suivi des clients, produits et crédits dans une interface claire et professionnelle.</p>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-box">
                <h1>Connexion Admin</h1>
                <p class="auth-subtitle">Connectez-vous pour accéder à votre tableau de bord.</p>

                <?php if (!empty($message)) { ?>
                    <div class="auth-message auth-error">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } ?>

                <form action="" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Entrer votre email"
                            value="<?php if (isset($email)) { echo htmlspecialchars($email); } ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe</label>
                        <input
                            type="password"
                            id="mot_de_passe"
                            name="mot_de_passe"
                            placeholder="Entrer votre mot de passe"
                            required
                        >
                    </div>

                    <button type="submit" class="auth-btn">Se connecter</button>
                </form>

                <p class="auth-switch">
                    Vous n'avez pas encore de compte ?
                    <a href="register.php">Créer une boutique</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>