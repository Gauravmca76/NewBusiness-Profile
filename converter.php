<?php

require('diag11.php');

session_start();
include ('database.php');
class PDF extends FPDF 
{
    var $angle=0;

protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';


function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Times','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    $this->SetY(282);
    $this->Cell(0,10,'Business Plan',0,0,'L');
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
            $op='B';
        else
            $op='S';
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
        $op='b';
    else
        $op='s';
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
        $this->SetLineWidth(0.2);
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
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
            $this->SetXY($x2,$y1);
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
        $this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

        $this->SetFont('Courier', '', 10);
        $this->SetFillColor($color[0],$color[1],$color[2]);
        $i=0;
        foreach($data as $val) {
            //Bar
            $xval = $XDiag;
            $lval = (int)($val * $unit);
            $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
            $hval = $eBaton;
            $this->Rect($xval, $yval, $lval, $hval, 'DF');
            //Legend
            $this->SetXY(0, $yval);
            $this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
            $i++;
        }

        //Scales
        for ($i = 0; $i <= $nbDiv; $i++) {
            $xpos = $XDiag + $lRepere * $i;
            $this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
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

$pdf->AddPage();

$data = array('Men' => 1510, 'Women' => 1610, 'Children' => 1400);

//Pie chart
$pdf->SetFont('Arial', 'BIU', 12);
$pdf->Cell(0, 5, '1 - Pie chart', 0, 1);
$pdf->Ln(8);

$pdf->SetFont('Arial', '', 10);
$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->Cell(30, 5, 'Number of men:');
$pdf->Cell(15, 5, $data['Men'], 0, 0, 'R');
$pdf->Ln();
$pdf->Cell(30, 5, 'Number of women:');
$pdf->Cell(15, 5, $data['Women'], 0, 0, 'R');
$pdf->Ln();
$pdf->Cell(30, 5, 'Number of children:');
$pdf->Cell(15, 5, $data['Children'], 0, 0, 'R');
$pdf->Ln();
$pdf->Ln(8);

$pdf->SetXY(90, $valY);
$col1=array(100,100,255);
$col2=array(255,100,100);
$col3=array(255,255,100);
$pdf->PieChart(100, 35, $data, '%l (%p)', array($col1,$col2,$col3));
$pdf->SetXY($valX, $valY + 40);

//Bar diagram
$pdf->SetFont('Arial', 'BIU', 12);
$pdf->Cell(0, 5, '2 - Bar diagram', 0, 1);
$pdf->Ln(8);
$valX = $pdf->GetX();
$valY = $pdf->GetY();
$pdf->BarDiagram(190, 70, $data, '%l : %v (%p)', array(255,175,100));
$pdf->SetXY($valX, $valY + 80);
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->Image( 'res/bplanpdf.jpg', 0, 0, 210, 298);
$sql = "SELECT * FROM bplan where bemail=''";
        $result = mysqli_query($conn,$sql);

        $rows=mysqli_fetch_array($result,MYSQLI_NUM);
            $cName=$rows[2];
            $cUsername=$rows[3];
            $cEmailid=$rows[4];
            $cNumber=$rows[5];
            $cLocation=$rows[6];
            $cStage=$rows[7];
            $cBusiness=$rows[8];
            $cIndustry=$rows[9];
            $cVisionmission=$rows[10];
            $oname=$rows[11];
            $yexp=$rows[12];
            $mexp=$rows[13];
            $omoney=$rows[14];
            $fmoney=$rows[15];
            $prefcur=$rows[16];
            $lmoney=$rows[17];
            $swot=json_decode($rows[18], true);
            $s=json_decode($rows[19], true);
            $w=json_decode($rows[20], true);
            $o=json_decode($rows[21], true);
            $t=json_decode($rows[22], true);
            $cproblem=json_decode($rows[23], true);
            $csolution=json_decode($rows[24], true);
            $cgap=json_decode($rows[25], true);
            $ctarget=json_decode($rows[26], true);
            $dp1=json_decode($rows[27],true);
            $dp2=json_decode($rows[28], true);
            $dp3=json_decode($rows[29], true);
            $miledate =json_decode($rows[30], true); 
            $miledesc    =json_decode($rows[31], true);    
            //EMPLOYEE
            $empname =json_decode($rows[32], true); 
            $empdes =json_decode($rows[33], true); 
            $empyr =json_decode($rows[34], true); 
            $about =json_decode($rows[35], true); 
            //Finance
            $iprevenue1 =$rows[36];
            $iexpense1 =$rows[37];
            $iprawmaterial1 =$rows[38];
            $iplabour1 =$rows[39];
            $ipstore1 =$rows[40];
            $ipmanufacture1 =$rows[41];
            $ipsalary1 =$rows[42];
            $ipfc1 =$rows[43];
            $ipoffice1 =$rows[44];
            $iptax1 =$rows[45];
            $iprentmisc1 =$rows[46];
            $ipdepreciate1 =$rows[47];
            $ipcapital1 =$rows[48];
            $ipunsecured1 =$rows[49];
            $ipsundry1 =$rows[50];
            $ipbrent1 =$rows[51];
            $ipasset1 =$rows[52];
            $ipinvest1 =$rows[53];
            $ipstock1 =$rows[54];
            $ipdebtors1 =$rows[55];
            $ipbalance1 =$rows[56];
            $ipother1 =$rows[57];

            $iprevenue2 =$rows[58];
            $iexpense2 =$rows[59];
            $iprawmaterial2 =$rows[60];
            $iplabour2 =$rows[61];
            $ipstore2 =$rows[62];
            $ipmanufacture2 =$rows[63];
            $ipsalary2 =$rows[64];
            $ipfc2 =$rows[65];
            $ipoffice2 =$rows[66];
            $iptax2 =$rows[67];
            $iprentmisc2 =$rows[68];
            $ipdepreciate2 =$rows[69];
            $ipcapital2 =$rows[70];
            $ipunsecured2 =$rows[71];
            $ipsundry2 =$rows[72];
            $ipbrent2 =$rows[73];
            $ipasset2 =$rows[74];
            $ipinvest2 =$rows[75];
            $ipstock2 =$rows[76];
            $ipdebtors2 =$rows[77];
            $ipbalance2 =$rows[78];
            $ipother2 =$rows[79];

            $iprevenue3 =$rows[80];
            $iexpense3 =$rows[81];
            $iprawmaterial3 =$rows[82];
            $iplabour3 =$rows[83];
            $ipstore3 =$rows[84];
            $ipmanufacture3 =$rows[85];
            $ipsalary3 =$rows[86];
            $ipfc3 =$rows[87];
            $ipoffice3 =$rows[88];
            $iptax3=$rows[89];
            $iprentmisc3 =$rows[90];
            $ipdepreciate3 =$rows[91];
            $ipcapital3 =$rows[92];
            $ipunsecured3 =$rows[93];
            $ipsundry3 =$rows[94];
            $ipbrent3 =$rows[95];
            $ipasset3 =$rows[96];
            $ipinvest3 =$rows[97];
            $ipstock3 =$rows[98];
            $ipdebtors3 =$rows[99];
            $ipbalance3 =$rows[100];
            $ipother3 =$rows[101];
            $oln=json_decode($rows[102], true); 
            $olc=json_decode($rows[103], true); 
            $ofn=json_decode($rows[104], true); 
            $ofc=json_decode($rows[105], true); 
            $ds=$rows[106];
            $chs=$rows[107];



$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);

$pdf->Cell(190,35,'Company Overview',0,0,'C');
$pdf->Ln(28);
//Company Information
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);


