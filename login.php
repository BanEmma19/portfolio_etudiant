<?php
// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
}
include 'includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $etudiant = $stmt->fetch();

    if (!$etudiant) {
        $error = "Aucun utilisateur trouvé avec cet email.";
    } elseif (!password_verify($password, $etudiant['password'])) {
        $error = "Le mot de passe est incorrect.";
    } else {
        // Stocker l'ID de l'utilisateur dans la session
        $_SESSION['etudiant_id'] = $etudiant['id'];

        // Redirection vers le tableau de bord
        header('Location: dashboard.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="./public/css/login.css"> <!-- Lien vers votre fichier CSS -->
</head>
<body>
    <div class="container">
        <form method="POST" action="login.php">
            <h1>Se connecter</h1>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>

</body>
</html>

