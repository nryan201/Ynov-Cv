<?php
session_start();

// Vérification si l'utilisateur est connecté sinon redirection
if (!isset($_SESSION['username'])) {
    header('Location: /');
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
$userId = $user['id'];

// Récupération des projets de l'utilisateur
$projectsQuery = "SELECT * FROM projects WHERE user_id = '$userId'";
$projectsResult = $conn->query($projectsQuery);
$projects = [];

if ($projectsResult->num_rows > 0) {
    while ($row = $projectsResult->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Gestion du formulaire pour ajouter un projet manuellement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    $project_name = $conn->real_escape_string($_POST['project_name']);
    $project_description = $conn->real_escape_string($_POST['project_description']);
    $project_url = $conn->real_escape_string($_POST['project_url']);

    $insertProjectQuery = "INSERT INTO projects (user_id, name, url, description) 
                           VALUES ('$userId', '$project_name', '$project_url', '$project_description')";

    if ($conn->query($insertProjectQuery) === TRUE) {
        $message = "Projet ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout du projet : " . $conn->error;
    }

    // Rafraîchir la page après l'insertion pour mettre à jour la liste des projets
    header('Location: projects.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets</title>
    <link rel="stylesheet" href="src/css/projects.css">
</head>
<body>

<!-- Bouton retour -->
<button class="back-button" onclick="window.location.href='/profil';">Retour</button>

<div class="main-content">
    <h2>Mes Projets</h2>

    <div class="second-content"
    <!-- Section des projets GitHub -->
    <?php if (!empty($projects)) { ?>
        <div class="project-list">
            <ul>
                <?php foreach ($projects as $project) { ?>
                    <li>
                        <h3><?php echo $project['name']; ?></h3>
                        <p><?php echo $project['description']; ?></p>
                        <?php if (!empty($project['url'])) { ?>
                            <a href="<?php echo $project['url']; ?>" target="_blank">Voir le projet</a>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } else { ?>
        <p>Aucun projet trouvé.</p>
    <?php } ?>

    <!-- Formulaire pour ajouter un nouveau projet -->
    <div class="add-project-form">
        <h3>Ajouter un projet</h3>
        <form action="" method="post">
            <label for="project_name">Nom du projet</label>
            <input type="text" id="project_name" name="project_name" required>

            <label for="project_description">Description du projet</label>
            <textarea id="project_description" name="project_description" required></textarea>

            <label for="project_url">URL du projet (optionnel)</label>
            <input type="url" id="project_url" name="project_url" placeholder="https://...">

            <button type="submit" name="add_project" class="add-btn">Ajouter</button>
        </form>
    </div>
    </div>
</div>

<!-- Message d'erreur ou de succès -->
<?php if (isset($message)) { echo "<p>$message</p>"; } ?>

<!-- Bouton pour afficher le formulaire d'ajout de projet -->
<div class="add-project-button">+</div>

</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addButton = document.querySelector('.add-project-button');
        const form = document.querySelector('.add-project-form');

        addButton.addEventListener('click', function () {
            form.classList.toggle('active');
        });
    });

</script>
</html>