$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 90 , 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 65, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->Cell(70,5,'Company Information',0,0,'C');
$pdf->Ln(14);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$pdf->MultiCell(0,5, "\t\t\t\to Business :".$cBusiness);$pdf->Ln(6);
$pdf->MultiCell(0,5, "\t\t\t\to Industry :".$cIndustry);$pdf->Ln(6);
$pdf->MultiCell(0,5, "\t\t\t\to Email :".$cEmailid);$pdf->Ln(6);          
$pdf->MultiCell(0,5, "\t\t\t\to Phone :".$cNumber);$pdf->Ln(6); 
$pdf->MultiCell(0,5, "\t\t\t\to Vision :");$pdf->Ln(4); 
$pdf->SetFont('Arial','B',11);
$vm=strlen("$cVisionmission");
if($vm>90)
{
    $dvm=str_split($cVisionmission,90);
    $sizedvm=sizeof($dvm);
    $i=0;
    
    while($sizedvm>0){
        $pdf->MultiCell(0,5,"\t\t\t\t\t".$dvm[$i]);$pdf->Ln(1);  
              
        $i++;
        $sizedvm--;
    }

}
else{
    $pdf->MultiCell(0,5,"\t\t\t\t\t".$cVisionmission);$pdf->Ln(4);  
    
}       


//Owner Information
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 155, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);


$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15,158, 180, 80, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,165);
$pdf->Cell(65,0,'Owner Information',0,0,'C');
$pdf->Ln(2);
$pdf->SetXY(10,175);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$pdf->MultiCell(0,5, "\t\t\t\to Owner Name :".$oname);$pdf->Ln(6);
$pdf->MultiCell(0,5, "\t\t\t\to Expericence :".$yexp." Year ".$mexp." Month");$pdf->Ln(6);
$pdf->MultiCell(0,5, "\t\t\t\to FUNDS :");$pdf->Ln(4);
$pdf->MultiCell(0,5, "\t\t\t\t\t\t* Owner Contribution :".number_format($omoney)." ".$prefcur);$pdf->Ln(4);
$pdf->MultiCell(0,5, "\t\t\t\t\t\t* Fund from Equity :".number_format($fmoney)." ".$prefcur);$pdf->Ln(4);
$pdf->MultiCell(0,5, "\t\t\t\t\t\t* Fund from Loan :".number_format($lmoney)." ".$prefcur);$pdf->Ln(4);
$bp="A Business plan is an essential document that every business requires. Business Plan contains company information, owner information, SWOT analysis, Goals of the company, etc. It also includes strategies to enlarge your business. A business plan is a road map of your business. It is focus internal as well as external factors of organization.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$bp);
           
           
//New Page......................
$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//Market Research
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Marketing Research',0,0,'C');
$pdf->Ln(28);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 85, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 65, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->Cell(70,6,'Problem\'s Faced',0,0,'C');
$pdf->SetXY(10,80);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$pdf->SetXY(15,80);
$n=sizeof($cproblem);
$i=0;
while($n>0)
{
    
$pdf->MultiCell(180,5, "\t".($i+1).".".$cproblem[$i]);$pdf->Ln(6);
$pdf->SetX(15);
$i++;
$n--;

}   

