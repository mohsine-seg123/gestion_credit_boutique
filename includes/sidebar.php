<?php
if (!isset($current_page)) {
    $current_page = "";
}
?>

<div class="sidebar">
    <div class="sidebar-brand">
        <h2>Ma Boutique</h2>
        <p>Gestion des credits</p>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo $pathPrefix; ?>/dashboard.php"
           class="sidebar-link <?php if ($current_page == "dashboard") { echo 'active'; } ?>">
           Dashboard
        </a>

        <a href="<?php echo $pathPrefix; ?>/clients/index.php"
           class="sidebar-link <?php if ($current_page == "clients") { echo 'active'; } ?>">
           Clients
        </a>

        <a href="<?php echo $pathPrefix; ?>/produits/index.php"
           class="sidebar-link <?php if ($current_page == "produits") { echo 'active'; } ?>">
           Produits
        </a>
    </nav>

    <a href="<?php echo $pathPrefix; ?>/auth/logout.php" class="sidebar-link logout-link">
       Déconnexion
    </a>
</div>