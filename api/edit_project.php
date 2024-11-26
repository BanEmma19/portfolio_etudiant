<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $external_link = $_POST['external_link'];

    $stmt = $pdo->prepare("UPDATE projets SET titre = ?, description = ?, lien_externe = ? WHERE id = ?");
    $stmt->execute([$title, $description, $external_link, $id]);

    header("Location: ../projects.php");
    exit();
}
?>
