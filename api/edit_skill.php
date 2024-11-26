<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['skill_name'];
    $level = $_POST['mastery_level'];

    $stmt = $pdo->prepare("UPDATE competences SET nom = ?, niveau_maitrise = ? WHERE id = ?");
    $stmt->execute([$name, $level, $id]);

    header("Location: ../skills.php");
    exit();
}
?>
