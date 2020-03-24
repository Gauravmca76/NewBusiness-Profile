<?php
require('fpdf/fpdf.php');
require('database.php');
require('phpqrcode/qrlib.php');
class PDF extends fpdf
{
    function Header()
    {
        $this->Image('profilebusiness.png',0,0,210,297);
    }
    var $legends;
	var $wLegend;
	var $sum;
	var $NbVal;
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
            $this->Rect($x1-140, $y1+48, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-140,$y1+48);
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
    function PieChart1($w, $h, $data, $format, $colors=null)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends1($data,$format);

		$XPage = $this->GetX();
        $YPage = $this->GetY();
		$margin = 1;
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
		$this->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
        $i = 0;
        $radius1=30;
        $XDiag1=90;
        $YDiag1=50;
		foreach($data as $val) {
            $angle = ($val * 360) / doubleval($this->sum);
			if ($angle != 0) {
				$angleEnd = $angleStart + $angle;
				$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag1+55, $YDiag1+158, $radius1-12, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
			$i++;
		}
		//Legends
		$this->SetFont('Courier', 'I', 10);
		$x1 = $XPage + 2 * $radius + 4 * $margin;
		$x2 = $x1 + $hLegend + $margin;
		$y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
		for($i=0; $i<$this->NbVal; $i++) {
			$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
			$this->Rect($x1, $y1+66, $hLegend, $hLegend, 'F');//legends symbol
			$this->SetXY($x2,$y1+66);
			$this->Cell(0,$hLegend,$this->legends[$i]);//pietext
			$y1+=$hLegend + $margin;
		}
    }
    function SetLegends1($data, $format)
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
    elseif($style=='F' || $style=='F')
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
function Circle($x, $y, $r, $style='D')
{
    $this->Ellipse($x,$y,$r,$r,$style);
}

function Ellipse($x, $y, $rx, $ry, $style='D')
{
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $lx=4/3*(M_SQRT2-1)*$rx;
    $ly=4/3*(M_SQRT2-1)*$ry;
    $k=$this->k;
    $h=$this->h;
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x+$rx)*$k,($h-$y)*$k,
        ($x+$rx)*$k,($h-($y-$ly))*$k,
        ($x+$lx)*$k,($h-($y-$ry))*$k,
        $x*$k,($h-($y-$ry))*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x-$lx)*$k,($h-($y-$ry))*$k,
        ($x-$rx)*$k,($h-($y-$ly))*$k,
        ($x-$rx)*$k,($h-$y)*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x-$rx)*$k,($h-($y+$ly))*$k,
        ($x-$lx)*$k,($h-($y+$ry))*$k,
        $x*$k,($h-($y+$ry))*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
        ($x+$lx)*$k,($h-($y+$ry))*$k,
        ($x+$rx)*$k,($h-($y+$ly))*$k,
        ($x+$rx)*$k,($h-$y)*$k,
        $op));
}
}
$pdf= new PDF();
$pdf->AddPage();
$pdf->SetFont('Times','I',30);
$pdf->SetTextColor(0,179,0);
$pdf->Text(70,20,'Business Profile');
$pdf->SetFont('Times','BU',15);
$pdf->SetTextColor(0,0,0);
$pdf->Text(20,40,'General Business Information');
$sql="SELECT * FROM companyprofile WHERE id='banuwo'";
$sql1="SELECT * FROM personal WHERE id='banuwo'";
$sql2="SELECT * FROM sales WHERE id='banuwo'";
$sql3="SELECT * FROM financial WHERE id='banuwo'";
$result=mysqli_query($conn,$sql);  $result1=mysqli_query($conn,$sql1);  $result2=mysqli_query($conn,$sql2);  $result3=mysqli_query($conn,$sql3);
$row=mysqli_fetch_array($result);  $row1=mysqli_fetch_array($result1);  $row2=mysqli_fetch_array($result2);  $row3=mysqli_fetch_array($result3);
$pdf->SetFont('Times','',12);
$pdf->Circle(28,49,1,'F');
$pdf->Text(32,50,'Business Name: ');
$pdf->Text(62,50,$row['cName']);
$pdf->Image($row['sicon'],120,40,50,30);
$pdf->Circle(28,57,1,'F');
$pdf->Text(32,58,'Office Address: ');
$pdf->Text(62,58,$row['cLocation']);
$pdf->Circle(28,65,1,'F');
$pdf->Text(32,66,'Phone Number: ');
$pdf->Text(62,66,$row['cNumber']);
$pdf->Circle(28,74,1,'F');
$pdf->Text(32,75,'Company URL: ');
$pdf->Text(62,75,$row['curl']);
$pdf->Circle(28,82,1,'F');
$pdf->Text(32,83,'Business Status: ');
$pdf->Text(62,83,$row['cBusiness']);
$pdf->Circle(28,90,1,'F');
$pdf->Text(32,91,'Person In Charge: ');
$pdf->Text(65,91,$row['cUsername']);
$pdf->SetFont('Times','BU',15);
$pdf->SetTextColor(0,0,0);
$pdf->Text(20,102,'Business Details');
$pdf->SetFont('Times','',12);
$pdf->Circle(28,112,1,'F');
$pdf->Text(32,113,'Main Areas of Business: ');
$pdf->Text(75,113,$row['cIndustry']);
$pdf->Circle(28,121,1,'F');
$pdf->Text(32,122,'Business Stating Year: ');
$pdf->Text(72,122,$row['csy']);
$pdf->Circle(28,130,1,'F');
$pdf->Text(32,131,'Business Stage: ');
$pdf->Text(60,131,$row['cStage']);
$pdf->Circle(28,139,1,'F');
$pdf->Text(32,140,'Business Mission: ');
$pdf->SetXY(63,136);
$pdf->MultiCell(140,5,$row['cMission'],0,0,'');
$pdf->SetFont('Times','BU',15);
$pdf->SetTextColor(0,0,0);
$pdf->Text(20,155,'Business Capacity');
$pdf->SetFont('Times','B',13);
$pdf->Text(30,165,'Human Resources');
$pdf->SetFont('Times','',12);
$pdf->Circle(35,171,1,'F');
$pdf->Text(38,172,'Number of Employees: ');   $emp=json_decode($row1['empname']);
$pdf->Text(79,172,count($emp));
$pdf->SetFont('Times','B',13);
$pdf->Text(30,182,'Financial');
$pdf->Circle(35,186,1,'F');
$pdf->SetFont('Times','',12);
$pdf->Text(38,187,'Sales Online (in %)');
$sol=json_decode($row2['salesoln']);  $sol1=json_decode($row2['salesol']); $sol2=json_decode($row2['salesol1']);
$sol3=json_decode($row2['salesol2']);
$d=array();  $data1=array();
for($i=0; $i < count($sol); $i++)
{
    $d[$i]=($sol1[$i]+$sol2[$i]+$sol3[$i]);
    $data1[$sol[$i]]=$d[$i];
}  
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(75,158);
$col1=array(100,100,255);
$col2=array(255,100,100);
$col3=array(255,255,100);
$pdf->PieChart(160,160, $data1, '%l (%p)', array($col1,$col2,$col3));
$pdf->SetXY($valX, $valY + 10);
$pdf->SetFillColor(0,0,0);
$pdf->Circle(115,186,1,'F');
$pdf->SetFont('Times','',12);
$pdf->Text(118,187,'Company Assets & Liability (in %)');
$aname=json_decode($row3['aname']); $ay1=json_decode($row3['ay1']); $ay2=json_decode($row3['ay2']); $ay3=json_decode($row3['ay3']);
$lname=json_decode($row3['lname']); $ly1=json_decode($row3['ly1']); $ly2=json_decode($row3['ly2']); $ly3=json_decode($row3['ly3']);
$dly=array();  $day=array();  $daly=array(); $daly1=array();  $asset=0; $liability=0;
for($i=0;$i<count($aname);$i++)
{
    $daly[-1]=0;
    $day[$i]=($ay1[$i]+$ay2[$i]+$ay3[$i]);
    $daly[$i]=$daly[$i-1] + $day[$i];
    if($i == (count($aname)-1))
    {
    //$asset=$daly[$i];
    $label["Asset"]=$daly[$i];
    }
}
for($j=0;$j<count($lname);$j++)
{
    $daly1[-1]=0;
    $dly[$j]=($ly1[$j]+$ly2[$j]+$ly3[$j]);
    $daly1[$j]=$daly1[$j-1] + $dly[$j];
    if($j == (count($lname)-1))
    {
        //$liability=$daly1[$j];
        $label["Liability"]=$daly1[$j];
    }
}
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(85,160);
$col1=array(77,255,77);
$col2=array(255,77,77);
$col3=array(255,77,255);
$pdf->PieChart1(85,60, $label, '%l (%p)', array($col1,$col2,$col3));
$pdf->SetXY($valX, $valY + 10);
$pdf->SetFont('Times','B',13);
$pdf->Text(5,292,$row['dateedited']);
$pdf->SetXY(10,260);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(155,10,'Funding: Rs.',1,0);
$pdf->SetXY(40,260);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(155,10,'25000',0,0);
$path = 'QRimages/';
$file = $path."custom_".$row['cName'].".png";
$code = $row['curl'];
$ecc = 'H'; 
$pixel_Size = 4; 
$frame_Size = 8; 
QRcode::png($code, $file, $ecc, $pixel_Size,$frame_Size);//QR code generator
$pdf->Image($file,170,257,35,35,'PNG');
$pdf->SetLineWidth(1.5);
$pdf->SetAlpha(0.2);
$pdf->RotatedImage('bekreta.png',60,150,100,50,50);
$pdf->SetAlpha(1);
$pdf->Output('I','Business Profile');
?>