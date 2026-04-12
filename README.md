# Gestion des crédits d'une boutique

Application web développée en **PHP** et **MySQL** pour gérer les crédits clients d'une boutique.

Le projet permet à un **admin de boutique** de :
- gérer ses clients,
- gérer ses produits,
- ajouter des produits à crédit pour un client,
- modifier ou supprimer les lignes de crédit,
- suivre l'état du crédit (**en cours** / **réglé**),
- consulter un tableau de bord avec les statistiques principales.

---

## Fonctionnalités principales

### Authentification
- Inscription d'une boutique avec un seul admin.
- Connexion / déconnexion.
- Protection des pages par session.

### Gestion des clients
- Ajouter un client.
- Modifier un client.
- Afficher la liste des clients.
- Voir le détail d'un client.

### Gestion des produits
- Ajouter un produit.
- Modifier un produit.
- Supprimer un produit.
- Afficher la liste des produits.

### Gestion des crédits clients
- Ajouter un produit à un client.
- Calcul automatique du prix unitaire et du total.
- Modifier une ligne de crédit.
- Supprimer une ligne de crédit.
- Changer l'état du crédit :
  - `en cours`
  - `regle`

### Dashboard
- Nombre total de clients.
- Nombre total de produits.
- Nombre total de crédits en cours.
- Montant total des crédits en cours.
- Derniers clients ajoutés.
- Dernières opérations effectuées.

---

## Technologies utilisées

- **PHP**
- **MySQL**
- **PDO** pour la connexion à la base de données
- **HTML / CSS**
- **XAMPP** en local

---

## Structure du projet

```bash
project/
├── config/
│   └── db.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── clients/
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── show.php
├── produits/
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── delete.php
├── client_produits/
│   ├── create.php
│   ├── edit.php
│   ├── delete.php
│   └── update_etat.php
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── sidebar.php
├── images/
├── style.css
└── dashboard.php
```

---

## Modèle de données

Le projet repose sur les tables suivantes :

### `boutiques`
Contient les informations de la boutique.

### `admins`
Contient les informations de l'administrateur lié à une boutique.

### `clients`
Contient les clients de la boutique.

### `produits`
Contient les produits de la boutique.

### `client_produits`
Table de liaison entre les clients et les produits.
Elle contient :
- la quantité,
- le prix unitaire,
- le total,
- l'état du crédit,
- la date d'ajout.

---

## Installation en local

### 1. Cloner ou copier le projet
Place le dossier dans le répertoire `htdocs` de XAMPP.

Exemple :

```bash
C:/xampp/htdocs/project
```

### 2. Démarrer XAMPP
Lancer :
- **Apache**
- **MySQL**

### 3. Créer la base de données
Créer une base de données MySQL, par exemple :

```sql
CREATE DATABASE boutique;
```

### 4. Importer les tables
Importer ensuite le script SQL contenant les tables :
- `boutiques`
- `admins`
- `clients`
- `produits`
- `client_produits`

### 5. Configurer la connexion
Modifier le fichier `config/db.php` :

```php
<?php
$host = "localhost";
$dbname = "boutique";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
```

### 6. Lancer le projet
Ouvrir dans le navigateur :

```text
http://localhost/project/auth/login.php
```

---

## Déploiement en ligne

Le projet peut être hébergé sur un hébergement PHP/MySQL gratuit comme **InfinityFree**.

### Étapes générales
1. Créer un compte d'hébergement.
2. Créer une base MySQL.
3. Importer les tables via phpMyAdmin.
4. Modifier `config/db.php` avec les identifiants de production.
5. Uploader les fichiers du projet.

Exemple de configuration de production :

```php
<?php
$host = "sqlXXX.infinityfree.com";
$dbname = "if0_xxxxxxxx_nomdelabase";
$username = "if0_xxxxxxxx";
$password = "mot_de_passe_mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
```

---

## Sécurité

Dans le projet :
- les pages sont protégées par session,
- les requêtes utilisent **PDO préparé**,
- les mots de passe admin sont hachés avec `password_hash()`.

Améliorations possibles :
- validation plus stricte des formulaires,
- protection CSRF,
- pagination,
- recherche dynamique,
- suppression soft delete,
- gestion du stock.

---

## Évolutions possibles

- Ajouter une recherche clients / produits.
- Ajouter des statistiques plus avancées.
- Ajouter un système de stock.
- Export PDF / Excel.
- Version multi-boutiques plus avancée.
- Amélioration du design responsive.

---

## Auteur

**Abdellah EL GHENNAMI**  
Étudiant en ingénierie logicielle et intelligence artificielle.

---

## Remarque

Ce projet a été réalisé dans un cadre d'apprentissage pour pratiquer :
- PHP,
- MySQL,
- PDO,
- les formulaires,
- les sessions,
- l'organisation d'un mini projet web complet.
