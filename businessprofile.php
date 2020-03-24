<?php

require('diag11.php');

$pdf= new PDF_Diag();
$con=mysqli_connect("localhost","root","");
if(!$con)
{
    die('Could not connect: '.mysqli_error());
}
mysqli_select_db($con,"business");
$sql="SELECT * FROM companyprofile WHERE id='banuwo'";
$result=mysqli_query($con,$sql);
$result1=mysqli_query($con,$sql);
$result2=mysqli_query($con,$sql);
$pdf->AddFont('Verdanaz','','verdanaz.php');
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');    
if($row=mysqli_fetch_array($result))
{
    $pdf->SetXY(55,110);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Mission: ',0,0,'C');
    $pdf->SetXY(45,125);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(130,5,$row['cMission']);
    $pdf->SetXY(50,165);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Vision: ',0,0,'C');
    $pdf->SetXY(45,179);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(130,5,$row['cVision']);
}
$pdf->AddPage('P','A4');
if($r=mysqli_fetch_array($result1))
{
    $pdf->SetXY(75,120);
    $pdf->SetFont('Times','BUI',25);
    $pdf->Cell(20,10,'Company Details: ',0,0,'C');
    $pdf->SetXY(50,140);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Date Edited: ',0,0,'C');
    $pdf->SetXY(87,140);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['dateedited'],0,0,'C');
    $pdf->SetXY(55,170);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Company Email: ',0,0,'C');
    $pdf->SetXY(93,170);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['cEmailid'],0,0,'C');
    $pdf->SetXY(67,200);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Company Contact Number: ',0,0,'C');
    $pdf->SetXY(113,200);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$r['cNumber'],0,0,'C');
}
$pdf->AddPage('P','A4');
if($rr=mysqli_fetch_array($result2))
{
    $pdf->SetXY(75,120);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'Business Details: ',0,0,'C');
    $pdf->SetXY(51,135);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Industry: ',0,0,'C');
    $pdf->SetXY(90,135);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cIndustry'],0,0,'C');
    $pdf->SetXY(48,155);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Stage: ',0,0,'C');
    $pdf->SetXY(80,155);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cStage'],0,0,'C');
    $pdf->SetXY(55,175);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Starting Year: ',0,0,'C');
    $pdf->SetXY(90,175);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['csy'],0,0,'C');
    $pdf->SetXY(50,195);
    $pdf->SetFont('Times','BI',15);
    $pdf->Cell(30,5,'Business Location: ',0,0,'C');
    $pdf->SetXY(85,195);
    $pdf->SetFont('Times','I',14);
    $pdf->Cell(30,5,$rr['cLocation'],0,0,'C');
}
$pdf->AddPage('P','A4');
$SQL3="SELECT * FROM personal WHERE id='banuwo'";
$result5=mysqli_query($con,$SQL3);
if($row1=mysqli_fetch_array($result5))
{
    $pdf->SetXY(60,120);
    $pdf->SetFont('Times','BUI',20);
    $pdf->Cell(20,10,'Milestone of Company:',0,0,'C');
    $mname=json_decode($row1['mname']); $edate=json_decode($row1['edate']);
    $x1=35; $y1=130;  $x2=40; $y2=140;
    for($i =0; $i < count($mname); $i++)
    {
        if($edate[$i] != "")
        {
            $pdf->SetXY($x1,$y1);
            $pdf->SetFont('Times','BI',15);
            $pdf->Cell(20,10,'Date: ',0,0,'C');
            $pdf->SetFont('Times','I',15); 
            $pdf->SetXY($x1+25,$y1);
            $pdf->Cell(20,10,$edate[$i],0,0,'C');
            $pdf->SetXY($x2,$y2);
            $pdf->SetFont('Times','BI',15);
            $pdf->Cell(20,10,'Milestone: ',0,0,'C');
            $pdf->SetFont('Times','I',15); 
            $pdf->SetXY($x2+25,$y2);
            $pdf->Cell(20,10,$mname[$i],0,0,'C');
        }
        $x1+=50;  $x2+=50;
    }
    $pdf->SetXY(55,160);
    $pdf->SetFont('Times','BUI',20);
    $pdf->Cell(20,10,'Company Core Team: ',0,0,'C');
    $ename=json_decode($row1['empname']);
    $de=json_decode($row1['design']);
    $ex=json_decode($row1['EXP']);
    $ab=json_decode($row1['about']);
    $pdf->SetFont('Times','I',15);
    $x3=35; $y3=175; $x4=38; $y4=185; $x5=38; $y5=195; $x6=32; $y6=205;  
    for($i=0; $i < count($ename); $i++)
    {
        if($ename[$i] != "")
        {
            $pdf->SetXY($x3,$y3);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,'Employee Name: ',0,0,'C');
            $pdf->SetXY($x3+24,$y3);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$ename[$i],0,0,'C');
            $pdf->SetXY($x4,$y4);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,'Employee Disgnation: ',0,0,'C');
            $pdf->SetXY($x4+35,$y4);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$de[$i],0,0,'C');
            $pdf->SetXY($x5,$y5);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,'Employee Experience: ',0,0,'C');
            $pdf->SetXY($x5+28,$y5);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$ex[$i]." years",0,0,'C');
            $pdf->SetXY($x6,$y6);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,'Employee Skills: ',0,0,'C');
            $pdf->SetXY($x6+40,$y6);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$ab[$i],0,0,'C');
        }
        $x3+=85; $x4+=85; $x5+=85; $x6+=85;
    }
}
$pdf->AddPage('P','A4');
$sql1="SELECT * FROM sales WHERE id='banuwo'";
$result3=mysqli_query($con,$sql1);
$ds=0;$cs=0;
if($row3=mysqli_fetch_array($result3))
{
    $ds1=$row3['ds']; $ds2=$row3['ds1']; $ds3=$row3['ds2'];
    $chs1=$row3['chs']; $chs2=$row3['chs1']; $chs3=$row3['chs2'];
    $diset=($ds1+$ds2+$ds3)/3;
    $chset=($chs1+$chs2+$chs3)/3;
    $d=array('Direct Set'=>$diset,'Channel Set'=>$chset);
    $pdf->SetXY(75,120);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'Marketing and Sales:',0,0,'C');
    $valX = round($pdf->GetX());
    $valY = round($pdf->GetY());
    $pdf->SetXY(90, $valY);
    $col1=array(100,100,255);
    $col2=array(255,100,100);
    $col3=array(255,255,100);
    $pdf->PieChart(150,150, $d, '%l (%p)', array($col1,$col2,$col3));
    $pdf->SetXY($valX, $valY + 40);
}
$pdf->AddPage('P','A4');
$sql2="SELECT * FROM sales WHERE id='banuwo'";
$result4=mysqli_query($con,$sql2);
if($row4=mysqli_fetch_array($result4))
{
    $pdf->SetXY(80,120);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'Online Marketing and Sales:',0,0,'C');
    $label=json_decode($row4['salesoln']);  
    $y1=json_decode($row4['salesol']);
    $y2=json_decode($row4['salesol1']);
    $y3=json_decode($row4['salesol2']);
    $d=array();  $data1=array();
    for($i=0; $i < count($label); $i++)
    {
        $d[$i]=($y1[$i]+$y2[$i]+$y3[$i]);
        $data1[$label[$i]]=$d[$i];
    }  
    $valX = round($pdf->GetX());
    $valY = round($pdf->GetY());
    $pdf->SetXY(90, $valY);
    $col1=array(100,100,255);
    $col2=array(255,100,100);
    $col3=array(255,255,100);
    $pdf->PieChart(160,160, $data1, '%l (%p)', array($col1,$col2,$col3));
    $pdf->SetXY($valX, $valY + 40);
}
$pdf->AddPage();
$sql4="SELECT * FROM marketreasearch WHERE  id='banuwo'";
$result5=mysqli_query($con,$sql4);
if($row2=mysqli_fetch_array($result5))
{
    $pdf->SetXY(80,110);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'Problems and Soluations:',0,0,'C');
   $prob=json_decode($row2['prob']);
   $sol=json_decode($row2['sol']);
   $mar=json_decode($row2['mar']);
   $tar=json_decode($row2['tar']); $l=1;
   $x1=35;$y1=120;  $x2=35; $y2=130;
   for($i=0; $i<count($sol);$i++)
   {
    if($sol[$i] != "")
    {
        $pdf->SetXY($x1,$y1);
        $pdf->SetFont('Times','BI',13);
        $pdf->Cell(20,10,"Problem ".$l." :",0,0,'C');
        $pdf->SetXY($x1+28,$y1);
        $pdf->SetFont('Times','I',13);
        $pdf->Cell(15,10,$prob[$i],0,0,'C');
        $pdf->SetXY($x2,$y2);
        $pdf->SetFont('Times','BI',13);
        $pdf->Cell(20,10,"Soluation ".$l." :",0,0,'C');
        $pdf->SetXY($x2+25,$y2);
        $pdf->SetFont('Times','I',13);
        $pdf->Cell(20,10,$sol[$i],0,0,'C');
        $l++;
    }
    $y1+=20; $y2+=20;
   }

    $pdf->SetXY(55,185);
    $pdf->SetFont('Times','BUI',30);
    $pdf->Cell(20,10,'SWOT Analysis:',0,0,'C');
    $scname=json_decode($row2['swotcname']);
    $s=json_decode($row2['s']);
    $w=json_decode($row2['w']);
    $o=json_decode($row2['o']);
    $t=json_decode($row2['t']);
   $x3=30; $y3=195;  $x4=23;$y4=205;  $x5=23;$y5=215;  $x6=25;$y6=225;  $x7=20;$y7=235;
    for($i=0;$i<1;$i++)
    {
        if($scname[$i] != "")
        {
            $pdf->SetXY($x3,$y3);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,"Company Name:",0,0,'C');
            $pdf->SetXY($x3+28,$y3);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$scname[$i],0,0,'C');
            $pdf->SetXY($x4,$y4);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,"Strength:",0,0,'C');
            $pdf->SetXY($x4+15,$y4);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$s[$i],0,0,'C');
            $pdf->SetXY($x5,$y5);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,"Weakness:",0,0,'C');
            $pdf->SetXY($x5+15,$y5);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$w[$i],0,0,'C');
            $pdf->SetXY($x6,$y6);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,"Oppurtunity:",0,0,'C');
            $pdf->SetXY($x6+20,$y6);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$o[$i],0,0,'C');
            $pdf->SetXY($x7,$y7);
            $pdf->SetFont('Times','BI',13);
            $pdf->Cell(20,10,"Threads:",0,0,'C');
            $pdf->SetXY($x7+13,$y7);
            $pdf->SetFont('Times','I',13);
            $pdf->Cell(20,10,$t[$i],0,0,'C');
        }
        $x3+=80; $x4+80; $x5+=80; $x6+=80; $x7+=80;
    }
}
$pdf->AddPage('P','A4');
$sql5="SELECT * FROM financial WHERE  id='banuwo'";
$result6=mysqli_query($con,$sql5);
if($row5=mysqli_fetch_array($result6))
{
    $iname=json_decode($row5['iname']);
    $iy1=json_decode($row5['iy1']);
    $iy2=json_decode($row5['iy2']);
    $iy3=json_decode($row5['iy3']);
    $ename=json_decode($row5['ename']);
    $ey1=json_decode($row5['ey1']);
    $ey2=json_decode($row5['ey2']);
    $ey3=json_decode($row5['ey3']);
    $diy=array();  $dey=array();   $diey=array();
    for($i=0;$i<count($iname);$i++)
    {
        $diy[$i]=($iy1[$i]+$iy2[$i]+$iy3[$i]);
        $dey[$i]=($ey1[$i]+$ey2[$i]+$ey3[$i]);
        $diey[$iname[$i]]=array($diy[$i],$dey[$i]);
    }

$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->ColumnChart(200, 70, $diey, null, array(255,175,100));
$pdf->SetXY(95,205);
$pdf->SetFont('Times','BUI',30);
$pdf->Cell(20,10,'Financial(Income & Expense)',0,0,'C');


$pdf->AddPage('P','A4');
    $aname=json_decode($row5['aname']);
    $ay1=json_decode($row5['ay1']);
    $ay2=json_decode($row5['ay2']);
    $ay3=json_decode($row5['ay3']);
    $lname=json_decode($row5['lname']);
    $ly1=json_decode($row5['ly1']);
    $ly2=json_decode($row5['ly2']);
    $ly3=json_decode($row5['ly3']);
    $dly=array();  $day=array();   $daly=array();
    for($i=0;$i<count($aname);$i++)
    {
        $day[$i]=($ay1[$i]+$ay2[$i]+$ay3[$i]);
        $dly[$i]=($ly1[$i]+$ly2[$i]+$ly3[$i]);
        $daly[$aname[$i]]=array($day[$i],$dly[$i]);
    }

$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->ColumnChart1(200, 70, $daly, null, array(255,175,100));
$pdf->SetXY(95,205);
$pdf->SetFont('Times','BUI',30);
$pdf->Cell(20,10,'Financial(Assets & Liability)',0,0,'C');

}
$pdf->Output();
?>