$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 155, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);


$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15,158, 180, 80, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,108);
$pdf->Cell(70,112,'Solution\'s Given',0,0,'C');
$pdf->Ln(2);
$pdf->SetXY(15,175);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);

$n=sizeof($csolution);
$i=0;
while($n>0)
{
    
$pdf->MultiCell(180,5, "\t".($i+1).".".$csolution[$i]);$pdf->Ln(6);
$pdf->SetX(15);
$i++;
$n--;

}   
$mr="Market research is an authentic process of collecting, analyzing & interpreting information about consumers. It is one of the fundamental components of business strategy. Market research helps to understand new business opportunities, interest of potential consumers that will indirectly increase the sales of the company. There are five basic methods of market research: focus groups, surveys, observations, personal interviews and field trials.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$mr);
//New Page......................
$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//Market Research
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Marketing Research',0,0,'C');
$pdf->Ln(28);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 85, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 65, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,66);
$pdf->Cell(70,6,'Market Gap',0,0,'C');
$pdf->SetXY(15,80);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$n=sizeof($cgap);
$i=0;
while($n>0)
{
    
$pdf->MultiCell(180,5, "\t".($i+1).".".$cgap[$i]);$pdf->Ln(6);
$pdf->SetX(15);
$i++;
$n--;

}   

$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 155, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);


$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15,158, 180, 80, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,97);
$pdf->Cell(70,135,'Target Market',0,0,'C');

$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$pdf->SetXY(15,175);
$n=sizeof($ctarget);
$i=0;
while($n>0)
{
    
$pdf->MultiCell(180,5, "\t".($i+1).".".$ctarget[$i]);$pdf->Ln(6);
$pdf->SetX(15);
$i++;
$n--;

}   
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$mr);

//new Page

$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//Market Research
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Swot Analysis',0,0,'C');
$pdf->Ln(28);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 85, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,66);
$pdf->MultiCell(180,5, "\t\t\t\t".$swot[0]);
$pdf->SetXY(15,80);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
//$n=sizeof($cgap);
  
$pdf->MultiCell(180,5, "o Strength :".$s[0]);$pdf->Ln(6);
$pdf->MultiCell(180,5, "\t\t\t\to Weaknesses :".$w[0]);$pdf->Ln(6);
$pdf->MultiCell(180,5, "\t\t\t\to Opportunites :".$o[0]);$pdf->Ln(6);
$pdf->MultiCell(180,5, "\t\t\t\to Threats :".$t[0]);$pdf->Ln(6);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 155, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
$pdf->Ln(2);


$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15,158, 180, 80, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 158, 180, 13, 0, 'F');

$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,162);
$pdf->MultiCell(180,5, "\t\t\t\t".$swot[1]);
$pdf->Ln(1);
$pdf->SetXY(15,173);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$pdf->MultiCell(180,5, "o Strength :".$s[1]);$pdf->Ln(6);
$pdf->MultiCell(180,5, "\t\t\t\to Weaknesses :".$w[1]);$pdf->Ln(6);
$pdf->MultiCell(180,5, "\t\t\t\to Opportunites :".$o[1]);$pdf->Ln(6);
$pdf->MultiCell(180,5, "\t\t\t\to Threats :".$t[1]);$pdf->Ln(6); 
$sa="SWOT analysis is a strategic planning technique.SWOT analysis includes 4 components strengths,weaknesses, opportunities & threats. SWOT analysis helps to identify objectives of the business, internal & external factors that affects business. Using the SWOT analysis technique organization can find creative ideas, overcome the weaknesses and expand the company.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30,3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$sa);
//new page

$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//Market Research    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(232, 187, 63);
    //(x,y,size)
    $pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
    $pdf->Ln(20);
    $pdf->SetFont('Times','B',20);
    $pdf->SetTextColor(0,0,5);
    $pdf->Cell(190,35,'Swot Analysis',0,0,'C');
    $pdf->Ln(28);
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(232, 187, 63);//orange
    //(x,y,size)
    $pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
    $pdf->Ln(2);
    
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(196, 221, 242);//blue
    //(x,y,size)
    $pdf->RoundedRect(15, 63, 180, 85, 0, 'F');
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(48, 96, 171);//blue
    //(x,y,size)
    $pdf->RoundedRect(15, 63, 180, 13, 0, 'F');
    $pdf->Ln(2);
    $pdf->SetFont('Times','B',16);
    $pdf->SetTextColor(255);
    $pdf->SetXY(10,66);
    $pdf->MultiCell(180,5, "\t\t\t\t".$swot[2]);
    $pdf->SetXY(15,80);
    $pdf->SetFont('Times','',14);
    $pdf->SetTextColor(0);
    //$n=sizeof($cgap);
      
    $pdf->MultiCell(180,5, "o Strength :".$s[2]);$pdf->Ln(6);
    $pdf->MultiCell(180,5, "\t\t\t\to Weaknesses :".$w[2]);$pdf->Ln(6);
    $pdf->MultiCell(180,5, "\t\t\t\to Opportunites :".$o[2]);$pdf->Ln(6);
    $pdf->MultiCell(180,5, "\t\t\t\to Threats :".$t[2]);$pdf->Ln(6);


