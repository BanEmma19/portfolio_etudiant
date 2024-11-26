<?php
require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
}
include 'includes/db.php'; // Connexion à la base de données

// Vérification de l'état de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hachage du mot de passe
    $bio = $_POST['bio'] ?? '';
    $niveau_etude = $_POST['niveau_etude'] ?? '';
    $filiere = $_POST['filiere'] ?? '';
    $diplomes = $_POST['diplomes'] ?? '';
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'] ?? null;

    // Validation de la date de naissance
    if (DateTime::createFromFormat('Y-m-d', $date_naissance) === false) {
        $error = "Date de naissance invalide.";
    } else {
        // Calcul de l'âge
        $dob = new DateTime($date_naissance);
        $now = new DateTime();
        $age = $now->diff($dob)->y; // Âge en années
        if ($age < 0 || $age > 150) {
            $error = "Âge non valide.";
        }
    }

    // Gestion de l'upload de l'image de profil
    $photoProfile = '';
    if (isset($_FILES['photoProfile']) && $_FILES['photoProfile']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['photoProfile']['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes) && $_FILES['photoProfile']['size'] <= 2 * 1024 * 1024) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['photoProfile']['name']);
            $photoProfile = $uploadDir . $fileName;
            move_uploaded_file($_FILES['photoProfile']['tmp_name'], $photoProfile);
        } else {
            $error = "Format d'image non valide ou taille trop grande (max 2 Mo).";
        }
    }

    // Si aucune erreur, on insère les données dans la base
    if (empty($error)) {
        // Préparation de l'insertion dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO etudiants (name, email, password, bio, photoProfile, niveau_etude, filiere, diplomes, date_naissance, age, adresse)
            VALUES (:name, :email, :password, :bio, :photoProfile, :niveau_etude, :filiere, :diplomes, :date_naissance, :age, :adresse)
        ");

        // Exécution de la requête d'insertion
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':bio' => $bio,
            ':photoProfile' => $photoProfile,
            ':niveau_etude' => $niveau_etude,
            ':filiere' => $filiere,
            ':diplomes' => $diplomes,
            ':date_naissance' => $date_naissance,
            ':age' => $age,  // Ajout de l'âge calculé
            ':adresse' => $adresse,
        ]);

        // Connexion automatique après inscription
        $_SESSION['etudiant_id'] = $pdo->lastInsertId();
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
    <title>Inscription - Portfolio Étudiant</title>
    <link rel="stylesheet" href="./public/css/register.css"> <!-- Inclusion du CSS -->
</head>
<body>
<div class="container">
    <h1>Créer un compte</h1>
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="register.php" enctype="multipart/form-data" class="form-register">
        <input type="text" name="name" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Adresse email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="date" name="date_naissance" placeholder="Date de naissance" required>
        <input type="text" name="adresse" placeholder="Adresse (optionnelle)">
        <textarea name="bio" placeholder="Biographie (optionnelle)"></textarea>
        <input type="text" name="niveau_etude" placeholder="Niveau d'études">
        <input type="text" name="filiere" placeholder="Filière">
        <input type="text" name="diplomes" placeholder="Diplômes (séparés par des virgules)">
        <input type="file" name="photoProfile" accept="image/*">
        <button type="submit">Créer un compte</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
