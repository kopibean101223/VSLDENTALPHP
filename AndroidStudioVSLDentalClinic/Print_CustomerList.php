<?php
require('fpdf/fpdf.php'); // Make sure FPDF is installed or use composer require fpdf/fpdf

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "VSLDENTALCLINIC";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM patientlist");

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Patient List', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'ID', 1);
$pdf->Cell(60, 10, 'Name', 1);
$pdf->Cell(60, 10, 'Email', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['id'], 1);
    $pdf->Cell(60, 10, $row['name'], 1);
    $pdf->Cell(60, 10, $row['email'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'Patient_List.pdf');
$conn->close();
?>