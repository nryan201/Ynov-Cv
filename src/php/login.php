<?php
session_start(); // Démarrer la session pour utiliser les variables de session

// Connexion à la base de données
$host = '127.1.1.1';
$dbname = 'ynovcv';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

$message = "";

// Gestion de la connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['username'] = $user['username']; // Stocker le nom d'utilisateur dans la session
            header('Location: ../../index.php'); // Rediriger vers la page profil
            exit(); // Assurez-vous d'arrêter le script après la redirection
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Aucun compte trouvé avec ce nom d'utilisateur.";
    }
}
// Gestion de l'inscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $firstname = $conn->real_escape_string($_POST['firstname']); // Nouveau champ pour le prénom
    $name = $conn->real_escape_string($_POST['name']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_BCRYPT);

    // Vérification si l'utilisateur existe déjà
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $message = "Un utilisateur avec cet email existe déjà.";
    } else {
        // Insertion de l'utilisateur avec le prénom dans la base de données
        $query = "INSERT INTO users (id, username, name, firstname, birthday, email, password) 
                  VALUES (UUID(), '$username', '$name', '$firstname', '$birthday', '$email', '$password')";
        if ($conn->query($query)) {
            $message = "Inscription réussie ! Vous pouvez vous connecter.";
        } else {
            $message = "Erreur lors de l'inscription : " . $conn->error;
        }
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion et Inscription</title>
    <link rel="stylesheet" href="../css/login.css"> <!-- Lien vers votre CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/d1a5a942fc.js" crossorigin="anonymous"></script>
</head>
<body>
<!-- Formulaire de Connexion -->
<div class="wrapper" id="loginForm">
    <form action="" method="post">
        <h1>Login</h1>
        <p><?php echo $message; ?></p>
        <div class="input-boxUser">
            <label>
                <input type="text" id="userName" name="username" placeholder="Username" required>
            </label>
            <i class='bx bxs-user'></i>
        </div>
        <div class="input-boxPass">
            <label>
                <input type="password" id="passWord" name="password" placeholder="Password" required>
            </label>
            <div class="eyePassword">
                <i class="fa-solid fa-eye"></i>
            </div>
            <i class='bx bxs-lock'></i>
        </div>
        <button id="LoginButton" type="submit" name="login" class="btn">Login</button>
        <div class="register-link">
            <p>Don't have an account? <a href="#registerForm" id="showRegister">Register</a></p>
        </div>
        <div class="Connexion-Container">
        </div>
</div>
</form>
</div>

<!-- Formulaire d'Inscription -->
<!-- Formulaire d'Inscription -->
<div class="wrapper-register" id="registerForm" style="display: none;">
    <form action="" method="post">
        <div class="back-to-login">
            <i class='bx bx-chevron-left'></i>
            <p><a href="#loginForm" id="showLogin">Login</a></p>
        </div>
        <h1>Register</h1>
        <p><?php echo $message; ?></p>

        <!-- Champ pour le nom d'utilisateur (username) -->
        <div class="input-box">
            <input type="text" name="username" placeholder="Username" required>
            <i class='bx bxs-user'></i>
        </div>

        <!-- Champ pour le prénom (firstname) -->
        <div class="input-box">
            <input type="text" name="firstname" placeholder="First Name" required>
            <i class='bx bxs-user'></i>
        </div>

        <!-- Champ pour le nom (name) -->
        <div class="input-box">
            <input type="text" name="name" placeholder="Last Name" required>
            <i class='bx bxs-user'></i>
        </div>

        <!-- Champ pour la date de naissance (birthday) -->
        <div class="input-box">
            <input type="date" name="birthday" placeholder="Birthday" required>
            <i class='bx bxs-calendar'></i>
        </div>

        <!-- Champ pour l'email -->
        <div class="input-box">
            <input type="email" name="email" placeholder="Email" required>
            <i class='bx bxs-envelope'></i>
        </div>

        <!-- Champ pour le mot de passe (password) -->
        <div class="input-boxPass2">
            <input type="password" name="password" placeholder="Password" required>
            <div class="eyePassword2">
                <i class="fa-solid fa-eye"></i>
            </div>
            <i class='bx bxs-lock'></i>
        </div>

        <button type="submit" name="register" class="btn">Register</button>
    </form>
</div>


<!-- JavaScript pour gérer les formulaires -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gestion du basculement entre les formulaires de connexion et d'inscription
        var loginForm = document.getElementById('loginForm');
        var registerForm = document.getElementById('registerForm');
        var forgotPassword = document.getElementById('forgotPassword');
        var showRegister = document.getElementById('showRegister');
        var showLogin = document.getElementById('showLogin');

        // Bouton pour afficher le formulaire d'inscription
        showRegister.addEventListener('click', function (e) {
            e.preventDefault();
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            if (forgotPassword) {
                forgotPassword.style.display = 'none';
            }
        });

        // Bouton pour afficher le formulaire de connexion
        showLogin.addEventListener('click', function (e) {
            e.preventDefault();
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            if (forgotPassword) {
                forgotPassword.style.display = 'none';
            }
        });

        // Gestion de l'affichage/masquage du mot de passe dans le formulaire de connexion
        let passwordInput = document.querySelector('.input-boxPass input');
        let showPasswordBtn = document.querySelector('.eyePassword i');

        if (showPasswordBtn) {
            showPasswordBtn.onclick = function () {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text"; // Afficher le mot de passe
                    showPasswordBtn.classList.add('active'); // Activer l'icône
                } else {
                    passwordInput.type = "password"; // Masquer le mot de passe
                    showPasswordBtn.classList.remove('active'); // Désactiver l'icône
                }
            }
        }

        // Gestion de l'affichage/masquage du mot de passe dans le formulaire d'inscription
        let passwordInput2 = document.querySelector('.input-boxPass2 input');
        let showPasswordBtn2 = document.querySelector('.eyePassword2 i');

        if (showPasswordBtn2) {
            showPasswordBtn2.onclick = function () {
                if (passwordInput2.type === "password") {
                    passwordInput2.type = "text"; // Afficher le mot de passe
                    showPasswordBtn2.classList.add('active'); // Activer l'icône
                } else {
                    passwordInput2.type = "password"; // Masquer le mot de passe
                    showPasswordBtn2.classList.remove('active'); // Désactiver l'icône
                }
            }
        }
    });

</script>
</body>
</html>
