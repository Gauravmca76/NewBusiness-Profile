<?php

require('fpdf/fpdf.php');
require('database1.php');

class PDF extends FPDF
{
    var $legends;
	var $wLegend;
	var $sum;
    var $NbVal;

    function Footer()
    {
        
    }
    function PieChart($w, $h, $data, $format, $colors=null)
	{
		$this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $hLegend = 5;
        $radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
        $radius = floor($radius / 2);
        $XDiag = $XPage + $margin + $radius;
        $YDiag = $YPage + $margin + $radius;
        if($colors == null) {
            for($i = 0; $i < $this->NbVal; $i++) {
                $gray = $i * intval(255 / $this->NbVal);
                $colors[$i] = array($gray,$gray,$gray);
            }
        }
        //Sectors
        $this->SetLineWidth(0);
        $angleStart = 0;
        $angleEnd = 0;
        $i = 0;
        foreach($data as $val) {
            $angle = ($val * 360) / doubleval($this->sum);
            if ($angle != 0) {
                $angleEnd = $angleStart + $angle;
                $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag-70, $YDiag+15, $radius-20, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }
        //Legends
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1-100, $y1+45, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-100,$y1+45);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }
    function SetLegends($data, $format)
	{
		$this->legends=array();
		$this->wLegend=0;
        $this->sum=array_sum($data);
		$this->NbVal=count($data); 
		foreach($data as $l=>$val)
		{
            $p=sprintf('%.2f',$val/$this->sum*100).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->legends[]=$legend;
            $this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
        }
    }
    function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		$d0 = $a - $b;
    if($cw){
        $d = $b;
        $b = $o - $a;
        $a = $o - $d;
    }else{
        $b += $o;
        $a += $o;
    }
    while($a<0)
        $a += 360;
    while($a>360)
        $a -= 360;
    while($b<0)
        $b += 360;
    while($b>360)
        $b -= 360;
    if ($a > $b)
        $b += 360;
    $b = $b/360*2*M_PI;
    $a = $a/360*2*M_PI;
    $d = $b - $a;
    if ($d == 0 && $d0 != 0)
        $d = 2*M_PI;
    $k = $this->k;
    $hp = $this->h;
    if (sin($d/2))
        $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
    else
        $MyArc = 0;
    //first put the center
    $this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k));
    //put the first point
    $this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
    //draw the arc
    if ($d < M_PI/2){
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
    }else{
        $b = $a + $d/4;
        $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
        $a = $b;
        $b = $a + $d/4;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
        $a = $b;
        $b = $a + $d/4;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
        $a = $b;
        $b = $a + $d/4;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
    }
    //terminate drawing
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='f';
    else
        $op='f';
    $this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }
    protected $extgstates = array();

    function SetAlpha($alpha, $bm='Normal')
    {
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
    var $angle=0;

function Rotate($angle,$x=-1,$y=-1)
{
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->angle!=0)
        $this->_out('Q');
    $this->angle=$angle;
    if($angle!=0)
    {
        $angle*=M_PI/180;
        $c=cos($angle);
        $s=sin($angle);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
}

function _endpage()
{
    if($this->angle!=0)
    {
        $this->angle=0;
        $this->_out('Q');
    }
    parent::_endpage();
}
function RotatedImage($file,$x,$y,$w,$h,$angle)
{
    //Image rotated around its upper-left corner
    $this->Rotate($angle,$x,$y);
    $this->Image($file,$x,$y,$w,$h);
    $this->Rotate(0);
}
}

