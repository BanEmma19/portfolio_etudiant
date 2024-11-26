<?php
// Vérification de l'état de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si un étudiant est connecté
$isLoggedIn = isset($_SESSION['etudiant_id']);
?>
