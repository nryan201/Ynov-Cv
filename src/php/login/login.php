<?php
session_start(); // Start session for session variables

// Database connection
$host = '127.1.1.1';
$dbname = 'ynovcv';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

$message = "";

// Login Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['username'] = $user['username']; // Store username in session
            header('Location: /'); // Redirect to home page
            exit(); // Stop script after redirection
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Aucun compte trouvé avec ce nom d'utilisateur.";
    }
}

// Registration Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $firstname = $conn->real_escape_string($_POST['firstname']); // New field for first name
    $name = $conn->real_escape_string($_POST['name']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_BCRYPT);

    // Check if user already exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $message = "Un utilisateur avec cet email existe déjà.";
    } else {
        // Insert new user with first name into database
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
    <link rel="stylesheet" href="/src/css/login.css"> <!-- Updated CSS path -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/d1a5a942fc.js" crossorigin="anonymous"></script>
</head>
<body>
<!-- Login Form -->
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

<!-- Registration Form -->
<div class="wrapper-register" id="registerForm" style="display: none;">
    <form action="" method="post">
        <div class="back-to-login">
            <i class='bx bx-chevron-left'></i>
            <p><a href="#loginForm" id="showLogin">Login</a></p>
        </div>
        <h1>Register</h1>
        <p><?php echo $message; ?></p>

        <div class="input-box">
            <input type="text" name="username" placeholder="Username" required>
            <i class='bx bxs-user'></i>
        </div>

        <div class="input-box">
            <input type="text" name="firstname" placeholder="First Name" required>
            <i class='bx bxs-user'></i>
        </div>

        <div class="input-box">
            <input type="text" name="name" placeholder="Last Name" required>
            <i class='bx bxs-user'></i>
        </div>

        <div class="input-box">
            <input type="date" name="birthday" placeholder="Birthday" required>
            <i class='bx bxs-calendar'></i>
        </div>

        <div class="input-box">
            <input type="email" name="email" placeholder="Email" required>
            <i class='bx bxs-envelope'></i>
        </div>

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

<!-- JavaScript for Form Toggling -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginForm = document.getElementById('loginForm');
        var registerForm = document.getElementById('registerForm');
        var showRegister = document.getElementById('showRegister');
        var showLogin = document.getElementById('showLogin');

        showRegister.addEventListener('click', function (e) {
            e.preventDefault();
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
        });

        showLogin.addEventListener('click', function (e) {
            e.preventDefault();
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
        });

        let passwordInput = document.querySelector('.input-boxPass input');
        let showPasswordBtn = document.querySelector('.eyePassword i');

        if (showPasswordBtn) {
            showPasswordBtn.onclick = function () {
                passwordInput.type = passwordInput.type === "password" ? "text" : "password";
                showPasswordBtn.classList.toggle('active');
            }
        }

        let passwordInput2 = document.querySelector('.input-boxPass2 input');
        let showPasswordBtn2 = document.querySelector('.eyePassword2 i');

        if (showPasswordBtn2) {
            showPasswordBtn2.onclick = function () {
                passwordInput2.type = passwordInput2.type === "password" ? "text" : "password";
                showPasswordBtn2.classList.toggle('active');
            }
        }
    });
</script>
</body>
</html>
