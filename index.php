<?php
require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
}
include 'includes/db.php';

// Récupération des portfolios
$search = $_GET['search'] ?? '';

// Préparer la requête pour récupérer les étudiants et leurs compétences (en ne sélectionnant que les noms des compétences)
$stmt = $pdo->prepare("
    SELECT etudiants.*, GROUP_CONCAT(competences.nom SEPARATOR ', ') AS competences
    FROM etudiants
    LEFT JOIN competences ON etudiants.id = competences.etudiant_id
    WHERE etudiants.filiere LIKE :search OR etudiants.bio LIKE :search OR etudiants.niveau_etude LIKE :search
    GROUP BY etudiants.id
");
$stmt->execute([':search' => "%$search%"]);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Portfolio Étudiant</title>
    <link rel="stylesheet" href="./public/css/style.css"> <!-- Inclusion du CSS -->
</head>
<body>
<div class="container">
    <h1>Bienvenue sur l'application Portfolio</h1>
    <form method="GET" action="index.php" class="search-form">
        <input type="text" name="search" placeholder="Recherchez par filière ou technologie" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Rechercher</button>
    </form>

    <div class="portfolio-grid">
        <?php if (!empty($etudiants)): ?>
            <?php foreach ($etudiants as $etudiant): ?>
                <div class="portfolio-item">
                    <img src="<?= $etudiant['photoProfile'] ?: 'assets/images/default-profile.png' ?>" alt="Photo de profil">
                    <h2><?= htmlspecialchars($etudiant['name']) ?></h2>
                    <p><?= htmlspecialchars($etudiant['filiere']) ?></p>
                    <!-- Affichage des noms des compétences -->
                    <p><strong>Compétences :</strong> <?= htmlspecialchars($etudiant['competences']) ?></p>
                    <a href="portfolio.php?id=<?= $etudiant['id'] ?>">Voir Portfolio</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun résultat trouvé pour votre recherche.</p>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
