<?php
require('database1.php');
require('fpdf/fpdf.php');
class PDF extends FPDF
{
    var $legends;
	var $wLegend;
	var $sum;
    var $NbVal;
    function Header()
    {
        $this->Image('bp1.jpg',0,0,210,297);
    }
	function PieChart($w, $h, $data, $format, $colors=null)//(100,35)
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
            $this->Rect($x1-90, $y1+35, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-90,$y1+35);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }

    function PieChart1($w, $h, $data, $format, $colors=null)//(100,35)
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
            $this->Rect($x1-145, $y1+56, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-145,$y1+56);
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
    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
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
$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetAlpha(0.2);
$pdf->RotatedImage('bekreta.png',60,160,100,50,55);
$pdf->SetAlpha(1);
$sql="SELECT * FROM bplan where bemail='demo@gmail.com'";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_array($result);
$pdf->Image('bekreta.png',10,10,50,30);
$pdf->SetXY(100,1);
$pdf->SetFont('Times','BI',30);
$pdf->SetTextColor(77,77,255);
$pdf->Cell(30,20,'Business Plan',0,0);
$pdf->SetXY(90,20);
$pdf->SetFont('Times','BI',30);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,20,$row['cName'],0,0);
$pdf->SetXY(115,29);
$pdf->SetFont('Times','I',15);
$pdf->Cell(30,20,'( '.$row['cBusiness'].' )',0,0);
$pdf->SetXY(7,40);
$pdf->SetFont('Times','BUI',13);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(30,20,'CONTACT INFORMATION',0,0); 
$pdf->SetXY(13,48);
$pdf->SetFont('Times','BI',13);
$pdf->Cell(30,20,$row['cName'],0,0);
$pdf->SetXY(5,58);
$pdf->SetFont('Times','I',13);
$pdf->Cell(30,20,'Location: ',0,0);
$pdf->SetXY(25,58);
$pdf->SetFont('Times','I',13);
$pdf->Cell(30,20,$row['cLocation'],0,0);
$pdf->SetXY(5,68);
$pdf->SetFont('Times','I',13);
$pdf->Cell(30,20,'contact number: ',0,0);
$pdf->SetXY(36,68);
$pdf->SetFont('Times','I',13);
$pdf->Cell(30,20,$row['cNumber'],0,0);
$pdf->SetXY(5,78);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,20,'Email: ',0,0);
$pdf->SetXY(17,78);
$pdf->SetFont('Times','I',11);
$pdf->Cell(30,20,$row['cEmailid'],0,0);
$pdf->SetXY(7,89);
$pdf->SetFont('Times','BUI',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(30,20,'FINANCIAL INFORMATION',0,0);
$pdf->SetXY(5,95);
$pdf->SetFont('Times','BI',12);
$pdf->Cell(30,20,'Company Stage: ',0,0);
$pdf->SetXY(35,95);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,20,$row['cStage'],0,0);
$capital=$row['capital1'] + $row['capital2'] + $row['capital3'];//sum of capital
$pdf->SetXY(5,103);
$pdf->SetFont('Times','BI',12);
$pdf->Cell(30,20,'Capital: ',0,0);
$pdf->SetXY(20,103);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,20,'Rs. '.number_format($capital,2),0,0);
$invesment=$row['investment1'] + $row['investment2'] + $row['investment3'];//sum of investment
$pdf->SetXY(5,110);
$pdf->SetFont('Times','BI',12);
$pdf->Cell(30,20,'Capital Seeking: ',0,0);
$pdf->SetXY(35,110);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,20,'Rs. '.number_format($invesment,2),0,0);
$pdf->SetXY(7,122);
$pdf->SetFont('Times','BUI',15);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(30,20,'MANAGEMENT TEAM',0,0);
$pdf->SetXY(7,128);
$pdf->SetFont('Times','BI',13);
$pdf->Cell(30,20,'CEO & Founder: ',0,0);
$pdf->SetXY(7,142);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(48,5,$row['oname'].' ,',0,0);
$pdf->SetXY(6,147);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(17,5,$row['yexp'].' years',0,0);
$pdf->SetXY(22,148);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(19,5,$row['mexp'].' months',0,0);
$pdf->SetXY(39,148);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(28,5,' experiance in ',0,0);
$pdf->SetXY(5,151);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(41,10,$row['cIndustry'],0,0);
$pdf->SetXY(33,151);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(23,10,' in a ',0,0);
$pdf->SetXY(5,157);
$pdf->SetFont('Times','I',13);
$pdf->MultiCell(28,10,$row['cBusiness'].' .',0,0);
$pdf->SetXY(75,50);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',30);
$pdf->Cell(30,20,'Mission: ',0,0);
$pdf->SetXY(75,66);
$pdf->SetFont('Times','I',12);
$pdf->MultiCell(125,10,$row['cVisionmission'],0,1);
$pdf->SetXY(70,95);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',30);
$pdf->Cell(30,20,'Problems: ',0,0);
$prob=json_decode($row['prob']); $a=1;  $y=110;
for($i=0;$i<2;$i++)
{
    $pdf->SetXY(70,$y);
    $pdf->SetFont('Times','I',14);
    $pdf->MultiCell(60,10,$a.". ".$prob[$i],0,1);
    $a++; $y+=10;
}
$pdf->SetXY(152,95);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',30);
$pdf->Cell(30,20,'Solutions: ',0,0);
$sol=json_decode($row['sol']);  $b=1;  $y=110;
for($i=0;$i<count($sol);$i++)
{
    $pdf->SetXY(150,$y);
    $pdf->SetFont('Times','I',14);
    $pdf->MultiCell(60,10,$b.". ".$sol[$i],0,1);
    $b++;  $y+=10;
}
$pdf->SetXY(68,150);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',27);
$pdf->Cell(30,20,'Target Markets: ',0,0);
$tar=json_decode($row['tar']); $c=1; $y=165;
for($i=0;$i<count($tar);$i++)
{
    $pdf->SetXY(70,$y);
    $pdf->SetFont('Times','I',14);
    $pdf->MultiCell(60,10,$c.". ".$tar[$i],0,1);
    $c++;  $y+=10;
}
$pdf->SetXY(148,150);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','BI',27);
$pdf->Cell(30,20,'Competitors: ',0,0);
$comp=json_decode($row['swotcname']); $d=1;  $y=165;
for($i=0;$i<count($comp);$i++)
{
    $pdf->SetXY(150,$y);
    $pdf->SetFont('Times','I',14);
    $pdf->MultiCell(60,10,$d.". ".$comp[$i],0,1);
    $d++;  $y+=10;
}
$var=3000000;
$pdf->SetXY(70,225);
$pdf->SetFont('Times','I',20);
$pdf->Cell(120,20,'  Funding Needed Rs.'.number_format($var,2),1,0,'C');
$pdf->SetXY(70,250);
$pdf->SetFillColor(77,77,255);
$pdf->Cell(135,8,'',0,0,'',true);
$pdf->SetXY(73,250);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,'FINANCIALS(000  Rs.)',0,0);
$pdf->SetXY(123,250);
$pdf->SetFont('Times','I',11);
$pdf->Cell(30,10,'Year 1',0,0);
$pdf->SetXY(155,250);
$pdf->SetFont('Times','I',11);
$pdf->Cell(30,10,'Year 2',0,0);
$pdf->SetXY(190,250);
$pdf->SetFont('Times','I',11);
$pdf->Cell(30,10,'Year 3',0,0);
$pdf->SetXY(73,258);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,'REVENUES',0,0);
$pdf->SetXY(73,266);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,'EXPENESS',0,0);
$pdf->SetXY(123,258);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,$row['revenue1'],0,0);
$pdf->SetXY(155,258);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,$row['revenue2'],0,0);
$pdf->SetXY(190,258);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,$row['revenue3'],0,0);
$pdf->SetXY(123,266);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,$row['expense1'],0,0);
$pdf->SetXY(155,266);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,$row['expense2'],0,0);
$pdf->SetXY(190,266);
$pdf->SetFont('Times','I',12);
$pdf->Cell(30,10,$row['expense`3'],0,0);
$pdf->Output();
?>