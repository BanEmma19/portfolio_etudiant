<?php
    // Vérification de l'état de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    $isLoggedIn = isset($_SESSION['user']) ? true : false; // Définit false par défaut
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./public/css/header2.css">
  <!-- Lien pour Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Portfolio Étudiant</title>
</head>
<body>
<header class="header">
  <div class="header-content">
    <div class="logo-title">
      <img src="./public/images/1.png" alt="Logo Polytechnique Douala" class="logo">
      <h1 class="site-title">Portfolio Étudiant</h1>
    </div>
    <nav class="nav-links">
      <a href="index.php">Accueil</a>
      <a href="about.php">À propos</a>
    </nav>
    <div class="options">
      <div class="lang-switch">
        <button onclick="changeLanguage('fr')"><i class="fas fa-globe"></i></button>
      </div>
      <div class="profil1">
        <a href="./register.php"><i class="fas fa-user-plus"></i> S'inscrire</a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
      </div>
    </div>
  </div>
</header>


  <script>
    function changeLanguage(lang) {
      console.log(`Changement de langue en : ${lang}`);
      // Implémentation de la logique de changement de langue
    }
  </script>
</body>
</html>