if(!$swot[3]=="")
{
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(232, 187, 63);//orange
    //(x,y,size)
    $pdf->RoundedRect(15, 155, 180, 3, 0, 'F');
    $pdf->Ln(2);
    
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(48, 96, 171);//blue
    //(x,y,size)
    $pdf->RoundedRect(15, 158, 65, 13, 0, 'F');
    $pdf->Ln(2);
    
    
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(196, 221, 242);//blue
    //(x,y,size)
    $pdf->RoundedRect(15,158, 180, 80, 0, 'F');
    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(48, 96, 171);//blue
    //(x,y,size)
    $pdf->RoundedRect(15, 158, 180, 13, 0, 'F');
    
    $pdf->SetFont('Times','B',16);
    $pdf->SetTextColor(255);
    $pdf->SetXY(10,162);
    $pdf->MultiCell(180,5, "\t\t\t\t".$swot[3]);
    $pdf->Ln(1);
    $pdf->SetXY(15,173);
    $pdf->SetFont('Times','',14);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(180,5, "o Strength :".$s[3]);$pdf->Ln(6);
    $pdf->MultiCell(180,5, "\t\t\t\to Weaknesses :".$w[3]);$pdf->Ln(6);
    $pdf->MultiCell(180,5, "\t\t\t\to Opportunites :".$o[3]);$pdf->Ln(6);
    $pdf->MultiCell(180,5, "\t\t\t\to Threats :".$t[3]);$pdf->Ln(6); 
}
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$sa);
if(!$swot[4]=="")
{
    $pdf->AddPage();

    $pdf->SetLineWidth(0);
    $pdf->SetFillColor(48, 96, 171);
    $pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');
    
    $pdf->SetFont('Times','B',24);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(60,20,$cName);
    $pdf->Ln(2);
    $pdf->SetFont('Times','I',12);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(80,35,$cLocation);
    $pdf->Ln(2);
    //Market Research    $pdf->SetLineWidth(0);
        $pdf->SetFillColor(232, 187, 63);
        //(x,y,size)
        $pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
        $pdf->Ln(20);
        $pdf->SetFont('Times','B',20);
        $pdf->SetTextColor(0,0,5);
        $pdf->Cell(190,35,'Swot Analysis',0,0,'C');
        $pdf->Ln(28);
        $pdf->SetLineWidth(0);
        $pdf->SetFillColor(232, 187, 63);//orange
        //(x,y,size)
        $pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
        $pdf->Ln(2);
        
        $pdf->SetLineWidth(0);
        $pdf->SetFillColor(196, 221, 242);//blue
        //(x,y,size)
        $pdf->RoundedRect(15, 63, 180, 85, 0, 'F');
        $pdf->SetLineWidth(0);
        $pdf->SetFillColor(48, 96, 171);//blue
        //(x,y,size)
        $pdf->RoundedRect(15, 63, 180, 13, 0, 'F');
        $pdf->Ln(2);
        $pdf->SetFont('Times','B',16);
        $pdf->SetTextColor(255);
        $pdf->SetXY(10,66);
        $pdf->MultiCell(180,5, "\t\t\t\t".$swot[4]);
        $pdf->SetXY(15,80);
        $pdf->SetFont('Times','',14);
        $pdf->SetTextColor(0);
        //$n=sizeof($cgap);
          
        $pdf->MultiCell(180,5, "o Strength :".$s[4]);$pdf->Ln(6);
        $pdf->MultiCell(180,5, "\t\t\t\to Weaknesses :".$w[4]);$pdf->Ln(6);
        $pdf->MultiCell(180,5, "\t\t\t\to Opportunites :".$o[4]);$pdf->Ln(6);
        $pdf->MultiCell(180,5, "\t\t\t\to Threats :".$t[4]);$pdf->Ln(6);
        $pdf->SetFont('Times','',11);
        $pdf->SetTextColor(48, 96, 171);
        $pdf->SetXY(15,245);
        $pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30, 0, 'DF');
$pdf->SetXY(17,247);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 30, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$sa);
    

}



//Graphs of Sales



$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Sales Graph',0,0,'C');
$pdf->Ln(35);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetFont('Times', 'i', 16);
$pdf->SetTextColor(83, 92, 104);
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(0, 9, "Sales & Marketing based on Online media", 0, 'J', 1);
$pdf->Ln(2);
            //about sales analysis
            // $pdf->subTitle("Online Sales","");
            $pdf->Ln(10);
            //Pie chart
$pdf->SetXY(50, $valY);
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
$pdf->SetXY($valX+40, $valY+5);

            
          
        // foreach($dp1 as $x => $x_value) {
        //     echo "Key=" . $x . ", Value=" . $x_value;
        //     echo "<br>";
        // }
        $data1=array();
  
    for($j=0;$j<count($oln);$j++)
        {
            $data1[$oln[$j]]=$olc[$j];
           
            
        }
      
      
    $data2=array();    

    for($j=0;$j<count($ofc);$j++)
    {
        $data2[$ofn[$j]]=$ofc[$j];
             
    }
    $data3=array();    

    
        $data3["DIRECT SALES"]=$ds;
        $data3["CHANNEL SALES"]=$chs;
           
  
        $pdf->PieChart(140, 140, $data1, '%l (%p)' ,$col);
        $pdf->Ln(10);
        $pdf->Ln(10);
       
