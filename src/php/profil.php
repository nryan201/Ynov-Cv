<?php
session_start();

// Vérification si l'utilisateur est connecté sinon redirection
if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit();
}

// Connexion à la base de données
$host = '127.1.1.1';
$dbname = 'ynovcv';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

// Récupération des informations de l'utilisateur connecté
$loggedInUsername = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username='$loggedInUsername'";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$userId = $user['id'];  // On récupère l'ID de l'utilisateur pour relier les projets

$cvUploaded = false; // Variable pour contrôler l'affichage du bouton "Voir CV"

// Gestion de l'upload du CV
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv']) && !empty($_FILES['cv']['tmp_name'])) {
    // Vérification du format du fichier (seuls les PDF sont autorisés)
    $fileType = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));

    if ($fileType != "pdf") {
        $message = "Seuls les fichiers PDF sont autorisés.";
    } elseif ($_FILES["cv"]["size"] > 5000000) { // Limite de taille : 5MB
        $message = "Le fichier est trop volumineux.";
    } else {
        // Vérifiez que le fichier a bien été téléchargé
        $filename = $_FILES['cv']['tmp_name'];
        if (!empty($filename) && file_exists($filename)) {
            // Récupérer le contenu du fichier
            $cvContent = file_get_contents($filename);

            // Préparer la requête pour insérer le CV dans la base de données
            $stmt = $conn->prepare("UPDATE users SET cv = ? WHERE username = ?");
            $null = NULL;
            $stmt->bind_param('bs', $null, $loggedInUsername);  // 'b' signifie BLOB

            // Envoyer les données en bloc pour les BLOB
            $stmt->send_long_data(0, $cvContent);

            if ($stmt->execute()) {
                $message = "CV téléversé avec succès dans la base de données.";
                $cvUploaded = true; // Marquer que le CV a été téléversé
            } else {
                $message = "Erreur lors de l'enregistrement du CV : " . $conn->error;
            }

            $stmt->close();
        } else {
            $message = "Erreur : Le fichier n'a pas été téléchargé correctement.";
        }
    }
}

// Gestion de la connexion à GitHub (vérification si access_token est présent)
$projects = [];
if (isset($_SESSION['access_token'])) {
    $access_token = $_SESSION['access_token'];
    // Appel à l'API GitHub pour obtenir les dépôts, si nécessaire
}

// Gestion de la mise à jour du statut et du titre du poste
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = isset($_POST['status']) ? 1 : 0;  // Si la case est cochée, 1, sinon 0
    if ($status == 0) {
        $job_title = ''; // Réinitialiser le champ job_title si le profil est privé
    } else {
        // Sinon, récupérer le poste recherché depuis le formulaire
        $job_title = $conn->real_escape_string($_POST['job_title']);
    }

    $updateQuery = "UPDATE users SET status='$status', job_title='$job_title' WHERE username='$loggedInUsername'";

    if ($conn->query($updateQuery) === TRUE) {
        $message = "Votre profil a été mis à jour.";
    } else {
        $message = "Erreur lors de la mise à jour : " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="../css/profil.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<!-- Bouton retour -->
<button class="back-button" onclick="window.location.href='../../index.php';">Retour</button>



<div class="profile-wrapper">
    <!-- Affichage des informations utilisateur -->
    <div class="info-section">
        <div class="info-item"><span id="username">Username :</span><p><?php echo $user['username']; ?></p></div>
        <div class="info-item"><span id="firstname">Firstname :</span><p><?php echo $user['firstname']; ?></p></div>
        <div class="info-item"><span id="name">Name :</span><p><?php echo $user['name']; ?></p></div>
        <div class="info-item"><span id="birthdate-section">Birthdate :</span><p><?php echo $user['birthday']; ?></p></div>
        <div class="info-item"><span id="email">Mail :</span><p><?php echo $user['email']; ?></p></div>
    </div>

    <!-- Section pour téléverser le CV -->
    <div class="upload-cv">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="cv-upload">Upload CV (PDF)</label>
            <input type="file" id="cv-upload" name="cv" accept="application/pdf">
            <button type="submit" class="btn-upload">Upload</button>
        </form>
    </div>

    <!-- Formulaire pour définir le statut du profil et le poste recherché -->
    <div class="update-section">
        <form action="" method="post">
            <label for="job_title">Poste recherché :</label>
            <input type="text" id="job_title" name="job_title" value="<?php echo $user['job_title']; ?>" required>

            <label for="status">Rendre le profil public :</label>
            <!-- La case est cochée si le statut est déjà public (status = 1) -->
            <input type="checkbox" id="status" name="status" <?php if ($user['status'] == 1) echo 'checked'; ?>>

            <button type="submit" class="btn-update">Mettre à jour</button>
        </form>
    </div>


    <!-- Affichage du bouton pour voir le CV si un CV est présent ou s'il a été téléversé avec succès -->
    <?php if ($user['cv'] || $cvUploaded) { ?>
        <div class="action-buttons">
            <form action="view_cv.php" method="post">
                <button type="submit" class="view_cv">Voir le CV</button>
            </form>

            <form action="projects.php" method="post">
                <button type="submit" class="view_projects">Voir les projets</button>
            </form>
        </div>
    <?php } ?>

    <!-- Affichage du bouton de connexion à GitHub si pas connecté à GitHub -->
    <?php if (!isset($_SESSION['access_token'])) { ?>
        <div class="github-connection">
            <form action="https://github.com/login/oauth/authorize">
                <input type="hidden" name="client_id" value="Ov23liS5tgqRiNYv8dE0">
                <input type="hidden" name="scope" value="user:read">
                <button type="submit" class="btn-github">Se connecter à GitHub</button>
            </form>
        </div>
    <?php } ?>


    <!-- Message d'erreur ou de succès -->
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

</div>

</body>
</html>
