<?php

require('fpdf/fpdf.php');
class PDF extends FPDF
{
    function Header()
    {
        $this->Image('banner.jpg',0,0,210,300);
        $con=mysqli_connect("localhost","root","");
        if(!$con)
        {
            die('Could not connect: '.mysqli_error());
        }
        mysqli_select_db($con,"sample");
        $sql="SELECT * FROM companyprofile";
        $result=mysqli_query($con,$sql);
        if($row2=mysqli_fetch_array($result))
        {
            $this->Image($row2['sicon'],140,30,50,20);   
        }
    }
    function Footer()
    {
        $con=mysqli_connect("localhost","root","");
        if(!$con)
        {
            die('Could not connect: '.mysqli_error());
        }
        mysqli_select_db($con,"sample");
        $sql="SELECT * FROM companyprofile";
        $result=mysqli_query($con,$sql);
        if($row1=mysqli_fetch_array($result))
        {
            $this->SetXY(10,285);
            $this->SetFont('Times','BI',20);
            $this->Cell(0,10,$row1['curl'],0,0,'C');      
        }
      $this->SetXY(195,285);
      $this->SetFont('Times','BI',20);
      $this->Cell(0,10,$this->PageNo(),0,0,'C');
    }
}
$pdf= new PDF();
$con=mysqli_connect("localhost","root","");
if(!$con)
{
    die('Could not connect: '.mysqli_error());
}
mysqli_select_db($con,"sample");
$sql="SELECT * FROM companyprofile";
$result=mysqli_query($con,$sql);
$result1=mysqli_query($con,$sql);
$result2=mysqli_query($con,$sql);
$result3=mysqli_query($con,$sql);
$pdf->AddFont('Verdanaz','','verdanaz.php');
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');    
if($row=mysqli_fetch_array($result))
{
    $pdf->SetXY(80,85);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(20,10,$row['cName'],0,0,'C');
    $pdf->SetXY(80,95);
    $pdf->SetFont('Times','I',15);
    $pdf->Cell(20,10,$row['cUsername'],0,0,'C');
    $pdf->SetXY(40,110);
    $pdf->SetFont('Times','BUI',20);
    $pdf->Cell(20,10,'Mission: ',0,0,'C');
    $pdf->SetXY(30,120);
    $pdf->SetFont('Times','I',12);
    $pdf->MultiCell(130,5,$row['cMission']);
    $pdf->SetXY(35,165);
    $pdf->SetFont('Times','BUI',20);
    $pdf->Cell(20,10,'Vision: ',0,0,'C');
    $pdf->SetXY(28,175);
    $pdf->SetFont('Times','I',12);
    $pdf->MultiCell(100,5,$row['cVision']);
}
$pdf->AddPage('P','A4');
if($r=mysqli_fetch_array($result1))
{
    $pdf->SetXY(80,85);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(20,10,$r['cName'],0,0,'C');
    $pdf->SetXY(80,95);
    $pdf->SetFont('Times','I',15);
    $pdf->Cell(20,10,$r['cUsername'],0,0,'C');
    $pdf->SetXY(55,110);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Company Details: ',0,0,'C');
    $pdf->SetXY(30,130);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Date Edited: ',0,0,'C');
    $pdf->SetXY(68,130);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['dateedited'],0,0,'C');
    $pdf->SetXY(35,160);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Company Email: ',0,0,'C');
    $pdf->SetXY(69,160);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['cEmailid'],0,0,'C');
    $pdf->SetXY(47,190);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Company Contact Number: ',0,0,'C');
    $pdf->SetXY(93,190);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['cNumber'],0,0,'C');
}
$pdf->AddPage('P','A4');
if($rr=mysqli_fetch_array($result2))
{
    $pdf->SetXY(80,85);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(20,10,$rr['cName'],0,0,'C');
    $pdf->SetXY(80,95);
    $pdf->SetFont('Times','I',15);
    $pdf->Cell(20,10,$rr['cUsername'],0,0,'C');
    $pdf->SetXY(55,110);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'Business Details: ',0,0,'C');
    $pdf->SetXY(30,125);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Type: ',0,0,'C');
    $pdf->SetXY(58,125);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cBusiness'],0,0,'C');
    $pdf->SetXY(34,145);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Industry: ',0,0,'C');
    $pdf->SetXY(68,145);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cIndustry'],0,0,'C');
    $pdf->SetXY(30,165);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Stage: ',0,0,'C');
    $pdf->SetXY(68,165);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cStage'],0,0,'C');
    $pdf->SetXY(39,185);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Starting Year: ',0,0,'C');
    $pdf->SetXY(75,185);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['csy'],0,0,'C');
    $pdf->SetXY(34,205);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Location: ',0,0,'C');
    $pdf->SetXY(70,205);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cLocation'],0,0,'C');
}
$pdf->AddPage('P','A4');
if($rrr=mysqli_fetch_array($result3))
{
    $pdf->SetXY(80,85);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(20,10,$rrr['cName'],0,0,'C');
    $pdf->SetXY(80,95);
    $pdf->SetFont('Times','I',15);
    $pdf->Cell(20,10,$rrr['cUsername'],0,0,'C');
    $pdf->SetXY(40,110);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'About Us: ',0,0,'C');
    $pdf->SetXY(30,125);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Owner Name: ',0,0,'C');
    $pdf->SetXY(63,125);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['oname'],0,0,'C');
    $pdf->SetXY(37,145);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Years Of Experience: ',0,0,'C');
    $pdf->SetXY(66,145);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['yexp'],0,0,'C');
    $pdf->SetXY(74,145);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,'years',0,0,'C');
    $pdf->SetXY(84,145);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['mexp'],0,0,'C');
    $pdf->SetXY(93,145);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,'months',0,0,'C');
    $pdf->SetXY(30,165);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Owner Money: ',0,0,'C');
    $pdf->SetXY(60,165);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['prefcur']." ".$rrr['omoney'],0,0,'C');
}
$pdf->Output();
?>