//about sales off analysis
$pdf->SetFont('Times', 'i', 16);
$pdf->SetTextColor(83, 92, 104);
$pdf->SetFillColor(255, 255, 255);
$pdf->Ln(2);
$pdf->Ln(2);$pdf->Ln(2);$pdf->Ln(2);
$pdf->MultiCell(180, 12, "Sales & Marketing based on Offline media", 0, 'J', 1);
$pdf->Ln(2);
            //about sales off analysis
        // $pdf->subTitle("Offile Sales","");
        
                 //Pie chart
$pdf->SetXY(140, $valY);
//color

$pdf->SetXY($valX+40, $valY+100);

            
          
           
        $pdf->PieChart(140, 140, $data2, '%l (%p)',$col);

        $dc="There are two ways a company can sell the product i.e. Direct Sales and Channel Sales. In direct sales company sell their product directly to clients but in Channel Sales Company sell the product through Third Party Company. An ideal organization should find right mix between direct sales & channel sales.";
        $pdf->SetFont('Times','',11);
        $pdf->SetTextColor(48, 96, 171);
        $pdf->SetXY(15,255);
        $pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 255, 180, 25, 3,13, 'DF');
$pdf->SetXY(17,257);
$pdf->MultiCell(175,6,$dc);


        $pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Sales Graph',0,0,'C');
$pdf->Ln(35);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);
//about sales  analysis
$pdf->SetFont('Times', 'i', 16);
$pdf->SetTextColor(83, 92, 104);
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(0, 5, "Describe your Sales (Direct and Channel)", 0, 'J', 1);
$pdf->Ln(2);
            //about sales  analysis
        
        //Pie chart
        $pdf->SetXY($valX+40, $valY+15);
//color
 
$pdf->PieChart(140, 140, $data3, '%l (%p)',$col);

$dc="There are two ways a company can sell the product i.e. Direct Sales and Channel Sales. In direct sales company sell their product directly to clients but in Channel Sales Company sell the product through Third Party Company. An ideal organization should find right mix between direct sales & channel sales.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 25, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$dc);

//Financial


