<?php
require_once './includes/db.php';

if (!isset($_GET['id'])) {
    die("Identifiant étudiant manquant.");
}

$etudiantId = $_GET['id'];

// Récupération des informations de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiantId]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    die("Étudiant introuvable.");
}

// Récupération des compétences
$competencesStmt = $pdo->prepare("SELECT * FROM competences WHERE etudiant_id = ?");
$competencesStmt->execute([$etudiantId]);
$competences = $competencesStmt->fetchAll();

// Récupération des projets
$projetsStmt = $pdo->prepare("SELECT * FROM projets WHERE etudiant_id = ?");
$projetsStmt->execute([$etudiantId]);
$projets = $projetsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/portfolio.css">
    <title>Portfolio de <?= htmlspecialchars($etudiant['name']) ?></title>
</head>
<body>
<?php require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
} ?>
<main class="detail">
    <h2>Portfolio de <?= htmlspecialchars($etudiant['name']) ?></h2>
    <p><strong>Email :</strong> <?= htmlspecialchars($etudiant['email']) ?></p>
    <p><strong>Niveau d'étude :</strong> <?= htmlspecialchars($etudiant['niveau_etude']) ?></p>
    <p><strong>Filière :</strong> <?= htmlspecialchars($etudiant['filiere']) ?></p>
    <p><strong>Biographie :</strong> <?= htmlspecialchars($etudiant['bio']) ?></p>

    <h3>Compétences</h3>
    <ul>
        <?php foreach ($competences as $competence): ?>
            <li><?= htmlspecialchars($competence['nom']) ?> - <?= htmlspecialchars($competence['niveau_maitrise']) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Projets</h3>
    <ul>
        <?php foreach ($projets as $projet): ?>
            <li>
                <strong><?= htmlspecialchars($projet['titre']) ?></strong>: <?= htmlspecialchars($projet['description']) ?>
                (<a href="<?= htmlspecialchars($projet['lien_externe']) ?>" target="_blank">Voir le projet</a>)
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="generate_cv.php?id=<?= $etudiant['id'] ?>" class="btn">Télécharger le CV</a>
</main>
</body>
</html>
