<?php
session_start();
if (!isset($_SESSION['etudiant_id'])) {
    header("Location: login.php");
    exit();
}

require_once './includes/db.php';
require_once './libs/fpdf/fpdf.php';

// Récupération des données de l'étudiant
$etudiantId = $_SESSION['etudiant_id'];
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiantId]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    die("Étudiant introuvable.");
}

// Récupération des compétences
$competencesStmt = $pdo->prepare("SELECT * FROM competences WHERE etudiant_id = ?");
$competencesStmt->execute([$etudiantId]);
$competences = $competencesStmt->fetchAll();

// Récupération des projets
$projetsStmt = $pdo->prepare("SELECT * FROM projets WHERE etudiant_id = ?");
$projetsStmt->execute([$etudiantId]);
$projets = $projetsStmt->fetchAll();

// Création du fichier PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// En-tête avec design professionnel
$pdf->SetFillColor(0, 102, 204); // Couleur de fond bleu
$pdf->SetDrawColor(0, 102, 204); // Couleur de bordure bleu
$pdf->Rect(0, 0, 210, 35, 'F'); // Rectangle couvrant toute la largeur du PDF
$pdf->SetTextColor(255, 255, 255); // Texte en blanc
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetY(8); // Ajustement vertical du texte
$pdf->Cell(0, 10, utf8_decode($etudiant['name']), 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 8, utf8_decode($etudiant['email']), 0, 1, 'C');
$pdf->Ln(15); // Espacement après l'en-tête

// Section : Informations personnelles
$pdf->SetTextColor(33, 33, 33); // Noir pour les textes
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Informations personnelles'), 'B', 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(2);
$pdf->Cell(0, 8, utf8_decode("Date de naissance : " . ($etudiant['date_naissance'] ?? 'Non renseigné')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Âge : " . ($etudiant['age'] ?? 'Non renseigné')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Niveau d'étude : " . ($etudiant['niveau_etude'] ?? 'Non renseigné')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Filière : " . ($etudiant['filiere'] ?? 'Non renseigné')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Adresse : " . ($etudiant['adresse'] ?? 'Non renseigné')), 0, 1);
$pdf->Ln(10);

// Section : Compétences
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Compétences'), 'B', 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(2);

if (count($competences) > 0) {
    foreach ($competences as $competence) {
        $pdf->Cell(0, 8, utf8_decode("- " . $competence['nom'] . " (Niveau : " . $competence['niveau_maitrise'] . ")"), 0, 1);
    }
} else {
    $pdf->Cell(0, 8, utf8_decode("Aucune compétence ajoutée."), 0, 1);
}
$pdf->Ln(10);

// Section : Projets
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Projets'), 'B', 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(2);

if (count($projets) > 0) {
    foreach ($projets as $projet) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, utf8_decode($projet['titre']), 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 8, utf8_decode($projet['description']));
        if (!empty($projet['lien_externe'])) {
            $pdf->SetTextColor(0, 51, 204); // Bleu pour le lien
            $pdf->Cell(0, 8, utf8_decode("Lien : " . $projet['lien_externe']), 0, 1, 'L');
            $pdf->SetTextColor(33, 33, 33); // Retour au noir
        }
        $pdf->Ln(5);
    }
} else {
    $pdf->Cell(0, 8, utf8_decode("Aucun projet ajouté."), 0, 1);
}
$pdf->Ln(10);

// Section : Footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(128, 128, 128);
$pdf->Cell(0, 10, utf8_decode('CV généré automatiquement via la plateforme Portfolio.'), 0, 1, 'C');

// Téléchargement du fichier PDF
$pdf->Output("I", "CV_" . $etudiant['name'] . ".pdf");
?>