$pdf= new PDF();
$sql="SELECT * FROM bplan WHERE bemail='demo@gmail.com'";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_array($result);
$sql1="SELECT * FROM sales WHERE bemail='demo@gmail.com'";
$result1=mysqli_query($conn,$sql1);
$row1=mysqli_fetch_array($result1);
$pdf->AddPage();
$pdf->SetXY(0,0);
$pdf->SetFont('Times','I',10);
$pdf->SetFillColor(248,248,255);
$pdf->Cell(210,50,'',0,0,'',true);
$pdf->Image('bekreta.png',5,5,60,40);
$pdf->SetXY(120,10);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',20);
$pdf->Cell(10,10,$row['cName'],0,0);
$pdf->SetXY(179,10);
$pdf->SetFont('Times','I',13);
$pdf->Cell(20,10,'( '.$row['cBusiness'].' )',0,0);
$pdf->SetXY(120,24);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['cLocation'],0,0);
$pdf->SetXY(120,32);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,'+91-'.$row['cNumber'],0,0);
$pdf->SetXY(120,40);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['cEmailid'],0,0);
$pdf->SetXY(0,50);
$pdf->SetFont('Times','I',10);
$pdf->SetFillColor(255,250,240);
$pdf->Cell(210,30,'',0,0,'',true);
$pdf->SetXY(10,55);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(10,10,'TOTAL REVENUES',0,0);
$pdf->SetXY(85,55);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(10,10,'TOTAL EXPENSES',0,0);
$pdf->SetXY(160,55);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(10,10,'TOTAL SALES',0,0);
$revenue=($row['revenue1'] + $row['revenue2'] + $row['revenue3']);
$pdf->SetXY(20,65);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(10,10,'Rs. '.$revenue,0,0);
$expense=($row['expense1'] + $row['expense2'] + $row['expense3']);
$pdf->SetXY(100,65);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(10,10,'Rs. '.$expense,0,0);
$salesol=json_decode($row1['salesol']);
$salesof=json_decode($row1['salesof']);
$d=array();  $sumol=array();  $sumof=array(); $e=array();
for($i=0;$i<count($salesol);$i++)
{
    $sumol[-1]=0;
    $d[$i]=$sumol[$i-1] + $salesol[$i];
    $sumol[$i] = $d[$i];
}
for($i=0;$i<count($salesof);$i++)
{
    $sumof[-1]=0;
    $e[$i]=$sumof[$i-1] + $salesof[$i];
    $sumof[$i] = $e[$i];
}
$sumsales=($d[(count($salesol)-1)] + $e[(count($salesof)-1)]);
$pdf->SetXY(170,65);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(10,10,'Rs. '.$sumsales,0,0);
$pdf->SetXY(10,85);
$pdf->SetFont('Times','BI',20);
$pdf->Cell(30,10,'PROBLEMS:',0,0);
$prob=json_decode($row['prob']); $a=1;  $y=95;
for($i=0;$i<count($prob);$i++)
{
    $pdf->SetXY(10,$y);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(100,5,'Problem '.$a.' :'.$prob[$i],0,1);
    $a++;  $y+=15;
}
$pdf->SetXY(150,85);
$pdf->SetFont('Times','BI',19);
$pdf->Cell(30,10,'SOLUTIONS:',0,0);
$sol=json_decode($row['sol']); $b=1;  $y=95;
for($i=0;$i<count($sol);$i++)
{
    $pdf->SetXY(109,$y);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(100,5,'Soluation '.$b.' :'.$sol[$i],0,1);
    $b++;  $y+=17;
}
$pdf->SetXY(0,150);
$pdf->SetFont('Times','I',10);
$pdf->SetFillColor(248,248,255);
$pdf->Cell(210,75,'',0,0,'',true);
$pdf->SetXY(150,155);
$pdf->SetFont('Times','BI',16);
$pdf->Cell(20,10,'SALES SUMMARY',0,0);
$ds=$row1['ds'];     $chs=$row1['chs']; 
$datasale["Direct Sales"]=$ds;
$datasale["Channel Sales"]=$chs;
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(245,170);
$col[0]=array(100,100,255);
$col[1]=array(255,100,100);
$col[2]=array(255,255,100);
$col[3]=array(63, 81, 181);
$col[4]=array(205, 220, 57);
$col[5]=array(139, 195, 74);
$col[6]=array(255, 152, 0);
$col[7]=array(53, 59, 72);
$col[8]=array(111, 30, 81);
$col[9]=array(0, 98, 102);
$col[10]=array(255,195,18);
$col[11]=array(87,88,187);
$pdf->PieChart(60,60, $datasale, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$pdf->SetXY(10,155);
$pdf->SetFont('Times','BI',16);
$pdf->Cell(20,10,'MARKETING SALES',0,0);
$saon=$d[(count($salesol)-1)]; 
$saof=$e[(count($salesof)-1)];   
$data1["Sales Online"] = $saon;  
$data1["Sales Offline"] = $saof; 
$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->SetXY(100,170);
$col[0]=array(100,100,255);
$col[1]=array(255,100,100);
$col[2]=array(255,255,100);
$col[3]=array(63, 81, 181);
$col[4]=array(205, 220, 57);
$col[5]=array(139, 195, 74);
$col[6]=array(255, 152, 0);
$col[7]=array(53, 59, 72);
$col[8]=array(111, 30, 81);
$col[9]=array(0, 98, 102);
$col[10]=array(255,195,18);
$col[11]=array(87,88,187);
$pdf->PieChart(60,60, $data1, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$tar=json_decode($row['tar']);
$pdf->SetXY(80,226);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,10,'TARGET MARKETS',0,0);  $a=1;  $x=0;
for($i=0;$i<count($tar);$i++)
{
    $pdf->SetXY($x,233);
    $pdf->SetFont('Times','I',13);
    $pdf->Cell(30,10,$a." . ".$tar[$i],0,0);
    $a++;   $x+=30;
}
$comp=json_decode($row['swotcname']);
$pdf->SetXY(80,243);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,10,'COMPETITORS',0,0);    $b=1;  $x=0;
for($i=0;$i<count($comp);$i++)   
{
    $pdf->SetXY($x,250);
    $pdf->SetFont('Times','I',13);
    $pdf->Cell(30,10,$b." . ".$comp[$i],0,0);
    $b++;   $x+=30;
}
$value=43500000;
$var=number_format($value,2);
$pdf->SetXY(10,262);
$pdf->SetFont('Times','BI',20);
$pdf->SetFillColor(240,255,240);
$pdf->Cell(190,10,'    FUNDING NEEDED Rs. '.$var,1,0,'C',true);
$pdf->Output();
?>