$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//SWOT Analysis
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',18);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Financial Information ',0,0,'C');
$pdf->Ln(28);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);
if (isset($cStage)) {
    if ($cStage == 'Idea' || $cStage == 'Start-up' ) {
        $pdf->Title("Financial's as on " . date("Y", strtotime('-1 year')) . "");
    } else {
        $pdf->Title("Financial's as on " . date("Y") . "");
    }
}
$pdf->SetX(50);
$w = array("Particular's","Amount in ".$prefcur);
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Times','B',14);
$w1 = array(60, 50);
for ($i = 0; $i < 2; $i++) {        
$pdf->Cell($w1[$i],8,$w[$i],1,0,'C',True);
}        
$pdf->Ln();
$pdf->SetFillColor(63, 81, 181);
$pdf->SetTextColor(0);
$pdf->SetFont('Times','',12);
// Data
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Revenue",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($iprevenue1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Expense",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($iexpense1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
if($cBusiness=="Manufacturing")
{
$pdf->Cell($w1[0],7,"Manufacturing",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipmanufacture1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Salary",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipsalary1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Raw Material",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($iprawmaterial1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Labour Charges",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($iplabour1),'LR',0,'R',false);
$pdf->Ln();  
$pdf->SetX(50);  
$pdf->Cell($w1[0],7,"Store Charges",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipstore1),'LR',0,'R',false);
$pdf->Ln();

}
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Depreciation",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipdepreciate1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Office Expense",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipoffice1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Tax",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($iptax1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Fixed Cost",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipfc1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0],7,"Rent & Misc Expenses",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($iprentmisc1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell(110,0,'','T');
$pdf->Ln();
$pdf->SetX(50);
$t=$iprevenue1+$iexpense1+$iprawmaterial1+$iplabour1+$ipstore1+$ipsalary1+$ipfc1+$ipoffice1+$iptax1+$iprentmisc1+$ipdepreciate1;
$pdf->Cell($w1[0],7,"Total",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($t),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell(110,0,'','T');
$pdf->Ln(6);

//Balance Sheet start------------------------------------
$pdf->Title("Balance Sheet");
$w = array("Liabilities in ".$prefcur,"Assets in ".$prefcur);
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Times','B',14);
$pdf->SetX(20);
$w1 = array(85,85);
for ($i = 0; $i < 2; $i++) {        
$pdf->Cell($w1[$i],8,$w[$i],1,0,'C',True);
}        
$pdf->Ln();
$pdf->SetFillColor(236, 240, 241);
$pdf->SetTextColor(0);
$pdf->SetFont('Times','',12);
$w1 = array(55,30,55,30);
// Data
$pdf->SetX(20);
$pdf->Cell($w1[0],7,"Capital",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipcapital1),'LR',0,'R',false);


$pdf->Cell($w1[2],7,"Fixed Asset",'LR',0,'L',false);
$pdf->Cell($w1[3],7,number_format($ipasset1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0],7,"Unsecured Loan",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipunsecured1),'LR',0,'R',false);

$pdf->Cell($w1[2],7,"Investment",'LR',0,'L',false);
$pdf->Cell($w1[3],7,number_format($ipinvest1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);

$pdf->Cell($w1[2],7,"Closing Stock",'LR',0,'L',false);
$pdf->Cell($w1[3],7,number_format($ipstock1),'LR',0,'R',false);
$pdf->Cell($w1[0],7,"",'LR',0,'L',false);
$pdf->Cell($w1[1],7,"",'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0],7,"Sundry Creditor's",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipsundry1),'LR',0,'R',false);

$pdf->Cell($w1[2],7,"Receivable Debtors",'LR',0,'L',false);
$pdf->Cell($w1[3],7,number_format($ipdebtors1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0],7,"Other Outstanding Rent",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($ipbrent1),'LR',0,'R',false);

$pdf->Cell($w1[2],7,"Cash & Bank Balance",'LR',0,'L',false);
$pdf->Cell($w1[3],7,number_format($ipbalance1),'LR',0,'R',false);
$pdf->Ln();$pdf->SetX(20);
$pdf->Cell($w1[0],7,"",'LR',0,'L',false);
$pdf->Cell($w1[1],7,"",'LR',0,'R',false);

$pdf->Cell($w1[2],7,"Others",'LR',0,'L',false);
$pdf->Cell($w1[3],7,number_format($ipother1),'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(170,0,'','T');
$pdf->Ln();$pdf->SetX(20);
$t1=$ipcapital1+ $ipunsecured1+$ipsundry1 +$ipbrent1;
$t2=$ipasset1+$ipinvest1+$ipstock1+ $ipdebtors1+$ipbalance1+$ipother1;
$pdf->Cell($w1[0],7,"Total",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($t1),'LR',0,'R',false);
$pdf->Cell($w1[0],7,"Total",'LR',0,'L',false);
$pdf->Cell($w1[1],7,number_format($t2),'LR',0,'R',false);
$pdf->Ln();$pdf->SetX(20);
$pdf->Cell(170,0,'','T');
$pdf->Ln(2);

//Financial Table current Table--------------------------------
$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//SWOT Analysis
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(30);
$pdf->SetFont('Times','B',20);
if (isset($cStage)) {
if ($cStage == 'Idea' || $cStage == 'Start-up' ) {
$pdf->Title("Financial's as on " . date("Y") . "");
} else {
$pdf->Title("Financial's as on " . date("Y", strtotime('+1 year')) . "");
}
}                $pdf->SetX(50);
$pdf->SetFont('Times','B',16);
$w = array("Particular's", "Amount in " . $prefcur);
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(.3);

$w1 = array(60, 50);
for ($i = 0; $i < 2; $i++) {
$pdf->Cell($w1[$i], 8, $w[$i], 1, 0, 'C', True);
}
$pdf->Ln();
$pdf->SetFillColor(63, 81, 181);
$pdf->SetTextColor(0);
$pdf->SetFont('Times','',12);
// Data
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Revenue", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iprevenue2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Expense", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iexpense2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
if ($cBusiness == "Manufacturing") {
$pdf->Cell($w1[0], 7, "Manufacturing", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipmanufacture2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Salary", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipsalary2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Raw Material", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iprawmaterial2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Labour Charges", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iplabour2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Store Charges", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipstore2), 'LR', 0, 'R', false);
$pdf->Ln();
}
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Depreciation", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipdepreciate2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Office Expense", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipoffice2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Tax", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iptax2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Fixed Cost", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipfc2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Rent & Misc Expenses", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iprentmisc2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell(110, 0, '', 'T');
$pdf->Ln();
$pdf->SetX(50);
$t = $iprevenue2 + $iexpense2 + $iprawmaterial2 + $iplabour2 + $ipstore2 + $ipsalary2 + $ipfc2 + $ipoffice2 + $iptax2 + $iprentmisc2 + $ipdepreciate2;
$pdf->Cell($w1[0], 7, "Total", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($t), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell(110, 0, '', 'T');
$pdf->Ln(6);

//Balance Sheet start------------------------------------
$pdf->Title("Balance Sheet");
$w = array("Liabilities in " . $prefcur, "Assets in " . $prefcur);
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Times','B',14);
$pdf->SetX(20);
$w1 = array(85, 85);
for ($i = 0; $i < 2; $i++) {
$pdf->Cell( $w1[$i], 8, $w[$i], 1, 0, 'C', True);
}
$pdf->Ln();
$pdf->SetFillColor(236, 240, 241);
$pdf->SetTextColor(0);
$pdf->SetFont('Times', '', 12);
$w1 = array(55, 30, 55, 30);
// Data
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Capital", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipcapital2), 'LR', 0, 'R', false);


$pdf->Cell($w1[2], 7, "Fixed Asset", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipasset2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Unsecured Loan", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipunsecured2), 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Investment", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipinvest2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[2], 7, "Closing Stock", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipstock2), 'LR', 0, 'R', false);
$pdf->Cell($w1[0],7,"",'LR',0,'L',false);
$pdf->Cell($w1[1],7,"",'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Sundry Creditor's", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipsundry2), 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Receivable Debtors", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipdebtors2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Other Outstanding Rent", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipbrent2), 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Cash & Bank Balance", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipbalance2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, "", 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Others", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipother2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(170, 0, '', 'T');
$pdf->Ln();
$pdf->SetX(20);
$t1 = $ipcapital2 + $ipunsecured2 + $ipsundry2 + $ipbrent2;
$t2 = $ipasset2 + $ipinvest2 + $ipstock2 + $ipdebtors2 + $ipbalance2 + $ipother2;
$pdf->Cell($w1[0], 7, "Total", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($t1), 'LR', 0, 'R', false);
$pdf->Cell($w1[0], 7, "Total", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($t2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(170, 0, '', 'T');
$pdf->Ln(2);
$fa="Financial is an area of finance that deals with accounting, funding, capital structuring, and investment decisions. It utilizes company's financial information to manage the money & develop organization's operation more beneficial.Business finance helps to connect the dots between profit & loss as well as balance sheet & cash flow statements. Business finance additionally provides tools to overcome any issues. It deals with financing.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 181, 33, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(178,6,$fa);

//Financials end second year

//Financial Table last Table--------------------------------
$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//SWOT Analysis
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->SetFont('Times','B',18);
$pdf->Ln(30);
    if (isset($cStage)) {
        if ($cStage == 'Idea' || $cStage == 'Start-up' ) {
            $pdf->Title("Financial's as on " . date("Y", strtotime('+1 year')) . "");
        } else {
            $pdf->Title("Financial's as on " . date("Y", strtotime('+2 year')) . "");
        }
    }
$pdf->SetX(50);
$w = array("Particular's", "Amount in " . $prefcur);
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Times','B',16);
$w1 = array(60, 50);
for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($w1[$i], 8, $w[$i], 1, 0, 'C', True);
}
$pdf->Ln();
$pdf->SetFillColor(63, 81, 181);
$pdf->SetTextColor(0);
$pdf->SetFont('Times', '', 12);
// Data
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Revenue", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iprevenue3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Expense", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iexpense3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
if ($cBusiness == "Manufacturing") {
    $pdf->Cell($w1[0], 7, "Manufacturing", 'LR', 0, 'L', false);
    $pdf->Cell($w1[1], 7, number_format($ipmanufacture3), 'LR', 0, 'R', false);
    $pdf->Ln();
    $pdf->SetX(50);
    $pdf->Cell($w1[0], 7, "Salary", 'LR', 0, 'L', false);
    $pdf->Cell($w1[1], 7, number_format($ipsalary3), 'LR', 0, 'R', false);
    $pdf->Ln();
    $pdf->SetX(50);
    $pdf->Cell($w1[0], 7, "Raw Material", 'LR', 0, 'L', false);
    $pdf->Cell($w1[1], 7, number_format($iprawmaterial3), 'LR', 0, 'R', false);
    $pdf->Ln();
    $pdf->SetX(50);
    $pdf->Cell($w1[0], 7, "Labour Charges", 'LR', 0, 'L', false);
    $pdf->Cell($w1[1], 7, number_format($iplabour3), 'LR', 0, 'R', false);
    $pdf->Ln();
    $pdf->SetX(50);
    $pdf->Cell($w1[0], 7, "Store Charges", 'LR', 0, 'L', false);
    $pdf->Cell($w1[1], 7, number_format($ipstore3), 'LR', 0, 'R', false);
    $pdf->Ln();
}
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Depreciation", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipdepreciate3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Office Expense", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipoffice3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Tax", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iptax3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Fixed Cost", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipfc3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell($w1[0], 7, "Rent & Misc Expenses", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($iprentmisc3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell(110, 0, '', 'T');
$pdf->Ln();
$pdf->SetX(50);
$t = $iprevenue3 + $iexpense3 + $iprawmaterial3 + $iplabour3 + $ipstore3 + $ipsalary3 + $ipfc3 + $ipoffice3 + $iptax3 + $iprentmisc3 + $ipdepreciate3;
$pdf->Cell($w1[0], 7, "Total", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($t), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(50);
$pdf->Cell(110, 0, '', 'T');
$pdf->Ln(6);

//Balance Sheet start------------------------------------
$pdf->Title("Balance Sheet");
$w = array("Liabilities in " . $prefcur, "Assets in " . $prefcur);
$pdf->SetFillColor(243, 156, 18);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(63, 81, 181);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Times','B',14);
$pdf->SetX(20);
$w1 = array(85, 85);
for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($w1[$i], 8, $w[$i], 1, 0, 'C', True);
}
$pdf->Ln();
$pdf->SetFillColor(236, 240, 241);
$pdf->SetTextColor(0);
$pdf->SetFont('Times', '', 12);
$w1 = array(55, 30, 55, 30);
// Data
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Capital", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipcapital3), 'LR', 0, 'R', false);


$pdf->Cell($w1[2], 7, "Fixed Asset", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipasset3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Unsecured Loan", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipunsecured3), 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Investment", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipinvest3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[2], 7, "Closing Stock", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipstock3), 'LR', 0, 'R', false);
$pdf->Cell($w1[0],7,"",'LR',0,'L',false);
$pdf->Cell($w1[1],7,"",'LR',0,'R',false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Sundry Creditor's", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipsundry3), 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Receivable Debtors", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipdebtors3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "Other Outstanding Rent", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($ipbrent3), 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Cash & Bank Balance", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipbalance3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell($w1[0], 7, "", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, "", 'LR', 0, 'R', false);

$pdf->Cell($w1[2], 7, "Others", 'LR', 0, 'L', false);
$pdf->Cell($w1[3], 7, number_format($ipother3), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(170, 0, '', 'T');
$pdf->Ln();
$pdf->SetX(20);
$t1 = $ipcapital3 + $ipunsecured3 + $ipsundry3 + $ipbrent3;
$t2 = $ipasset3 + $ipinvest3 + $ipstock3 + $ipdebtors3 + $ipbalance3 + $ipother3;
$pdf->Cell($w1[0], 7, "Total", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($t1), 'LR', 0, 'R', false);
$pdf->Cell($w1[0], 7, "Total", 'LR', 0, 'L', false);
$pdf->Cell($w1[1], 7, number_format($t2), 'LR', 0, 'R', false);
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(170, 0, '', 'T');
$pdf->Ln(2);
$fa="Financial is an area of finance that deals with accounting, funding, capital structuring, and investment decisions. It utilizes company's financial information to manage the money & develop organization's operation more beneficial.Business finance helps to connect the dots between profit & loss as well as balance sheet & cash flow statements. Business finance additionally provides tools to overcome any issues. It deals with financing.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 181, 33, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(178,6,$fa);

// Mile Stones 

$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//Market Research
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);
$pdf->Cell(190,35,'Strategic Goals and Action',0,0,'C');
$pdf->Ln(28);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(10, 60, 190, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(10, 63, 190, 125, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(10, 63, 190, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,65);
$pdf->Cell(35,6,'Milestones',0,0,'C');
$pdf->SetXY(10,80);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$n=sizeof($miledate);
$i=0;
while($n>0)
{
    
$pdf->MultiCell(180,5,($i+1).".Date :".$miledate[$i]);$pdf->Ln(4);
$pdf->SetX(14);
$pdf->MultiCell(180,5, "Description -\n".$miledesc[$i]);$pdf->Ln(6);
$i++;
$n--;

}   

$ms="Milestones are a crucial part of the business planning. If you want to know where your business is heading or where your business will be in few years then you need to calculate your milestones. Milestones provide perspective about the future of the organization & support you to measure the progress as you move forward. There are two categories of milestones short-term milestones & Long-term milestones.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 29, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$ms);
//Core Team Member
$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);
$pdf->Ln(2);
//Market Research
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln(20);
$pdf->SetFont('Times','B',20);
$pdf->SetTextColor(0,0,5);

$pdf->Cell(190,35,'Core Team Member\'s',0,0,'C');

$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);//orange
//(x,y,size)
$pdf->RoundedRect(15, 60, 180, 3, 0, 'F');
$pdf->Ln(2);

$pdf->SetLineWidth(0);
$pdf->SetFillColor(196, 221, 242);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 165, 0, 'F');
$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);//blue
//(x,y,size)
$pdf->RoundedRect(15, 63, 180, 13, 0, 'F');
$pdf->Ln(2);
$pdf->SetFont('Times','B',16);
$pdf->SetTextColor(255);
$pdf->SetXY(10,67);
$pdf->Cell(52,6,'Core Employee',0,0,'C');
$pdf->Ln(14);
$pdf->SetFont('Times','',14);
$pdf->SetTextColor(0);
$pdf->SetXY(10,80);
$n=sizeof($empname);
$i=0;
while($n>0)
{
    
    $pdf->MultiCell(0,5, "\t\t\t\t".($i+1).".Name :".$empname[$i]);$pdf->Ln(4);
    $pdf->MultiCell(0,5, "\t\t\t\t\t\tDesignation :".$empdes[$i]);$pdf->Ln(4);
    $pdf->MultiCell(0,5, "\t\t\t\t\t\tExperience :".$empyr[$i]);$pdf->Ln(4);
  //  $pdf->MultiCell(0,5, "\t\t\t\t\t\tAbout :".$about[$i]);$pdf->Ln(4);
$pdf->Ln(2);
$i++;
$n--;

}   
$cm="Core members are permanent employees that contribute skills & knowledge to the organization.It promotes organization's growth & financial stories for years to come.Core members are also the founding members of the organization and also the pillars of strength for the company.";
$pdf->SetFont('Times','',11);
$pdf->SetTextColor(48, 96, 171);
$pdf->SetXY(15,245);
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255);
$pdf->RoundedRect(15, 245, 180, 24, 3,13, 'DF');
$pdf->SetXY(17,247);
$pdf->MultiCell(175,6,$cm);

//Thank you
$pdf->AddPage();

$pdf->SetLineWidth(0);
$pdf->SetFillColor(48, 96, 171);
$pdf->RoundedRect(-10, 0, 230, 36, 0, 'DF');

$pdf->SetFont('Times','B',24);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(60,20,$cName);
$pdf->Ln(2);
$pdf->SetFont('Times','I',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,35,$cLocation);

$pdf->Ln(2);
//thankyou
$pdf->SetLineWidth(0);
$pdf->SetFillColor(232, 187, 63);
//(x,y,size)
$pdf->RoundedRect(0, 36, 230, 3, 0, 'F');
$pdf->Ln();
$pdf->Image( 'res/thankyou.jpg', 20, 40, 150, 80);
$pdf->SetXY(15,150);
$html = '<center><h2>Goto Our Website :-</h2> <a href="http://www.sarestatesbiz.tk">
www.bikreta.com </a></center>';
$link = $pdf->AddLink();
$pdf->SetLink($link);
//$pdf->Image('logo.png',10,12,30,0,'','http://www.fpdf.org');
$pdf->SetLeftMargin(45);
$pdf->SetFontSize(14);
$pdf->WriteHTML($html);
$pdf->Ln();
//$pdf->Cell(120,10,'THANKYOU !!!',0,1,'C');
$pdf->Output();
?>