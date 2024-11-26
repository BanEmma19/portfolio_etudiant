// delete_competence.php
<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $competenceId = $_GET['id'];
    $userId = $_SESSION['etudiant_id'];

    $stmt = $pdo->prepare("DELETE FROM competences WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$competenceId, $userId]);

    header("Location: ../skills.php");
    exit();
} else {
    echo "Aucun ID de compétence fourni.";
}
?>
