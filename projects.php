<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
require_once './includes/db.php';

// Ajout d'un projet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $external_link = $_POST['external_link'];
    $userId = $_SESSION['etudiant_id'];

    // Validation du lien
    if (!filter_var($external_link, FILTER_VALIDATE_URL)) {
        $error = "Veuillez entrer un lien valide.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO projets (titre, description, lien_externe, etudiant_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $external_link, $userId]);
        header("Location: projects.php");
        exit();
    }
}

// Récupération des projets existants
$userId = $_SESSION['etudiant_id'];
$projects = $pdo->query("SELECT * FROM projets WHERE etudiant_id = $userId")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/project.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Ajout de Font Awesome -->
    <title>Gestion des Projets</title>
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
    <a href="dashboard.php" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
        Retour au tableau de bord
    </a>
    <h2>Gérer mes Projets</h2>
    <form method="POST" class="form">
        <input type="text" name="title" placeholder="Titre du projet" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="url" name="external_link" placeholder="Lien externe (GitHub, etc.)" required>
        <button type="submit">Ajouter le Projet</button>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>
    <h3>Mes Projets</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Lien Externe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($projects) > 0): ?>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?= htmlspecialchars($project['id']) ?></td>
                        <td><?= htmlspecialchars($project['titre']) ?></td>
                        <td><?= htmlspecialchars($project['description']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($project['lien_externe']) ?>" target="_blank">
                                <?= htmlspecialchars($project['lien_externe']) ?>
                            </a>
                        </td>
                        <td class="actions">
                            <!-- Icônes Font Awesome pour la modification et la suppression -->
                            <a href="javascript:void(0);" onclick="openEditModal(
                                <?= htmlspecialchars($project['id']) ?>,
                                '<?= htmlspecialchars($project['titre']) ?>',
                                '<?= htmlspecialchars($project['description']) ?>',
                                '<?= htmlspecialchars($project['lien_externe']) ?>'
                            )">
                                <i class="fas fa-edit"></i> <!-- Icône de modification -->
                            </a>
                            <a href="./api/delete_project.php?id=<?= $project['id'] ?>" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                <i class="fas fa-trash-alt"></i> <!-- Icône de suppression -->
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Aucun projet ajouté pour l'instant.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal améliorée pour la modification d'un projet -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier un Projet</h2>
            <form id="editForm" method="POST" action="./api/edit_project.php">
                <input type="hidden" name="id" id="editId">
                <label for="editTitle">Titre</label>
                <input type="text" name="title" id="editTitle" placeholder="Titre du projet" required>
                
                <label for="editDescription">Description</label>
                <textarea name="description" id="editDescription" placeholder="Description" required></textarea>
                
                <label for="editLink">Lien Externe</label>
                <input type="url" name="external_link" id="editLink" placeholder="Lien externe (GitHub, etc.)" required>
                
                <button type="submit">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

</main>

<script>
    const modal = document.getElementById("editModal");
    const closeModal = document.querySelector(".close");

    // Fonction pour afficher la modal
    function openEditModal(id, title, description, link) {
        document.getElementById("editId").value = id;
        document.getElementById("editTitle").value = title;
        document.getElementById("editDescription").value = description;
        document.getElementById("editLink").value = link;
        modal.style.display = "block";
    }

    // Fermer la modal lorsqu'on clique sur "X"
    closeModal.onclick = function() {
        modal.style.display = "none";
    };

    // Fermer la modal lorsqu'on clique en dehors du contenu
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
</script>

</body>
</html>
