<?php
// Connexion à la base de données
$host = '127.1.1.1';
$dbname = 'ynovcv';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

// Récupérer les profils publics
$query = "SELECT firstname, name, job_title, cv, birthday, email FROM users WHERE status = 1"; // Sélectionner firstname et lastname
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chercheurs d'Emplois</title>
    <link rel="stylesheet" href="src/css/public_profiles.css"> <!-- Lien vers votre fichier CSS -->
</head>
<body>

<!-- Bouton retour -->
<button class="back-button" onclick="window.location.href='/';">Retour</button>



<h2>Chercheurs D'Emplois</h2>

<div class="profiles-list">
    <?php while ($user = $result->fetch_assoc()) { ?>
        <div class="profile-card" onclick="window.location.href='profile_detail.php?user=<?php echo $user['firstname'] . ' ' . $user['name']; ?>'">
            <h3><?php echo $user['firstname'] . ' ' . $user['name']; ?></h3> <!-- Afficher le prénom et le nom -->
            <p>Date : <?php echo $user['birthday']; ?></p>
            <p>Email : <?php echo $user['email']; ?></p>
            <p>Poste recherché : <?php echo $user['job_title']; ?></p>
            <?php if ($user['cv']) { ?>
                <a href="../pdf/download_cv.php?user=<?php echo $user['firstname'] . ' ' . $user['name']; ?>">Télécharger le CV</a>
            <?php } ?>
        </div>
    <?php } ?>
</div>

</body>
</html>
