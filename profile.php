<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.php");
    exit();
}

require_once './includes/db.php';

// Récupération des informations de l'étudiant
$etudiantId = $_SESSION['etudiant_id'];
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiantId]);
$etudiant = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $niveauEtude = $_POST['niveau_etude'];
    $filiere = $_POST['filiere'];
    $bio = $_POST['bio'];
    $dateNaissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];

    // Calcul de l'âge
    $dob = new DateTime($dateNaissance);
    $now = new DateTime();
    $age = $now->diff($dob)->y;

    // Mise à jour des informations dans la base de données
    $updateStmt = $pdo->prepare("
        UPDATE etudiants 
        SET name = ?, email = ?, niveau_etude = ?, filiere = ?, bio = ?, date_naissance = ?, age = ?, adresse = ?
        WHERE id = ?
    ");
    $updateStmt->execute([$name, $email, $niveauEtude, $filiere, $bio, $dateNaissance, $age, $adresse, $etudiantId]);

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/profile.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Gestion de Profil</title>
</head>
<body>
<?php
require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
}
?>

<main class="manage">
    <!-- Bouton Retour -->
    <div class="back-button">
        <a href="dashboard.php">
            <i class="fa fa-arrow-left arrow"></i>Retour au tableau de bord
        </a>
    </div>
    
    <h2>Gérer mon Profil</h2>
    <form method="POST" class="form">
        <input type="text" name="name" placeholder="Nom" value="<?= htmlspecialchars($etudiant['name']) ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($etudiant['email']) ?>" required>
        <input type="text" name="niveau_etude" placeholder="Niveau d'étude" value="<?= htmlspecialchars($etudiant['niveau_etude']) ?>" required>
        <input type="text" name="filiere" placeholder="Filière" value="<?= htmlspecialchars($etudiant['filiere']) ?>" required>
        <textarea name="bio" placeholder="Biographie"><?= htmlspecialchars($etudiant['bio']) ?></textarea>
        <input type="date" name="date_naissance" placeholder="Date de naissance" value="<?= htmlspecialchars($etudiant['date_naissance']) ?>" required>
        <input type="text" name="adresse" placeholder="Adresse" value="<?= htmlspecialchars($etudiant['adresse']) ?>">
        
        <!-- Boutons de modification et suppression avec icônes -->
        <button type="submit">
            <i class="fa fa-edit"></i> Modifier
        </button>
        <a href="delete_profile.php?id=<?= $etudiant['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre profil ?');">
            <i class="fa fa-trash"></i> Supprimer
        </a>
    </form>
</main>
</body>
</html>
