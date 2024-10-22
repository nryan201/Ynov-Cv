<?php
session_start(); // Démarrage de la session pour vérifier si l'utilisateur est connecté
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="src/css/index.css"> <!-- Fichier CSS global -->
</head>
<body>

<!-- Menu en haut -->
<header class="navbar">
    <div class="nav-left">
        <?php if (isset($_SESSION['username'])): ?>
            <a href="src/php/profil.php" class="nav-link">Profil</a>
            <a href="src/php/projects.php" class="nav-link">Voir les Projets</a>
        <?php else: ?>
            <a href="src/php/login.php" class="nav-link">Se connecter</a>
        <?php endif; ?>
        <!-- Le lien vers les profils publics est accessible à tous -->
        <a href="src/php/public_profiles.php" class="nav-link">Profils Publics</a>
    </div>
    <div class="nav-right">
        <a href="contact.php" class="nav-link">Contact</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="src/php/logout.php" class="nav-link">Déconnexion</a>
        <?php endif; ?>
    </div>
</header>

<!-- Conteneur principal de la page -->
<div class="main-container">

    <!-- Section de bienvenue -->
    <div class="welcome-section">
        <h1>CV MAKER</h1>
        <p>Faites votre CV et montrez vos expériences personnelles au monde entier</p>
    </div>

</div>
</body>
</html>
