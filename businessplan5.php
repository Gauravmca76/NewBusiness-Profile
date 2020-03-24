<?php

require('fpdf/fpdf.php');
require('database1.php');

class PDF extends FPDF
{
    var $legends;
	var $wLegend;
	var $sum;
    var $NbVal;
    
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
        $this->SetFont('Times', 'I', 11);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1-50, $y1+20, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-50,$y1+20);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }
    function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
    {
        $this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $YDiag = $YPage + $margin;
        $hDiag = floor($h - $margin * 2);
        $XDiag = $XPage + $margin * 2 + $this->wLegend;
        $lDiag = floor($w - $margin * 3 - $this->wLegend);
        if($color == null)
            $color=array(155,155,155);
        if ($maxVal == 0) {
            $maxVal = max($data);
        }
        $valIndRepere = ceil($maxVal / $nbDiv);
        $maxVal = $valIndRepere * $nbDiv;
        $lRepere = floor($lDiag / $nbDiv);
        $lDiag = $lRepere * $nbDiv;
        $unit = $lDiag / $maxVal;
        $hBar = floor($hDiag / ($this->NbVal + 1));
        $hDiag = $hBar * ($this->NbVal + 1);
        $eBaton = floor($hBar * 80 / 100);
        $this->SetLineWidth(0.2);
        $this->SetFont('Times', 'I', 11);
        $this->SetFillColor($color[0],$color[1],$color[2]);
        $i=0;
        foreach($data as $val) {
            //Bar
            $xval = $XDiag;
            $lval = (int)($val * $unit);
            $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
            $hval = $eBaton;
            $this->Rect($xval-13, $yval, $lval, $hval, 'F');
            //Legend
            $this->SetXY(0,$yval);
            $this->Cell($xval - $margin-10, $hval, $this->legends[$i],0,0,'C');
            $i++;
        }
        //Scales
        for ($i = 0; $i <= $nbDiv; $i++) {
            $val = $i * $valIndRepere;
            $xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
            $ypos = $YDiag + $hDiag - $margin;
            $this->Text($xpos-13, $ypos, $val);
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
    function Sector($xc, $yc, $r, $a, $b, $style='F', $cw=true, $o=90)
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
$pdf->AddPage('L','A4');
$pdf->SetXY(80,2);
$pdf->SetFont('Times','BI',30);
$pdf->Cell(150,10,'Business Plan',0,0,'C');
$pdf->SetXY(0,15);//yellow color
$pdf->SetFont('Times','I',15);
$pdf->SetFillColor(254,252,187);
$pdf->Cell(140,50,'',0,0,'',true);
$pdf->SetXY(40,15);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(50,10,'Company Details',0,0,'C');
$pdf->SetXY(10,22);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Company Name: ',0,0,'C');
$pdf->SetXY(44,22);
$pdf->SetFont('Times','I',15);
$pdf->Cell(30,10,$row['cName'],0,0,'C');
$pdf->SetXY(9,29);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Company Type: ',0,0,'C');
$pdf->SetXY(34,29);
$pdf->SetFont('Times','I',15);
$pdf->Cell(30,10,$row['cBusiness'],0,0,'C');
$pdf->SetXY(12,36);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Company Address: ',0,0,'C');
$pdf->SetXY(52,36);
$pdf->SetFont('Times','I',15);
$pdf->Cell(30,10,$row['cLocation'],0,0,'C');
$pdf->SetXY(12,43);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Company Number: ',0,0,'C');
$pdf->SetXY(48,43);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['cNumber'],0,0,'C');
$pdf->SetXY(10,50);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Company Email: ',0,0,'C');
$pdf->SetXY(59,50);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['cEmailid'],0,0,'C');
$pdf->SetXY(140,15);//green color
$pdf->SetFont('Times','I',15);
$pdf->SetFillColor(189,254,187);
$pdf->Cell(160,50,'',0,0,'',true);
$pdf->SetXY(200,15);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(50,10,'Owner Details',0,0,'C');
$pdf->SetXY(150,22);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Owner Name: ',0,0,'C');
$pdf->SetXY(190,22);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['oname'],0,0,'C');
$pdf->SetXY(154,29);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Company Mission: ',0,0,'C');
$pdf->SetXY(185,32);
$pdf->SetFont('Times','I',12);
$pdf->MultiCell(113,5,$row['cVisionmission'],0,1);
$pdf->SetXY(154,45);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Owner Experience: ',0,0,'C');
$pdf->SetXY(184,45);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['yexp'].' years',0,0,'C');
$pdf->SetXY(205,45);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['mexp'].' months',0,0,'C');
$pdf->SetXY(149,53);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(20,10,'Owner Money: ',0,0,'C');
$pdf->SetXY(175,53);
$pdf->SetFont('Times','I',15);
$pdf->Cell(20,10,$row['prefcur']." ".$row['omoney'],0,0,'C');
$pdf->SetXY(0,65);//pink color
$pdf->SetFont('Times','I',15);
$pdf->SetFillColor(254,187,223);
$pdf->Cell(300,50,'',0,0,'',true);
$pdf->SetXY(5,65);
$pdf->SetFont('Times','BI',20);
$pdf->Cell(30,10,'PROBLEMS:',0,0);
$prob=json_decode($row['prob']); $a=1;  $y=75;
for($i=0;$i<count($prob);$i++)
{
    $pdf->SetXY(5,$y);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(100,5,'Problem '.$a.' :'.$prob[$i],0,1);
    $a++;  $y+=15;
}
$pdf->SetXY(170,65);
$pdf->SetFont('Times','BI',19);
$pdf->Cell(30,10,'SOLUTIONS:',0,0);
$sol=json_decode($row['sol']); $b=1;  $y=75;
for($i=0;$i<count($sol);$i++)
{
    $pdf->SetXY(170,$y);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(100,5,'Soluation '.$b.' :'.$sol[$i],0,1);
    $b++;  $y+=17;
}
$pdf->SetXY(0,115);//blue color
$pdf->SetFont('Times','I',15);
$pdf->SetFillColor(200,202,241);
$pdf->Cell(300,50,'',0,0,'',true);
$pdf->SetXY(200,115);
$pdf->SetFont('Times','BI',16);
$pdf->Cell(20,10,'SALES SUMMARY',0,0);
$ds=$row1['ds'];     $chs=$row1['chs']; 
$datasale["Direct Sales"]=$ds;
$datasale["Channel Sales"]=$chs;
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(275,125);
$col[0]=array(118,122,221);
$col[1]=array(221,118,122);
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
$pdf->PieChart(65,80, $datasale, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$pdf->SetXY(30,115);
$pdf->SetFont('Times','BI',16);
$pdf->Cell(20,10,'MARKETING SALES',0,0);
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
$saon=$d[(count($salesol)-1)];  //sum value of sales online
$saof=$e[(count($salesof)-1)];  //sum value of sales offline
$data1["Sales Online"] = $saon;  
$data1["Sales Offline"] = $saof; 
$pdf->SetXY(0,115);
$pdf->BarDiagram(150, 50, $data1, '%l : Rs. %v', array(122,221,118));
$tar=json_decode($row['tar']);
$pdf->SetXY(20,165);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,10,'TARGET MARKETS',0,0);$a=1;  $x=0;
for($i=0;$i<count($tar);$i++)
{
    $pdf->SetXY($x,172);
    $pdf->SetFont('Times','I',13);
    $pdf->Cell(30,10,$a." . ".$tar[$i],0,0);
    $a++;   $x+=30;
}
$comp=json_decode($row['swotcname']);
$pdf->SetXY(100,182);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,3,'COMPETITORS',0,0);    $b=1;  $x=100;
for($i=0;$i<count($comp);$i++)   
{
    $pdf->SetXY($x,188);
    $pdf->SetFont('Times','I',13);
    $pdf->Cell(30,1,$b." . ".$comp[$i],0,0);
    $b++;   $x+=30;
    
}
$pdf->Output();
?>