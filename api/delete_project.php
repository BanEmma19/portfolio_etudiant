<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
require_once '../includes/db.php';

// Vérification si un ID est passé dans l'URL
if (isset($_GET['id'])) {
    $projectId = $_GET['id'];
    $userId = $_SESSION['etudiant_id'];

    // Suppression du projet de l'étudiant connecté
    $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$projectId, $userId]);

    // Redirection après suppression
    header("Location: ../projects.php");
    exit();
} else {
    echo "Aucun ID de projet fourni.";
}
?>
