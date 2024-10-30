<?php
session_start();

// Connexion à la base de données
$host = '127.1.1.1';
$dbname = 'ynovcv';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

// Récupérer le nom et prénom de l'utilisateur à partir de l'URL
$userName = isset($_GET['user']) ? $_GET['user'] : '';
// Séparer le prénom et le nom
list($firstname, $lastname) = explode(' ', $userName);

// Récupérer les détails de l'utilisateur
$query = "SELECT * FROM users WHERE firstname = '$firstname' AND name = '$lastname'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Profil</title>
    <link rel="stylesheet" href="src/css/profile_detail.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>

<button class="back-button" onclick="window.location.href='/';">Retour</button>
<div class="profile-wrapper">
    <h2>Détails de <?php echo $user['firstname'] . ' ' . $user['name']; ?></h2>
    <div class="info-section">
        <p><strong>Date de Naissance :</strong> <?php echo $user['birthday']; ?></p>
        <p><strong>Email :</strong> <?php echo $user['email']; ?></p>
        <p><strong>Poste recherché :</strong> <?php echo $user['job_title']; ?></p>
        <p><strong>Status :</strong> <?php echo $user['status'] ? 'Public' : 'Privé'; ?></p>
    </div>
    <?php if ($user['cv']) { ?>
        <a href="../pdf/download_cv.php?user=<?php echo $user['firstname'] . ' ' . $user['name']; ?>" class="btn-download">Télécharger le CV</a>
    <?php } ?>
</div>

</body>
</html>
