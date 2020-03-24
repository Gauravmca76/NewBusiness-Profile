<?
define('FPDF_FONTPATH','font/');
require('../fpdf.php');

$pdf=new FPDF();
$pdf->AddFont('Trebuchet','','trebuchet.php');
$pdf->AddPage();
$pdf->SetFont('Trebuchet','',35);
$pdf->Cell(0,10,'Changez de police avec FPDF !');
$pdf->Output();
?>  
