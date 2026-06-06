<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'etudiant') {
    header("Location: ../login.php"); exit();
}
require_once '../config/connexion.php';
require_once '../vendor/autoload.php';

$id = $_SESSION['id'];

// Infos étudiant
$r = mysqli_query($conn, "
    SELECT e.*, f.nom_filiere 
    FROM etudiant e 
    LEFT JOIN filiere f ON e.id_filiere = f.id_filiere 
    WHERE e.id_etudiant = '$id'
");
$etudiant = mysqli_fetch_assoc($r);

// Notes
$notes = mysqli_query($conn, "
    SELECT n.*, m.nom_module, m.coefficient, a.libelle AS annee
    FROM note n
    JOIN module m ON n.id_module = m.id_module
    JOIN annee_academique a ON n.id_annee = a.id_annee
    WHERE n.id_etudiant = '$id'
");

// Moyenne générale
$r2 = mysqli_query($conn, "SELECT note_controle, note_examen FROM note WHERE id_etudiant='$id'");
$total = 0; $count = 0;
while($n = mysqli_fetch_assoc($r2)) {
    $total += ($n['note_controle'] + $n['note_examen'] * 2) / 3;
    $count++;
}
$moyenne = $count > 0 ? number_format($total / $count, 2) : "0.00";

// Créer le PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('GestEtud');
$pdf->SetAuthor('GestEtud');
$pdf->SetTitle('Relevé de notes');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

// Logo + En-tête
$pdf->Image('../assets/img/bts.png', 20, 15, 25);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetXY(50, 15);
$pdf->Cell(0, 8, 'GestEtud - Relevé de notes', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(50, 24);
$pdf->Cell(0, 6, 'Lycée Qualifiant Technique - Settat', 0, 1, 'L');
$pdf->SetXY(50, 30);
$pdf->Cell(0, 6, 'Année académique : ' . ($etudiant ? '2025-2026' : ''), 0, 1, 'L');

$pdf->Ln(20);

// Ligne séparatrice
$pdf->SetDrawColor(37, 99, 235);
$pdf->SetLineWidth(0.8);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(6);

// Infos étudiant
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Informations de l\'étudiant', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 7, 'Nom complet :', 0, 0);
$pdf->Cell(0, 7, $etudiant['prenom'] . ' ' . $etudiant['nom'], 0, 1);
$pdf->Cell(50, 7, 'Email :', 0, 0);
$pdf->Cell(0, 7, $etudiant['email'], 0, 1);
$pdf->Cell(50, 7, 'Filière :', 0, 0);
$pdf->Cell(0, 7, $etudiant['nom_filiere'], 0, 1);

$pdf->Ln(4);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(6);

// Tableau des notes
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Notes par module', 0, 1);
$pdf->Ln(2);

// En-tête tableau
$pdf->SetFillColor(37, 99, 235);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(55, 8, 'Module',      1, 0, 'C', true);
$pdf->Cell(25, 8, 'Coefficient', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Contrôle',    1, 0, 'C', true);
$pdf->Cell(25, 8, 'Examen',      1, 0, 'C', true);
$pdf->Cell(25, 8, 'Moyenne',     1, 0, 'C', true);
$pdf->Cell(15, 8, 'Résultat',    1, 1, 'C', true);

// Lignes notes
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 9);
$i = 0;
while($n = mysqli_fetch_assoc($notes)) {
    $moy = ($n['note_controle'] + $n['note_examen'] * 2) / 3;
    $resultat = $moy >= 10 ? 'Validé' : 'Échec';

    if ($i % 2 == 0) {
        $pdf->SetFillColor(239, 246, 255);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }

    if ($moy >= 10) {
        $pdf->SetTextColor(5, 150, 105);
    } else {
        $pdf->SetTextColor(220, 38, 38);
    }

    $pdf->Cell(55, 7, $n['nom_module'],             1, 0, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(25, 7, $n['coefficient'],             1, 0, 'C', true);
    $pdf->Cell(25, 7, $n['note_controle'] . '/20',   1, 0, 'C', true);
    $pdf->Cell(25, 7, $n['note_examen'] . '/20',     1, 0, 'C', true);

    if ($moy >= 10) $pdf->SetTextColor(5, 150, 105);
    else $pdf->SetTextColor(220, 38, 38);

    $pdf->Cell(25, 7, number_format($moy, 2) . '/20', 1, 0, 'C', true);
    $pdf->Cell(15, 7, $resultat,                      1, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $i++;
}

// Moyenne générale
$pdf->Ln(4);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(37, 99, 235);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(130, 9, 'Moyenne générale', 1, 0, 'R', true);
$pdf->Cell(40, 9, $moyenne . '/20',    1, 1, 'C', true);

// Pied de page
$pdf->Ln(10);
$pdf->SetTextColor(100, 116, 139);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 6, 'Document généré le ' . date('d/m/Y à H:i') . ' - GestEtud', 0, 1, 'C');

$pdf->Output('releve_notes_' . $etudiant['nom'] . '.pdf', 'D');
?>