<?php

require('fpdf/fpdf.php');
class PDF extends FPDF
{
    function Header()
    {
        $this->Image('banner4.jpg',0,0,210,300);
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
            list($x1,$y1)=getimagesize($row2['sicon']);
            $x2=$x1-115;  $y2=$y1-25;
            $this->SetXY(18,18);
            $this->SetFillColor(255,255,255);
            $this->Cell($x2,$y2,'',0,0,'C',true);
            $this->Image($row2['sicon'],20,20,50,20);
            $this->SetXY(15,155);
            $this->SetFont('Times','BI',15);
            $this->setTextColor(0,0,0);
            $this->Cell(0,10,$row2['cName'],0,0,'C');
            $this->SetXY(18,165);
            $this->SetFont('Times','I',15);
            $this->setTextColor(0,0,0);
            $this->Cell(0,10,$row2['cUsername'],0,0,'C');                  
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
            $this->SetXY(0,285);
            $this->SetFont('Times','BI',15);
            $this->setTextColor(0,0,0);
            $this->Cell(0,10,$row1['curl'],0,0,'C');      
        }
      $this->SetXY(175,285);
      $this->SetFont('Times','I',15);
      $this->setTextColor(0,0,0);
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
    $pdf->SetXY(55,175);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Mission: ',0,0,'C');
    $pdf->SetXY(45,187);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(130,5,$row['cMission']);
    $pdf->SetXY(50,219);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Vision: ',0,0,'C');
    $pdf->SetXY(45,232);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(130,5,$row['cVision']);
}
$pdf->AddPage('P','A4');
if($r=mysqli_fetch_array($result1))
{
    $pdf->SetXY(65,180);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Company Details: ',0,0,'C');
    $pdf->SetXY(38,195);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Date Edited: ',0,0,'C');
    $pdf->SetXY(75,195);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['dateedited'],0,0,'C');
    $pdf->SetXY(42,215);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Company Email: ',0,0,'C');
    $pdf->SetXY(75,215);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['cEmailid'],0,0,'C');
    $pdf->SetXY(53,235);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Company Contact Number: ',0,0,'C');
    $pdf->SetXY(97,235);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['cNumber'],0,0,'C');
}
$pdf->AddPage('P','A4');
if($rr=mysqli_fetch_array($result2))
{
    $pdf->SetXY(65,180);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'Business Details: ',0,0,'C');
    $pdf->SetXY(35,195);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Type: ',0,0,'C');
    $pdf->SetXY(62,195);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cBusiness'],0,0,'C');
    $pdf->SetXY(38,210);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Industry: ',0,0,'C');
    $pdf->SetXY(72,210);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cIndustry'],0,0,'C');
    $pdf->SetXY(34,225);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Stage: ',0,0,'C');
    $pdf->SetXY(69,225);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cStage'],0,0,'C');
    $pdf->SetXY(43,240);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Starting Year: ',0,0,'C');
    $pdf->SetXY(75,240);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['csy'],0,0,'C');
    $pdf->SetXY(38,255);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Location: ',0,0,'C');
    $pdf->SetXY(73,255);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cLocation'],0,0,'C');
}
$pdf->AddPage('P','A4');
if($rrr=mysqli_fetch_array($result3))
{
    $pdf->SetXY(60,180);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'About Us: ',0,0,'C');
    $pdf->SetXY(45,195);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Owner Name: ',0,0,'C');
    $pdf->SetXY(80,195);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['oname'],0,0,'C');
    $pdf->SetXY(52,210);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Years Of Experience: ',0,0,'C');
    $pdf->SetXY(80,210);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['yexp'],0,0,'C');
    $pdf->SetXY(89,210);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,'years',0,0,'C');
    $pdf->SetXY(98,210);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['mexp'],0,0,'C');
    $pdf->SetXY(107,210);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,'months',0,0,'C');
    $pdf->SetXY(45,225);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Owner Money: ',0,0,'C');
    $pdf->SetXY(75,225);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rrr['prefcur']." ".$rrr['omoney'],0,0,'C');
}
$pdf->Output();
?>