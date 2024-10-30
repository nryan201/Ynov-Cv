<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
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

// Récupérer le CV de l'utilisateur connecté
$loggedInUsername = $_SESSION['username'];
$query = "SELECT cv FROM users WHERE username='$loggedInUsername'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

// Si le CV est présent dans la base de données
if ($user && !empty($user['cv'])) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="cv.pdf"'); // Inline pour afficher dans le navigateur
    echo $user['cv'];
} else {
    echo "Aucun CV disponible.";
}

$conn->close();
?>

