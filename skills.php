<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
require_once './includes/db.php';

// Ajout d'une compétence
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skill_name = $_POST['skill_name'];
    $mastery_level = $_POST['mastery_level'];
    $userId = $_SESSION['etudiant_id'];

    $stmt = $pdo->prepare("INSERT INTO competences (nom, niveau_maitrise, etudiant_id) VALUES (?, ?, ?)");
    $stmt->execute([$skill_name, $mastery_level, $userId]);
    header("Location: skills.php");
    exit();
}

// Récupération des compétences existantes
$userId = $_SESSION['etudiant_id'];
$skills = $pdo->query("SELECT * FROM competences WHERE etudiant_id = $userId")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/skills.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Ajout de Font Awesome -->
    <title>Gestion des Compétences</title>
</head>
<body>
<?php require_once './includes/auth.php';

// Inclure le bon header en fonction de l'état de connexion
if ($isLoggedIn) {
    include './includes/header2.php';
} else {
    include './includes/header.php';
} ?>
<main class="manage">
    <!-- Bouton retour -->
    <div class="back-button">
        <a href="dashboard.php">
            <span class="arrow">&larr;</span> Retour au Dashboard
        </a>
    </div>

    <h2>Gérer mes Compétences</h2>
    <form method="POST" class="form">
        <input type="text" name="skill_name" placeholder="Nom de la compétence" required>
        <select name="mastery_level" required>
            <option value="" disabled selected>Niveau de maîtrise</option>
            <option value="Élevé">Élevé</option>
            <option value="Moyen">Moyen</option>
            <option value="Bas">Bas</option>
        </select>
        <button type="submit">Ajouter la Compétence</button>
    </form>
    <h3>Mes Compétences</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Niveau de Maîtrise</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($skills) > 0): ?>
                <?php foreach ($skills as $skill): ?>
                    <tr>
                        <td><?= htmlspecialchars($skill['id']) ?></td>
                        <td><?= htmlspecialchars($skill['nom']) ?></td>
                        <td><?= htmlspecialchars($skill['niveau_maitrise']) ?></td>
                        <td class="actions">
                            <a href="#" 
                                onclick="openEditSkillModal(<?= $skill['id'] ?>, '<?= htmlspecialchars($skill['nom']) ?>', '<?= htmlspecialchars($skill['niveau_maitrise']) ?>')">
                                <i class="fas fa-edit"></i> <!-- Icône pour modifier -->
                            </a>
                            <a href="./api/delete_skill.php?id=<?= $skill['id'] ?>" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?');">
                                <i class="fas fa-trash-alt"></i> <!-- Icône pour supprimer -->
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Aucune compétence ajoutée pour l'instant.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal pour la modification des compétences -->
    <div id="editSkillModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier une Compétence</h2>
            <form id="editSkillForm" method="POST" action="./api/edit_skill.php">
                <input type="hidden" name="id" id="editSkillId">
                <label for="editSkillName">Nom</label>
                <input type="text" name="skill_name" id="editSkillName" placeholder="Nom de la compétence" required>
                
                <label for="editMasteryLevel">Niveau de maîtrise</label>
                <select name="mastery_level" id="editMasteryLevel" required>
                    <option value="Élevé">Élevé</option>
                    <option value="Moyen">Moyen</option>
                    <option value="Bas">Bas</option>
                </select>
                
                <button type="submit">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

</main>

<script>
    const skillModal = document.getElementById("editSkillModal");
    const closeSkillModal = document.querySelector(".close");

    // Fonction pour ouvrir la modal
    function openEditSkillModal(id, name, level) {
        document.getElementById("editSkillId").value = id;
        document.getElementById("editSkillName").value = name;
        document.getElementById("editMasteryLevel").value = level;
        skillModal.style.display = "block";
    }

    // Fermer la modal lorsqu'on clique sur "X"
    closeSkillModal.onclick = function() {
        skillModal.style.display = "none";
    };

    // Fermer la modal lorsqu'on clique en dehors du contenu
    window.onclick = function(event) {
        if (event.target === skillModal) {
            skillModal.style.display = "none";
        }
    };
</script>

</body>
</html>
