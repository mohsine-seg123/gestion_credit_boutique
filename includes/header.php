<?php
if (!isset($page_title)) {
    $page_title = "Gestion des crédits";
}

if (!isset($pathPrefix)) {
    $pathPrefix = ".";
}

if (!isset($use_layout)) {
    $use_layout = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="<?php echo $pathPrefix; ?>/style.css">
</head>
<body class="<?php echo $use_layout ? 'app-body' : 'auth-body'; ?>">
<div class="layout">