<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
include('database.php');

require('fpdf/diag.php');
class PDF extends FPDF
{
    var $angle=0;

protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';

function LineGraph($w, $h, $data, $options='', $colors=null, $maxVal=0, $nbDiv=4){
    /*******************************************
    Explain the variables:
    $w = the width of the diagram
    $h = the height of the diagram
    $data = the data for the diagram in the form of a multidimensional array
    $options = the possible formatting options which include:
        'V' = Print Vertical Divider lines
        'H' = Print Horizontal Divider Lines
        'kB' = Print bounding box around the Key (legend)
        'vB' = Print bounding box around the values under the graph
        'gB' = Print bounding box around the graph
        'dB' = Print bounding box around the entire diagram
    $colors = A multidimensional array containing RGB values
    $maxVal = The Maximum Value for the graph vertically
    $nbDiv = The number of vertical Divisions
    *******************************************/
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(0);
    $keys = array_keys($data);
    $ordinateWidth = 10;
    $w -= $ordinateWidth;
    $valX = $this->getX()+$ordinateWidth;
    $valY = $this->getY();
    $margin = 1;
    $titleH = 8;
    $titleW = $w;
    $lineh = 5;
    $keyH = count($data)*$lineh;
    $keyW = $w/5;
    $graphValH = 5;
    $graphValW = $w-$keyW-3*$margin;
    $graphH = $h-(3*$margin)-$graphValH;
    $graphW = $w-(2*$margin)-($keyW+$margin);
    $graphX = $valX+$margin;
    $graphY = $valY+$margin;
    $graphValX = $valX+$margin;
    $graphValY = $valY+2*$margin+$graphH;
    $keyX = $valX+(2*$margin)+$graphW;
    $keyY = $valY+$margin+.5*($h-(2*$margin))-.5*($keyH);
    //draw graph frame border
    if(strstr($options,'gB')){
        $this->Rect($valX,$valY,$w,$h);
    }
    //draw graph diagram border
    if(strstr($options,'dB')){
        $this->Rect($valX+$margin,$valY+$margin,$graphW,$graphH);
    }
    //draw key legend border
    if(strstr($options,'kB')){
        $this->Rect($keyX,$keyY,$keyW,$keyH);
    }
    //draw graph value box
    if(strstr($options,'vB')){
        $this->Rect($graphValX,$graphValY,$graphValW,$graphValH);
    }
    //define colors
    if($colors===null){
        $safeColors = array(0,51,102,153,204,225);
        for($i=0;$i<count($data);$i++){
            $colors[$keys[$i]] = array($safeColors[array_rand($safeColors)],$safeColors[array_rand($safeColors)],$safeColors[array_rand($safeColors)]);
        }
    }
    //form an array with all data values from the multi-demensional $data array
    $ValArray = array();
    foreach($data as $key => $value){
        foreach($data[$key] as $val){
            $ValArray[]=$val;                    
        }
    }
    //define max value
    if($maxVal<ceil(max($ValArray))){
        $maxVal = ceil(max($ValArray));
    }
    //draw horizontal lines
    $vertDivH = $graphH/$nbDiv;
    if(strstr($options,'H')){
        for($i=0;$i<=$nbDiv;$i++){
            if($i<$nbDiv){
              //  $this->Line($graphX,$graphY+$i*$vertDivH,$graphX+$graphW,$graphY+$i*$vertDivH);
            } else{
                //$this->Line($graphX,$graphY+$graphH,$graphX+$graphW,$graphY+$graphH);
            }
        }
    }
    //draw vertical lines
    $horiDivW = floor($graphW/(count($data[$keys[0]])-1));
    if(strstr($options,'V')){
        for($i=0;$i<=(count($data[$keys[0]])-1);$i++){
            if($i<(count($data[$keys[0]])-1)){
               // $this->Line($graphX+$i*$horiDivW,$graphY,$graphX+$i*$horiDivW,$graphY+$graphH);
            } else {
                //$this->Line($graphX+$graphW,$graphY,$graphX+$graphW,$graphY+$graphH);
            }
        }
    }
    //draw graph lines
    foreach($data as $key => $value){
        $this->setDrawColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
        $this->SetLineWidth(0.7);
        $valueKeys = array_keys($value);
        for($i=0;$i<count($value);$i++){
            if($i==count($value)-2){
                $this->Line(
                    $graphX+($i*$horiDivW),
                    $graphY+$graphH-($value[$valueKeys[$i]]/$maxVal*$graphH),
                    $graphX+$graphW,
                    $graphY+$graphH-($value[$valueKeys[$i+1]]/$maxVal*$graphH)
                );
            } else if($i<(count($value)-1)) {
                $this->Line(
                    $graphX+($i*$horiDivW),
                    $graphY+$graphH-($value[$valueKeys[$i]]/$maxVal*$graphH),
                    $graphX+($i+1)*$horiDivW,
                    $graphY+$graphH-($value[$valueKeys[$i+1]]/$maxVal*$graphH)
                );
            }
        }
        //Set the Key (legend)
        $this->SetFont('Courier','',9);
        if(!isset($n))$n=0;
        $this->Line($keyX+1,$keyY+$lineh/2+$n*$lineh,$keyX+2,$keyY+$lineh/2+$n*$lineh);
        $this->SetXY($keyX+2,$keyY+$n*$lineh);
        $this->Cell($keyW,$lineh,$key,0,1,'L');
        $n++;
    }
    //print the abscissa values
    foreach($valueKeys as $key => $value){
        if($key==0){
            $this->SetXY($graphValX,$graphValY);
            $this->Cell(30,$lineh,$value,0,0,'L');
        } else if($key==count($valueKeys)-1){
            $this->SetXY($graphValX+$graphValW-30,$graphValY);
            $this->Cell(30,$lineh,$value,0,0,'R');
        } else {
            $this->SetXY($graphValX+$key*$horiDivW-15,$graphValY);
            $this->Cell(30,$lineh,$value,0,0,'C');
        }
    }
    //print the ordinate values
    for($i=0;$i<=$nbDiv;$i++){
        $this->SetXY($graphValX-10,$graphY+($nbDiv-$i)*$vertDivH-3);
        $this->Cell(8,6,sprintf('%.1f',$maxVal/$nbDiv*$i),0,0,'L');
    }
    // $this->SetDrawColor(0,0,0);
    // $this->SetLineWidth(0.2);
}
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Times','',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    $this->SetY(282);
    $this->Cell(0,10,'Pitch Deck || Bekreta',0,0,'L');
}
function Title($label)
{
// Arial 12
$this->SetFont('Times','B',22);
// Background color
$this->SetFillColor( 189, 62, 67);
$this->SetTextColor( 249, 248, 129);
// Title
$this->Cell(0,9,"$label",7,2,'C',true);
// Line break
$this->Ln(8);
}
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='f';
        else
            $op='f';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc1($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc1($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc1($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc1($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc1($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
    
    // Page header

    function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
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
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        $x1*$this->k,
        ($h-$y1)*$this->k,
        $x2*$this->k,
        ($h-$y2)*$this->k,
        $x3*$this->k,
        ($h-$y3)*$this->k));
}
    var $legends;
    var $wLegend;
    var $sum;
    var $NbVal;

    function PieChart($w, $h, $data, $format, $colors=null)
    {
        $this->SetFont('Courier', '', 11);
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
                $this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }

        //Legends
        $this->SetFont('Courier', 'B', 11);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1, $y1, $hLegend, $hLegend, 'F');
            $this->SetXY($x2,$y1);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }

    function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
    {
        $this->SetFont('Courier', 'B', 11);
        $this->SetLegends($data,$format);

        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 0;
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

        $this->SetLineWidth(0);
      //  $this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

        $this->SetFont('Courier', 'B', 11);
        $this->SetFillColor($color[0],$color[1],$color[2]);
        $i=0;
        foreach($data as $val) {
            //Bar
            $xval = $XDiag;
            $lval = (int)($val * $unit);
            $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
            $hval = $eBaton;
            $this->Rect($xval, $yval, $lval, $hval, 'F');
            //Legend
            $this->SetXY(0, $yval);
            $this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'L');
            $i++;
        }

        //Scales
        for ($i = 0; $i <= $nbDiv; $i++) {
            $xpos = $XDiag + $lRepere * $i;
          //  $this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
            $val = $i * $valIndRepere;
            $xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
            $ypos = $YDiag + $hDiag - $margin;
            $this->Text($xpos, $ypos, $val);
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

      
    function WriteHTML($html)
    {
        // HTML parser
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
            }
            else
            {
                // Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extract attributes
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }
    
    function OpenTag($tag, $attr)
    {
        // Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }
    
    function CloseTag($tag)
    {
        // Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }
    
    function SetStyle($tag, $enable)
    {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }
    
    function PutLink($URL, $txt)
    {
        // Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

    
   
}
$pdf = new PDF_Diag();
$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->Image( 'images/pitch/grey.png', 0, 0, 210, 40);

$pdf->Image( 'images/pitch/blue1.png', 0, 40, 210, 30);

$pdf->Image( 'images/pitch/white.png', 0, 70, 210, 70);

$pdf->Image( 'images/pitch/dgrey.png', 0, 140, 210, 70);

$pdf->Image( 'images/pitch/white.png', 0, 210, 210, 60);

$pdf->Image( 'images/pitch/blue.png', 0, 270, 210, 30);

$pdf->Image( 'images/logo.jpg', 15, 10, 65, 20);
$pdf->SetFont('Times','B',18);
$pdf->SetTextColor(0);
$pdf->SetXY(120,8);
$pdf->MultiCell(0,5, "Sarestates Reality Advisors");
$pdf->SetFont('Arial','',12);
$pdf->SetXY(120,20);
$pdf->MultiCell(0,5, "Kurla,Mumbai - 400070");
$pdf->SetX(120);
$pdf->MultiCell(0,5, "+91-9773383276");
$pdf->SetX(120);
$pdf->MultiCell(0,5, "www.sareaptitude.tk");

$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0);
$pdf->SetXY(20,45);
$pdf->MultiCell(0,5, "Total Revenue");

$pdf->SetXY(95,45);
$pdf->MultiCell(0,5, "Total Sales");


$pdf->SetXY(165,45);
$pdf->MultiCell(0,5, "Cash Flow");


$pdf->SetFont('Arial','B',18);
$pdf->SetTextColor(0);

$pdf->SetXY(20,55);
$pdf->MultiCell(0,5, "85,000");


$pdf->SetXY(95,55);
$pdf->MultiCell(0,5, "1,00,5000");


$pdf->SetXY(165,55);
$pdf->MultiCell(0,5, "GOOD");


$pdf->SetFont('Arial','B',13);
$pdf->SetTextColor(0);
$pdf->SetXY(37,75);
$pdf->MultiCell(0,5, "THE PROBLEM");


$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(50,35,25);
$pdf->SetXY(10,85);
$pdf->MultiCell(90,5, "1.PROPERTY : We assist our clients to form a robust real estate portfolio and assure that our clients make ");
$pdf->MultiCell(90,5, "2.MORTGAGE : We assist our clients to form a robust real estate portfolio and assure that our clients make ");
$pdf->MultiCell(90,5, "3.ADVISOR : We assist our clients to form a robust real estate portfolio and assure that our clients make  ");
$pdf->MultiCell(90,5, "4.LEADING  :We assist our clients to form a robust real estate portfolio and assure that our clients make   ");


$pdf->SetFont('Arial','B',13);
$pdf->SetTextColor(0);
$pdf->SetXY(135,75);
$pdf->MultiCell(0,5, "THE SOLUTION");

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(50,35,25);
$pdf->SetXY(105,85);
$pdf->MultiCell(90,5, "1.PROPERTY : We assist our clients to form a robust real estate portfolio and assure that our clients make ");
$pdf->SetX(105);
$pdf->MultiCell(90,5, "2.MORTGAGE : We assist our clients to form a robust real estate portfolio and assure that our clients make ");
$pdf->SetX(105);
$pdf->MultiCell(90,5, "3.ADVISOR : We assist our clients to form a robust real estate portfolio and assure that our clients make  ");
$pdf->SetX(105);
$pdf->MultiCell(90,5, "4.LEADING  :We assist our clients to form a robust real estate portfolio and assure that our clients make   ");




$pdf->SetFont('Arial','B',13);
$pdf->SetTextColor(255);
$pdf->SetXY(130,145);
$pdf->MultiCell(0,5, "SALES SUMMARY");
//color
$col[0]=array(244, 67, 54);
$col[1]=array(156, 39, 176);
$col[2]=array(255, 193, 7);
$col[3]=array(63, 81, 181);
$col[4]=array(205, 220, 57);
$col[5]=array(139, 195, 74);
$col[6]=array(255, 152, 0);
$col[7]=array(53, 59, 72);
$col[8]=array(111, 30, 81);
$col[9]=array(0, 98, 102);
$col[10]=array(255,195,18);
$col[11]=array(87,88,187);
$data3=array();    
$pdf->SetTextColor(0);
$pdf->SetXY(95,155);

$data3["DIRECT SALES"]=5000;
$data3["CHANNEL SALES"]=2500;
$data3["OTHER SALES"]=2500;



$pdf->PieChart(120, 50, $data3, '%l (%p)',$col);

$data2["Year 1"]=5000;
$data2["Year 2"]=2500;
$data2["Year 3"]=2500;

$col1=array(100,100,255);

$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',13);
$pdf->SetXY(20,145);
$pdf->MultiCell(0,5, "CASH ANALYSIS");

$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor(0);
$pdf->SetXY(5,165);
$pdf->BarDiagram(80,30,$data2,'%l',$col[9]);

$pdf->SetFont('Arial','B',13);
$pdf->SetTextColor(0);
$pdf->SetXY(85,215);
$pdf->MultiCell(0,5, "TARGET MARKET");
$pdf->SetFont('Arial','',11);
$pdf->SetTextColor(0);
$pdf->SetXY(10,225);
$pdf->Image( 'images/pitch/1.png', 25, 225, 10, 10);
$pdf->SetXY(10,240);
$pdf->MultiCell(50,5, "Professionals, managers and executives");

$pdf->SetXY(85,225);
$pdf->Image( 'images/pitch/2.png', 100, 225, 10, 10);
$pdf->SetXY(85,240);
$pdf->MultiCell(50,5, "Sense of achievement and belonging Self-expression");

$pdf->SetXY(155,225);
$pdf->Image( 'images/pitch/3.png', 170, 225, 10, 10);
$pdf->SetXY(155,240);
$pdf->MultiCell(50,5, "Speed of service, advanced features and capabilities");


$pdf->SetFont('Times','U',10);
$pdf->SetTextColor(0);
$pdf->SetXY(45,271);
$pdf->MultiCell(0,5, "The Company Requires Funding of Rupees 10 Lakh For Further Investment to Grow.");


$pdf->Output();
// $pdf->Output("BusinessPlan.pdf","D");
?>