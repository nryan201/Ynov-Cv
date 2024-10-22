<?php
session_start();

// Paramètres de l'application GitHub
$client_id = 'Ov23liS5tgqRiNYv8dE0';  // Remplace par ton client ID
$client_secret = '079311331c8b1662b91f8fb4179cf3c92ca4467c';  // Remplace par ton secret
$redirect_uri = 'http://127.1.1.1/src/php/callback.php';  // URL de redirection

// Vérification de la présence du paramètre "code"
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Étape 1 : Échanger le code contre un access_token
    $url = 'https://github.com/login/oauth/access_token';

    // Paramètres à envoyer à GitHub
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri
    ];

    // Configuration de la requête POST pour échanger le code
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    echo "Réponse brute de GitHub : " . $result;
    // Traitement de la réponse
    parse_str($result, $response);

    if (isset($response['access_token'])) {
        $access_token = $response['access_token'];

        // Sauvegarde de l'access_token dans la session
        $_SESSION['access_token'] = $access_token;

        // Redirection vers la page de profil ou une autre page
        header('Location: profil.php');
        exit();
    } else {
        // Erreur lors de l'échange du code contre un access_token
        echo "Erreur lors de la récupération de l'access_token.";
    }
} else {
    // Si aucun "code" n'est présent, erreur
    echo "Erreur : Code GitHub non trouvé.";
}
?>
