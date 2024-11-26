<?php
require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
}

// Vérification de l'état de la session

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




// Connexion à la base de données
require_once './includes/db.php';

// Récupération des statistiques
$etudiant_id = $_SESSION['etudiant_id'];
$projectCount = $pdo->query("SELECT COUNT(*) FROM projets WHERE etudiant_id = $etudiant_id")->fetchColumn();
$skillCount = $pdo->query("SELECT COUNT(*) FROM competences WHERE etudiant_id = $etudiant_id")->fetchColumn();
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = :id");
$stmt->execute([':id' => $etudiant_id]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/dashboard.css">
    <title>Tableau de Bord</title>
</head>
<body>
<main class="dashboard">
    <h2>Bienvenue sur votre Tableau de Bord, <?= htmlspecialchars($etudiant['name']) ?>!</h2>
    <div class="cards">
        <div class="card">
            <a href="./profile.php" class="btn">
                <h3>Mon Profil</h3>
                Gérer Son Profil
            </a>
        </div>
        <div class="card">
            <a href="./projects.php" class="btn">
                <h3>Mes Projets</h3>
                Gérer les projets
                <p>Total : <?= $projectCount ?></p>
            </a>
        </div>
        <div class="card">
            <a href="./skills.php" class="btn">
                <h3>Mes Compétences</h3>
                Gérer les compétences
                <p>Total : <?= $skillCount ?></p>
            </a>
        </div>
    </div>
</main>
</body>
</html>
