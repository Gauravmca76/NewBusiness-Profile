<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
include('database.php');
require('fpdf/fpdf.php');

//set colors for gradients (r,g,b) or (grey 0-255)
$cprofile = "SELECT * FROM companyprofile where id='banuwo'";
$result1 = mysqli_query($conn, $cprofile);
$rows1 = mysqli_fetch_array($result1, MYSQLI_NUM);
$cName = $rows1[2];
$cEmailid = $rows1[5];
$cNumber = $rows1[6];
$curl = $rows1[7];
$cStage = $rows1[9];
$cBusiness = $rows1[10];
$cIndustry = $rows1[11];
$csy=$rows1[12];
$cVision = $rows1[13];
$cMission = $rows1[14];

$oname = $rows1[15];
$yexp = $rows1[16];
$mexp = $rows1[17];
$omoney = $rows1[18];
$prefcur = $rows1[19];

$cmarket = "SELECT * FROM marketreasearch where id='banuwo'";
$result2 = mysqli_query($conn, $cmarket);
$rows2 = mysqli_fetch_array($result2, MYSQLI_NUM);
$swotcname = json_decode($rows2[1], true);
$s = json_decode($rows2[2], true);
$w = json_decode($rows2[3], true);
$o = json_decode($rows2[4], true);
$t = json_decode($rows2[5], true);
$prob = json_decode($rows2[6], true);
$sol = json_decode($rows2[7], true);
$mar = json_decode($rows2[8], true);
$tar = json_decode($rows2[9], true);


$cpersonal = "SELECT * FROM personal where id='banuwo'";
$result5 = mysqli_query($conn, $cpersonal);
$rows5 = mysqli_fetch_array($result5, MYSQLI_NUM);
$edate = json_decode($rows5[1], true);
$mname = json_decode($rows5[2], true);
$empname = json_decode($rows5[3], true);
$design = json_decode($rows5[4], true);
$exp = json_decode($rows5[5], true);
$about = json_decode($rows5[6], true);

class PDF extends FPDF
{

    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }
    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
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
    var $legends;
    var $wLegend;
    var $sum;
    var $NbVal;
    
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


   

    function PieChart($w, $h, $data, $format, $colors = null)
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
            $this->Rect($x1, $y1, $hLegend, $hLegend, 'F');
            $this->SetXY($x2,$y1);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }

    function balancesheet($heading, $val)
    {
        // Colors, line width and bold font
        $this->SetFillColor(195, 230, 203);
        $this->SetTextColor(0);
        $this->SetDrawColor(255, 255, 255);
        $this->SetLineWidth(0);
        $this->SetFont('', 'B',10);
        // Header
        $w = array(40, 35, 40, 45);
        for ($i = 0; $i < count($heading); $i++)
            $this->Cell(48, 11, $heading[$i], 0, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data

        
        $fill = false;
        foreach ($val as $row1) {
            $this->Cell(48, 10, $row1[0], 'LR', 0, 'L', $fill);
            $this->Cell(48, 10, 'Rs. '.number_format($row1[1]), 'LR', 0, 'R', $fill);
            $this->Cell(48, 10, $row1[2], 'LR', 0, 'L', $fill);
            $this->Cell(48, 10, 'Rs. '.number_format($row1[3]), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
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
}




// First Page - company profile
$pdf = new PDF();
$pdf->AddPage();
//$pdf->Image('../../logo/classicover2020.jpg', 0, 0, 210, 298);
$pdf->SetFont('arial', '', 33);
$pdf->SetY(216);
$pdf->SetTextColor(44, 62, 80);
$pdf->Cell(0, 8, $cName, 0, 1, 'C');
$pdf->SetFont('arial', '', 12);
$pdf->SetY(265);
$pdf->Cell(0, 8, "Email us at : " . $cEmailid, 0, 1, 'L');
$pdf->SetY(265);
$pdf->Cell(0, 8, "Call us at :" . $cNumber, 0, 1, 'R');

$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(44, 62, 80, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');


$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(44, 62, 80);
$pdf->Cell(190, 35, 'Company Profile', 0, 0, 'C');
$pdf->Ln(30);
//$pdf->Image('../../logo/factory.png', 40, 100, 130, 90);
$pdf->SetFont('arial', '', 11);
$pdf->SetTextColor(30, 39, 46);
//$pdf->Image('../../logo/tick.png', 10, 42, 5, 5);
$pdf->SetX(20);
$pdf->MultiCell(0, 7, "A Company Profile is an essential part of a business plan & organization.", 0, 'J');
$pdf->Ln(2);
//$pdf->Image('../../logo/tick.png', 10, 51, 5, 5);
$pdf->SetX(20);
$pdf->MultiCell(0, 7, "It is a Professional Identification which helps to create identity of a company.", 0, 'J');
$pdf->Ln(2);
//$pdf->Image('../../logo/tick.png', 10, 60, 5, 5);
$pdf->SetX(20);
$pdf->MultiCell(0, 7, "It describes overview of a company to its partners, investors & shareholders.", 0, 'J');
$pdf->Ln(2);
//$pdf->Image('../../logo/tick.png', 10, 69, 5, 5);
$pdf->SetX(20);
$pdf->MultiCell(0, 7, "The Company Profile usually includes the products & services provided by the company, current position, short term & long term goals.", 0, 'J');
$pdf->Ln(2);
//$pdf->Image('../../logo/tick.png', 10, 85, 5, 5);
$pdf->SetX(20);
$pdf->MultiCell(0, 7, "It also contains the basic information about the company (Name, address, Contact number), all business strategies & activities, financial data & achievements of the business.", 0, 'J');


$pdf->SetLineWidth(1);
$pdf->Line(25, 100, 185, 100);
$pdf->Ln(8);

$pdf->SetTextColor(30, 39, 46);
//$pdf->Image('../../logo/bulb.png', 10, 120, 25, 30);
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, "Business : ", 0, 0, 'J');
$pdf->SetX(65);
$pdf->SetFont('arial', '', 12);
$pdf->Cell(0, 8, $cBusiness, 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, "Industry : ", 0, 0, 'J');
$pdf->SetX(65);
$pdf->SetFont('arial', '', 12);
$pdf->Cell(0, 8, $cIndustry, 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, "Our Vision: ", 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', '', 12);
$pdf->MultiCell(0, 8, $cVision, 0, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, "Our Mission: ", 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', '', 12);
$pdf->MultiCell(0, 8, $cMission, 0, 'J');

$pdf->SetLineWidth(1);
$pdf->Line(25, 200, 185, 200);
$pdf->Ln(15);


$pdf->SetTextColor(30, 39, 46);
//$pdf->Image('../../logo/mandollar.png', 10, 215, 25, 25);
$pdf->SetY(205);
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, "Owner Name : ", 0, 0, 'J');
$pdf->SetX(77);
$pdf->SetFont('arial', '', 12);
$pdf->Cell(0, 8, $oname, 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, "Experience : ", 0, 0, 'J');
$pdf->SetX(72);
$pdf->SetFont('arial', '', 12);
$pdf->Cell(0, 8, $yexp . " Years & " . $mexp . " months", 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, 'Owner Contribution :', 0, 0, 'J');
$pdf->SetX(86);
$pdf->SetFont('arial', '', 12);
$pdf->Cell(0, 8, "" . $omoney . " " . $prefcur, 0, 1, 'J');
$pdf->SetX(40);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 8, 'Establishment Year :', 0, 0, 'J');
$pdf->SetX(93);
$pdf->SetFont('arial', '', 12);
$pdf->Cell(0, 8, $csy, 0, 1, 'J');

$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(19, 15, 64);
$pdf->SetFont('arial', '', 10);
$pdf->SetY(263);
$pdf->MultiCell(0, 4, 'Note : Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting.', 1, 'J');


// Second Page - company profile
$pdf->AddPage();

$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(44, 62, 80, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(44, 62, 80);
$pdf->Cell(190, 35, 'Company Profile', 0, 0, 'C');
$pdf->Ln(30);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetX(15);
$pdf->Cell(0, 8, "Company Stage", 0, 1, 'L');
//$pdf->Image('../../logo/stage.png', 15, 50, 180, 50);
$pdf->Ln(57);
$pdf->SetFont('arial', 'U', 16);
$pdf->SetTextColor(109, 33, 79);
$pdf->MultiCell(0, 8, $cName . " is at " . $cStage . " stage.", 0, 'C');
$pdf->SetFont('arial', '', 12);
$pdf->SetTextColor(30, 39, 46);
$pdf->Ln(4);
$pdf->SetX(15);
$pdf->Cell(0, 8, "Company URL: ", 0, 0, 'L');
$pdf->SetTextColor(59, 59, 152);
$pdf->SetFont('arial', 'U', 16);
$pdf->SetX(51);
$pdf->Cell(0, 8, $curl, 0, 1, 'L');


// Third Page - Market Research
$pdf->AddPage();
//$pdf->Image('../../logo/market.png', 40, 80, 130, 130);
//$pdf->Image('../../logo/mrket.png', 114, 201, 90, 90);
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(190, 35, 'Market Research', 0, 0, 'C');
$pdf->Ln(30);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(83, 92, 104);
$pdf->MultiCell(0, 8, "Market Research is an authentic process of collecting, analzing & interpreting information about consumers. It is one of the fundamental components of business strategy. Market research helps to understand new business oppurtunities, intrest of potential consumers that will indirectly increase the sales of the company. There are five basic methods of market research: foucs groups, surveys, observations, personal interviews and field trials.", 0, 'C');
$pdf->Ln(6);
$pdf->SetX(15);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', 'U', 18);
$pdf->Cell(0, 8, "Problems Faced by Customers", 0, 0, 'C');
$pdf->Ln(20);
$pdf->SetFont('arial', '', 16);
for ($i = 0; $i < 10; $i++) {
    if ($prob[$i] != "") {
        $pdf->MultiCell(0, 5, ($i + 1) .  ") " . $prob[$i]);
        $pdf->Ln(5);
        $pdf->Ln(5);
    }
}


// fourth Page - Market Research
$pdf->AddPage();
//$pdf->Image('../../logo/market.png', 40, 80, 130, 130);
//$pdf->Image('../../logo/solution.png', 140, 225, 55, 55);
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(190, 35, 'Market Research', 0, 0, 'C');
$pdf->Ln(30);

$pdf->SetX(15);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', 'U', 18);
$pdf->Cell(0, 8, "Solution's Given", 0, 0, 'C');
$pdf->Ln(10);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(83, 92, 104);
$pdf->MultiCell(0, 8, "Gaps in the market represent opportunities for companies to expand their customer base by increasing awareness and creating targeted offers or advertising campaigns to reach the untapped market. Identification of gaps in the market is an important step in increasing market penetration.", 0, 'C');
$pdf->Ln(6);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', '', 16);
for ($i = 0; $i < 10; $i++) {
    if ($sol[$i] != "") {
        $pdf->MultiCell(0, 5, ($i + 1) . ") " . $sol[$i]);
        $pdf->Ln(5);
        $pdf->Ln(5);
    }
}
$pdf->Ln(75);


// fifth Page - Market Research
$pdf->AddPage();
//$pdf->Image('../../logo/market.png', 40, 80, 130, 130);
//$pdf->Image('../../logo/gap.png', 120, 201, 75, 75);
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(190, 35, 'Market Research', 0, 0, 'C');
$pdf->Ln(30);
$pdf->SetX(15);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', 'U', 18);
$pdf->Cell(0, 8, "Market Gap", 0, 0, 'C');
$pdf->Ln(10);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(83, 92, 104);
$pdf->MultiCell(0, 8, "Gaps in the market represent opportunities for companies to expand their customer base by increasing awareness and creating targeted offers or advertising campaigns to reach the untapped market. Identification of gaps in the market is an important step in increasing market penetration.", 0, 'C');
$pdf->Ln(6);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', '', 16);
for ($i = 0; $i < 10; $i++) {
    if ($mar[$i] != "") {
        $pdf->MultiCell(0, 5, ($i + 1) . ") " . $mar[$i]);
        $pdf->Ln(5);
        $pdf->Ln(5);
    }
}


// Sixth Page - Market Research
$pdf->AddPage();
//$pdf->Image('../../logo/market.png', 40, 80, 130, 130);
//$pdf->Image('../../logo/target.png', 120, 201, 75, 75);
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(190, 35, 'Market Research', 0, 0, 'C');
$pdf->Ln(30);
$pdf->SetX(15);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', 'U', 18);
$pdf->Cell(0, 8, "Target Market", 0, 0, 'C');
$pdf->Ln(10);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(83, 92, 104);
$pdf->MultiCell(0, 8, "A target market is a group of customers within a business's serviceable available market at which a business aims its marketing efforts and resources. A target market is a subset of the total market for a product or service.", 0, 'C');
$pdf->Ln(6);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', '', 16);
for ($i = 0; $i < 10; $i++) {
    if ($tar[$i] != "") {
        $pdf->MultiCell(0, 5, ($i + 1) . ") " . $tar[$i]);
        $pdf->Ln(5);
        $pdf->Ln(5);
    }
}


// Seventh Page - Swot Analysis
$pdf->AddPage();

$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 26);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(190, 35, 'Strength - Weakness - Oppurtunity - Threat', 0, 0, 'C');
$pdf->Ln(25);
$pdf->SetX(15);


//no 1 swot c
$pdf->SetY(51);
for ($i = 0; $i < 1; $i++) {
    if($swotcname[$i]!="")
    {
    $pdf->SetFillColor(255, 204, 204); //creamish
    $pdf->RoundedRect(12, 45, 85, 65, 5, 'F');
    $pdf->SetFont('arial', 'B', 14);
    $pdf->SetTextColor(196, 69, 0);
    $pdf->SetX(14);
    $pdf->Cell(75, 0, $swotcname[$i], 0, 0, 'C');
    $pdf->SetY(59);
    $pdf->SetFont('arial', '', 12);
    if ($s[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "S) " . $s[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($w[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "W) " . $w[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($o[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "O) " . $o[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($t[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "T) " . $t[$i], 0, 'L');
        $pdf->Ln(2);
    }
}
}
//no 2 swot c
$pdf->SetY(51);
for ($i = 1; $i < 2; $i++) {
    if($swotcname[$i]!="")
    {
    $pdf->SetFillColor(126, 214, 223); //skin blue
    $pdf->RoundedRect(107, 45, 85, 65, 5, 'F');
    $pdf->SetFont('arial', 'B', 14);
    $pdf->SetTextColor(44, 44, 84);
    $pdf->SetX(110);
    $pdf->Cell(70, 0, $swotcname[$i], 0, 0, 'C');
    $pdf->SetY(59);
    $pdf->SetFont('arial', '', 12);

    if ($s[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,   "S) " . $s[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($w[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "W) " . $w[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($o[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "O) " . $o[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($t[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "T) " . $t[$i], 0, 'L');
        $pdf->Ln(2);
    }
}
}
//no 3 swot c
$pdf->SetY(130);
for ($i = 2; $i < 3; $i++) {
    if($swotcname[$i]!="")
    {
    $pdf->SetFillColor(149, 175, 192); //grey
    $pdf->RoundedRect(12, 125, 85, 65, 5, 'F');
    $pdf->SetFont('arial', 'B', 14);
    $pdf->SetTextColor(0, 98, 102);
    $pdf->SetX(14);
    $pdf->Cell(70, 0, $swotcname[$i], 0, 0, 'C');
    $pdf->SetY(138);
    $pdf->SetFont('arial', '', 12);

    if ($s[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "S) " . $s[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($w[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "W) " . $w[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($o[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "O) " . $o[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($t[$i] != "") {
        $pdf->SetX(14);
        $pdf->MultiCell(85, 6,  "T) " . $t[$i], 0, 'L');
        $pdf->Ln(2);
    }
}
}
//no 4 swot c
$pdf->SetY(130);
for ($i = 3; $i < 4; $i++) {
    if($swotcname[$i]!="")
    {
    $pdf->SetFillColor(104, 109, 224); //grey
    $pdf->RoundedRect(107, 125, 85, 65, 5, 'F');
    $pdf->SetFont('arial', 'B', 14);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetX(110);
    $pdf->Cell(70, 0, $swotcname[$i], 0, 0, 'C');
    $pdf->SetY(138);
    $pdf->SetFont('arial', '', 12);

    if ($s[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "S) " . $s[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($w[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "W) " . $w[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($o[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "O) " . $o[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($t[$i] != "") {
        $pdf->SetX(110);
        $pdf->MultiCell(85, 6,  "T) " . $t[$i], 0, 'L');
        $pdf->Ln(2);
    }
}
}
//no 5 swot c
$pdf->SetY(210);
for ($i = 4; $i < 5; $i++) {
    if($swotcname[$i]!="")
    {
    $pdf->SetFillColor(246, 229, 141); //grey
    $pdf->RoundedRect(60, 205, 85, 65, 5, 'F');
    $pdf->SetFont('arial', 'B', 14);
    $pdf->SetTextColor(205, 97, 0);
    $pdf->SetX(66);
    $pdf->Cell(70, 0, $swotcname[$i], 0, 0, 'C');
    $pdf->SetY(213);
    $pdf->SetFont('arial', '', 12);

    if ($s[$i] != "") {
        $pdf->SetX(66);
        $pdf->MultiCell(85, 6,  "S) " . $s[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($w[$i] != "") {
        $pdf->SetX(66);
        $pdf->MultiCell(85, 6,  "W) " . $w[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($o[$i] != "") {
        $pdf->SetX(66);
        $pdf->MultiCell(85, 6,  "O) " . $o[$i], 0, 'L');
        $pdf->Ln(2);
    }
    if ($t[$i] != "") {
        $pdf->SetX(66);
        $pdf->MultiCell(85, 6,  "T) " . $t[$i], 0, 'L');
        $pdf->Ln(2);
    }
}
}



$csales = "SELECT * FROM sales where id='banuwo'";
$result3 = mysqli_query($conn, $csales);
$rows3 = mysqli_fetch_array($result3, MYSQLI_NUM);
$salesoln = json_decode($rows3[1], true);
$salesol = json_decode($rows3[2], true);
$salesol1 = json_decode($rows3[3], true);
$salesol2 = json_decode($rows3[4], true);

$ds1 = $rows3[5];
$ds2 = $rows3[6];
$ds3 = $rows3[7];
$chs1 = $rows3[8];
$chs2 = $rows3[9];
$chs3 = $rows3[10];

$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('helvetica', '', 35);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(190, 35, 'Sales and Marketing Statistic', 0, 0, 'C');
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', '', 16);

$pdf->SetXY(10, $valY);
//color
$col[0] = array(111, 30, 81);
$col[1] = array(255, 195, 18);
$col[2] = array(234, 32, 39);
$col[3] = array(87, 88, 187);
$col[4] = array(0, 148, 50);
$col[5] = array(238, 90, 36);
$col[6] =   array(0, 98, 102);
$col[7] =  array(237, 76, 103);
$col[8] =   array(196, 229, 56);
$col[9] =  array(18, 137, 167);
$col[10] = array(247, 159, 31);
$col[11] = array(253, 167, 223);
$data1 = array();


for ($i = 0; $i < 15; $i++) {
    if ($salesol[$i] != "" || $salesol1[$i] != "" || $salesol2[$i] != "") {

        $onlinesales[$i] = $salesol[$i] + $salesol1[$i] + $salesol2[$i];
        $data1[$salesoln[$i]] = $onlinesales[$i];
    }
}
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', '', 16);
$pdf->Cell(0, 90, 'Marketing Media', 0, 0, 'C');
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetX(140);
$pdf->SetY(55);
$pdf->PieChart(160, 160, $data1, '%l (%p)', $col);
//Pie chart

$pdf->SetX(0);
$pdf->SetY(115);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('arial', '', 16);
$pdf->Cell(0, 90, 'Sales Statistic', 0, 0, 'C');
$datasale["Direct Sales"]=($ds1+$ds2+$ds3)/3;
$datasale["Channel Sales"]=($chs1+$chs2+$chs3)/3;
$pdf->SetX(150);
$pdf->SetY(160);
 $pdf->PieChart(150, 150, $datasale, '%l (%p)', $col);


$cfinancial = "SELECT * FROM financial where id='banuwo'";
$result4 = mysqli_query($conn, $cfinancial);
$rows4 = mysqli_fetch_array($result4, MYSQLI_NUM);
$id = $rows4[0];
$iname = json_decode($rows4[1]);
$iy1 = json_decode($rows4[2]);
$iy2 =  json_decode($rows4[3]);
$iy3 = json_decode($rows4[4]);
$sval1 = json_decode($rows4[5]);
$sval2 = json_decode($rows4[6]);
$sval3 = json_decode($rows4[7]);
$ename = json_decode($rows4[8]);
$ey1 = json_decode($rows4[9]);
$ey2 = json_decode($rows4[10]);
$ey3 = json_decode($rows4[11]);
$mval1 = json_decode($rows4[12]);
$mval2 = json_decode($rows4[13]);
$mval3 = json_decode($rows4[14]);
$aname = json_decode($rows4[15]);
$ay1 = json_decode($rows4[16]);
$ay2 = json_decode($rows4[17]);
$ay3 = json_decode($rows4[18]);
$lname = json_decode($rows4[19]);
$ly1 = json_decode($rows4[20]);
$ly2 = json_decode($rows4[21]);
$ly3 = json_decode($rows4[22]);

// $pdf->AddPage();
// $pdf->SetLineWidth(1.5);
// $pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
// $pdf->Rect(5, 5, 200, 287, 'D');
// $pdf->SetFont('arial', '', 25);
// $pdf->SetTextColor(116, 185, 255);
// $pdf->Cell(190, 25, 'Profit and Loss', 0, 0, 'C');

// $pdf->SetY(40);
// $pdf->SetFont('arial', '', 14);
// $pdf->SetTextColor(0);
// $pdf->Cell(190, 0, 'Year ' . Date('Y'), 0, 0, 'C');
// $pdf->SetY(50);
// $heading = array('Dr','','','Cr');
// $inc=0;
// $exp=0;
// $k=0;
// for($j=0;$j<=count($ename);$j++){
//     $val[$j] = array($ename[$j], $ey1[$j], $iname[$j], $iy1[$j]);
//     $inc+=$iy1[$j];
//     $exp+=$ey1[$j];
//     $k=$j;
// }

// $total=$inc-$exp;
// if($total>0)
// {
//     $val[$k++] = array("Adjusting Acc", $total,'' ,'');
//     $val[$k++] = array('Total',$inc,"Total", $inc);
// }elseif ($total<0) {
//     $val[$k++] = array('','',"Adjusting Acc", $total*-1);
//     $val[$k++] = array('Total',$exp,"Total", $exp);
// }else{
//     $val[$k++] = array('Total',$exp,"Total", $inc);
// }

// $pdf->balancesheet($heading, $val);
// $pdf->SetY(220);
// if ($inc == $exp) {
//     $pdf->SetTextColor(39, 174, 96);
//     $pdf->Cell(190, 0, 'Company is Balanced', 0, 0, 'C');
// } else if ($inc > $exp) {
//     $pdf->SetTextColor(192, 57, 43);
//     $pdf->Cell(190, 0, 'Company in Profit', 0, 0, 'C');
// }
// else if ($inc < $exp) {
//     $pdf->SetTextColor(192, 57, 43);
//     $pdf->Cell(190, 0, 'Company in Loss', 0, 0, 'C');
// }

//First Year
$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$w = array(58, 42,42,42);

$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('arial', '', 25);
$pdf->SetTextColor(116, 185, 255);
$pdf->Cell(190, 25, 'Profit and Loss', 0, 0, 'C');
//table profit and loss

$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetY(30);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(0);

$pdf->SetX(50);
$pdf->SetFillColor(195, 230, 203);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('', 'B',10);
// Header

$pdf->SetY(30);
$pdf->SetFont('', 'B',11);
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();
    $pdf->Cell(58, 11, "Particular", 'LR', 0, 'L', true);
    $pdf->Cell(42, 11, "First Year", 'LR', 0, 'L', true);
    $pdf->Cell(42, 11, "Second Year", 'LR', 0, 'L', true);
    $pdf->Cell(42, 11, "Third Year", 'LR', 0, 'L', true);
    $fill = false;
    $pdf->Ln();
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->SetTextColor(255, 05, 10);
    $pdf->Cell(58, 10,'Income', 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->SetTextColor(0);
    $pdf->SetFont('', '',10);
if($mval1>=0)
{
    $pdf->Ln();
   
    $pdf->Cell(58, 10,"Net Sales", 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($mval1), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($mval2), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($mval3), 'LR', 0, 'L', $fill);
}

$j=0;
$k=0;
$inc1=0;
$exp1=0;
$inc2=0;
$exp2=0;
$inc3=0;
$exp3=0;
for($m=0;$m<count($iname);$m++){
    $pdf->Ln();
    
    $pdf->Cell(58, 10,$iname[$m], 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($iy1[$m]), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($iy2[$m]), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($iy3[$m]), 'LR', 0, 'L', $fill);
    $inc1+=$iy1[$m];
    $inc2+=$iy2[$m];
    $inc3+=$iy3[$m];
    $k=$j;
}
$pdf->Ln();
if($mval1>=0)
{
    $inc1+=$mval1;
    $inc2+=$mval2;
    $inc3+=$mval3;
}



$pdf->SetFont('', 'B',11);

$pdf->Cell(58, 10,'Total Income (A)', 'LR', 0, 'L', false);
    $pdf->Cell(42, 10,'Rs. '.number_format($inc1), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($inc2), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($inc3), 'LR', 0, 'L', $fill);
    $pdf->Ln();
    $pdf->SetTextColor(255, 05, 10);
    $pdf->Cell(58, 10,'Expense', 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->SetTextColor(0);
    $pdf->SetFont('', '',10);
    $i=0;
while($i<count($sval1))
{
    $me1+=$sval1[$i];
    $me2+=$sval2[$i];
    $me3+=$sval3[$i];
    $i++;
}
    if($me1>=0){
        $pdf->Ln();
   
        $pdf->Cell(58, 10,"Marketing Expense", 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($me1), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($me2), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($me3), 'LR', 0, 'L', $fill);
    }

    for($n=0;$n<count($ename);$n++){
        if($ename[$n]!="Depreciation" && $ename[$n]!="Intrest" && $ename[$n]!="Tax")
        {
            $pdf->Ln();
           
            $pdf->Cell(58, 10,$ename[$n], 'LR', 0, 'L', $fill);
            $pdf->Cell(42, 10,'Rs. '.number_format($ey1[$n]), 'LR', 0, 'L', $fill);
            $pdf->Cell(42, 10,'Rs. '.number_format($ey2[$n]), 'LR', 0, 'L', $fill);
            $pdf->Cell(42, 10,'Rs. '.number_format($ey3[$n]), 'LR', 0, 'L', $fill);
            $exp1+=$ey1[$n];
            $exp2+=$ey2[$n];
            $exp3+=$ey3[$n];
            
        }
         
    }
    $pdf->Ln();
    if($me1>=0)
{
            $exp1+=$me1;
            $exp2+=$me2;
            $exp3+=$me3;

}
    

$pdf->SetFont('', 'B',11);
$pdf->Cell(58, 10,'Total Expense (B)', 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($exp1), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($exp2), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format($exp3), 'LR', 0, 'L', $fill);
    $tot1=$inc1-$exp1;
    $tot2=$inc2-$exp2;
    $tot3=$inc3-$exp3;
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Total (A-B)', 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot1), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot2), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot3), 'LR', 0, 'L', $fill);

        $depkey = array_search('Depreciation', $ename);
        if($depkey>0){
        $depval1=$ey1[$depkey];
        $depval2=$ey2[$depkey];
        $depval3=$ey3[$depkey];
        }else{
        $depval1=0;
        $depval2=0;
        $depval3=0;
        }
        $pdf->Ln();
        $pdf->SetFont('', '',10);
        $pdf->Cell(58, 10,'Less : Depreciation', 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($depval1), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($depval2), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($depval3), 'LR', 0, 'L', $fill);
        $pdf->Ln();
        $pdf->SetFont('', 'B',11);
        $pdf->Cell(58, 10,'Profit Before Interest & Tax', 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot1-$depval1), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot2-$depval2), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot3-$depval3), 'LR', 0, 'L', $fill);
        $intkey = array_search('Intrest', $ename);
        if($intkey>0){
            $intval1=$ey1[$intkey];
            $intval2=$ey2[$intkey];
            $intval3=$ey3[$intkey];
        }
         else{
        $intval1=0;
        $intval2=0;
        $intval3=0;
         }
        $pdf->Ln();
        $pdf->SetFont('', '',10);
        $pdf->Cell(58, 10,'Less : Interest', 'LR', 0, 'L', $fill);
                $pdf->Cell(42, 10,'Rs. '.number_format($intval1), 'LR', 0, 'L', $fill);
                $pdf->Cell(42, 10,'Rs. '.number_format($intval2), 'LR', 0, 'L', $fill);
                $pdf->Cell(42, 10,'Rs. '.number_format($intval3), 'LR', 0, 'L', $fill);
        $pdf->Ln();
        $pdf->SetFont('', 'B',11);
        $pdf->Cell(58, 10,'Profit Before Tax', 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot1-$depval1-$intval1), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot2-$depval2-$intval2), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot3-$depval3-$intval3), 'LR', 0, 'L', $fill);
        
        $tkey = array_search('Tax', $ename);
        if($tkey>0){
        $tval1=$ey1[$tkey];
        $tval2=$ey2[$tkey];
        $tval3=$ey3[$tkey];
    } else{
        $tval1=0;
        $tval2=0;
        $tval3=0;
    }
        
        $pdf->Ln();
        $pdf->SetFont('', '',10);
        $pdf->Cell(58, 10,'Less : Tax', 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tval1), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tval2), 'LR', 0, 'L', $fill);
        $pdf->Cell(42, 10,'Rs. '.number_format($tval3), 'LR', 0, 'L', $fill);
        $pdf->Ln();
        $pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();
        $pdf->SetFont('', 'B',11);
        $pdf->Cell(58, 10,'Profit After Tax ', 'LR', 0, 'L', true);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot1-$depval1-$intval1-$tval1), 'LR', 0, 'L', true);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot2-$depval2-$intval2-$tval2), 'LR', 0, 'L', true);
        $pdf->Cell(42, 10,'Rs. '.number_format($tot3-$depval3-$intval3-$tval3), 'LR', 0, 'L', true);
        
        $profit1=$tot1-$depval1-$intval1-$tval1;
        $profit2=$tot2-$depval2-$intval2-$tval2;
        $profit3=$tot3-$depval3-$intval3-$tval3;

    $pdf->Ln();
// Color and font restoration
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetFont('');
// Data
// Closing line
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();

//Balance Sheet first Year
$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetFillColor(195, 230, 203);
$w = array(58, 42,42,42);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('arial', '', 25);
$pdf->SetTextColor(116, 185, 255);
$pdf->Cell(190, 25, 'Balance Sheet', 0, 0, 'C');
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetY(30);
$pdf->SetFont('', 'B',11);
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();
$pdf->Cell(58, 11, "Particular", 'LR', 0, 'L', true);
$pdf->Cell(42, 11, "First Year", 'LR', 0, 'L', true);
$pdf->Cell(42, 11, "Second Year", 'LR', 0, 'L', true);
$pdf->Cell(42, 11, "Third Year", 'LR', 0, 'L', true);
$fill = false;
$pdf->Ln();
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();
$pdf->SetTextColor(255, 05, 10);
$pdf->Cell(58, 10,'Liability', 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
$pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
$pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
$pdf->SetTextColor(0);
$lial1=0;
$ass1=0;
$lial2=0;
$ass2=0;
$lial3=0;
$ass3=0;
$n=1;
$pdf->SetFont('', '',10);
$a=count($aname);
$l=count($lname);
$pdf->Ln();
$pdf->Cell(58, 10,$lname[0], 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly1[0]), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly2[0]), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly3[0]), 'LR', 0, 'L', $fill);
$pdf->Ln();
$pdf->Cell(58, 10,'Profit/Loss', 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($profit1), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($profit2), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($profit3), 'LR', 0, 'L', $fill);

for($m=2;$m<=$l;$m++){
    $pdf->Ln();
$pdf->Cell(58, 10,$lname[$m-1], 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly1[$m-1]), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly2[$m-1]), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly3[$m-1]), 'LR', 0, 'L', $fill);
    $lial1+=$ly1[$m-1];
    $lial2+=$ly2[$m-1];
    $lial3+=$ly3[$m-1];
    
}
$pdf->Ln();
$pdf->SetFont('', 'B',11);
$pdf->Cell(58, 10,'Total Liablities (A)', 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly1[0]+$lial1+$profit1), 'LR', 0, 'L', $fill);

$pdf->Cell(42, 10, 'Rs. '.number_format($ly2[0]+$lial2+$profit2), 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, 'Rs. '.number_format($ly3[0]+$lial3+$profit3), 'LR', 0, 'L', $fill);
$pdf->Ln();
$pdf->SetTextColor(255, 5, 10);
$pdf->Cell(58, 10,'Asset', 'LR', 0, 'L', $fill);
$pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
$pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
$pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
$pdf->SetTextColor(0);
$pdf->SetFont('', '',10);
for($m=0;$m<$a;$m++){
    $pdf->Ln();
    $pdf->Cell(58, 10,$aname[$m], 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, 'Rs. '.number_format($ay1[$m]), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, 'Rs. '.number_format($ay2[$m]), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, 'Rs. '.number_format($ay3[$m]), 'LR', 0, 'L', $fill);
       // $val2[$m] = array($lname[$m-1],$ly1[$m-1],$aname[$m], $ay1[$m]);
        $ass1+=$ay1[$m];
        $ass2+=$ay2[$m];
        $ass3+=$ay3[$m];
        
    }
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Total Asset (B)', 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, 'Rs. '.number_format($ass1), 'LR', 0, 'L', $fill); 
    $pdf->Cell(42, 10, 'Rs. '.number_format($ass2), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10, 'Rs. '.number_format($ass3), 'LR', 0, 'L', $fill);
    $pdf->Ln();
    $fill=true;
    
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->Cell(58, 10,'Diff (A)-(B)', 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format(($ly1[0]+$lial1+$profit1)-$ass1), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format( ($ly2[0]+$lial2+$profit2)-$ass2), 'LR', 0, 'L', $fill);
    $pdf->Cell(42, 10,'Rs. '.number_format( ( $ly3[0]+$lial3+$profit3)-$ass3), 'LR', 0, 'L', $fill);
    $pdf->Ln();
    // Color and font restoration
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('');
    // Data
    // Closing line
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();

    $lial1=$ly1[0]+$lial1+$profit1;
    $lial2=$ly2[0]+$lial2+$profit2;
    $lial3=$ly3[0]+$lial3+$profit3;
//Ratio
$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('arial', '', 35);
$pdf->SetTextColor(116, 185, 255);
$pdf->Cell(190, 25, 'Ratio Analysis', 0, 0, 'C');
$pdf->SetY(40);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(0);

$pdf->SetY(50);

$pdf->SetFillColor(195, 230, 203);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetLineWidth(0);
$pdf->SetFont('', 'B',10);
// Header
$w = array(48, 48,48,48);
$pdf->SetY(35);
$pdf->SetFont('', 'B',11);
$pdf->Cell(48, 11, "Ratio", 0, 0, 'L', true);
$pdf->Cell(48, 11, "Overall Industry Score", 0, 0, 'C', true);
$pdf->Cell(48, 11, "Your Company Score", 0, 0, 'C', true);
$pdf->Cell(48, 11, "Status", 0, 0, 'C', true);
$fill = false;
$pdf->Ln();
$pdf->SetFont('', '',10);
$pdf->Cell(48, 12,'Current Ratio', 'LR', 0, 'L', $fill);
$overallcr=2.5;
$pdf->Cell(48, 12, $overallcr, 'LR', 0, 'C', $fill);
$currentratio=(float)$ass3/$lial3;
$pdf->Cell(48, 12,number_format($currentratio, 2, '.', '') , 'LR', 0, 'C', $fill);
if($currentratio>=$overallcr)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($currentratio>=$overallcr/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($currentratio>=$overallcr/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$overallqr=1.5;
$quickratio=(float)(($ass3-$lial3)/$lial3);
$pdf->Ln();
$pdf->Cell(48, 12,'Quick Ratio', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallqr, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($quickratio, 2, '.', ''), 'LR', 0, 'C', $fill);
if($quickratio>=$overallqr)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($quickratio>=$overallqr/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($quickratio>=$overallqr/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$pdf->Ln();
$pdf->Cell(48, 12,'Debt Ratio', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, '2.5', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,'2.5', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12, '', 'LR', 0, 'C', $fill);

$pdf->Ln();
$pdf->Cell(48, 12,'Debt-To-Equity Ratio', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, '1.5', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,'1.0', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12, '', 'LR', 0, 'C', $fill);


$overallnpm=1.5;
$netprofitmargin=(float)($profit3/($ds3+$chs3));
$pdf->Ln();
$pdf->Cell(48, 12,'Net Profit Margin', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallnpm, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($netprofitmargin, 2, '.', ''), 'LR', 0, 'C', $fill);
if($netprofitmargin>=$overallnpm)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($netprofitmargin>=$overallnpm/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($netprofitmargin>=$overallnpm/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);

$pdf->Ln();
$pdf->Cell(48, 12,'Return on Equity', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, '2.2', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,'2.0', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12, '', 'LR', 0, 'C', $fill);


$overallgpm=1.2;
$grossprofitmargin=(float)((($ds3+$chs3)-$lial3)/($ds3+$chs3));
$pdf->Ln();
$pdf->Cell(48, 12,'Gross Profit Margin', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12,$overallgpm , 'LR', 0, 'C', $fill); 
$pdf->Cell(48, 12,number_format($grossprofitmargin, 2, '.', ''), 'LR', 0, 'C', $fill);
if($grossprofitmargin>=$overallgpm)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($grossprofitmargin>=$overallgpm/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($grossprofitmargin>=$overallgpm/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$overallrota=1.2;
$returnonasset=(float)($profit3/$ass3);
$pdf->Ln();
$pdf->Cell(48, 12,'Return on Total Asset', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12,$overallrota, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($returnonasset, 2, '.', ''), 'LR', 0, 'C', $fill);
if($returnonasset>=$overallrota)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($returnonasset>=$overallrota/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($returnonasset>=$overallrota/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$overallstwc=1.2;
$salestowc=(float)(($ds3+$chs3)/$ly3[array_search('Owner Contribution', $lname)]);
$pdf->Ln();
$pdf->Cell(48, 12,'Sales To Working Capital', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallstwc, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($salestowc, 2, '.', ''), 'LR', 0, 'C', $fill);
if($salestowc>=$overallstwc)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($salestowc>=$overallstwc/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($salestowc>=$overallstwc/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$overallpbt=2.1;
$profitbeforetax=(float)(($tot3-$depval3)/$ass3);
$pdf->Ln();
$pdf->Cell(48, 12,'PBT / Total Asset Ratio', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12,$overallpbt, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($profitbeforetax, 2, '.', ''), 'LR', 0, 'C', $fill);
if($profitbeforetax>=$overallpbt)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($profitbeforetax>=$overallpbt/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($profitbeforetax>=$overallpbt/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);

$nfa=0;
$fa1=array_search('Gross Fixed Assets', $aname);
if($fa1>0)
$nfa+=$ay3[$fa1];
else
$nfa+=0;

$fa2=array_search('Stock in Trade', $aname);
if($fa2>0)
$nfa+=$ay3[$fa2];
else
$nfa+=0;

$overallstfa=2.1;
$salestonetfa=(float)(($ds3+$chs3)/($nfa));

$pdf->Ln();
$pdf->Cell(48, 12,'Sales to Net Fixed Asset', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallstfa, 'LR', 0, 'C', $fill);
if($salestonetfa>0 && $salestonetfa<=0)
{
$pdf->Cell(48, 12,number_format($salestonetfa, 2, '.', ''), 'LR', 0, 'C', $fill);

if($salestonetfa>=$overallstfa)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($salestonetfa>=$overallstfa/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($salestonetfa>=$overallstfa/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
}
else{
    $pdf->Cell(48, 12,'NAN', 'LR', 0, 'C', $fill);
    $pdf->Cell(48, 12,'---', 'LR', 0, 'C', $fill);
}


$overallstta=3.1;
$salestonetta=(float)(($ds3+$chs3)/($ass3));
$pdf->Ln();
$pdf->Cell(48, 12,'Sales to Total Asset ', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallstta, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($salestonetta, 2, '.', ''), 'LR', 0, 'C', $fill);
if($salestonetta>=$overallstta)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($salestonetta>=$overallstta/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($salestonetta>=$overallstta/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$overalldtsr=2.2;
$dfa=0;
$dep1=array_search('Depreciation', $ename);
if($dep1>0)
$dfa+=$ey3[$dep1];
else
$dfa+=0;

$depstosales=(float)(($dfa)/($ds3+$chs3));
$pdf->Ln();
$pdf->Cell(48, 12,'Depreciation to Sales Ratio', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overalldtsr, 'LR', 0, 'C', $fill);
if($dfa>0)
{
$pdf->Cell(48, 12,number_format($depstosales, 2, '.', ''), 'LR', 0, 'C', $fill);

if($depstosales>=$overalldtsr)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($depstosales>=$overalldtsr/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($depstosales>=$overalldtsr/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
}
else{
    $pdf->Cell(48, 12,'NAN', 'LR', 0, 'C', $fill);
    $pdf->Cell(48, 12,'---', 'LR', 0, 'C', $fill);
}



$overallebitir=1.2;
$ebitir=(float)(($tot3-$depval3)/($intval3));
$pdf->Ln();
$pdf->Cell(48, 12,'EBIT Intrest Ratio', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallebitir, 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($ebitir, 2, '.', ''), 'LR', 0, 'C', $fill);
if($ebitir>=$overallebitir)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($ebitir>=$overallebitir/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($ebitir>=$overallebitir/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$overallebita=2.2;
$ebitamargin=(float)(($tot3-$depval3)/($ds3+$chs3));
$pdf->Ln();
$pdf->Cell(48, 12,'EBITA Margin', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12, $overallebita*100 .'%', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($ebitamargin*100, 2, '.', '').'%', 'LR', 0, 'C', $fill);
if($ebitamargin>=$overallebita)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($ebitamargin>=$overallebita/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($ebitamargin>=$overallebita/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);



$EBITADA=(float)((($ds3+$chs3)-$exp3));
$pdf->Ln();
$pdf->Cell(48, 12,'EBITADA', 'LR', 0, 'L', $fill);
$pdf->Cell(48, 12,'---', 'LR', 0, 'C', $fill);
$pdf->Cell(48, 12,number_format($EBITADA, 2, '.', ''), 'LR', 0, 'C', $fill);
if($EBITADA>=$exp3)
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);
else if($EBITADA>=$exp3/2)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else if($EBITADA>=$exp3/3)
$pdf->Cell(48, 13,'', 'LR', 0, 'C', $fill);
else
$pdf->Cell(48, 12,'', 'LR', 0, 'C', $fill);


$pdf->Ln();
$pdf->SetFont('', 'B',11);
$pdf->Cell(48, 10, "Total Average Ratio", 0, 0, 'L', true);
$pdf->Cell(48, 10, "2.3", 0, 0, 'C', true);
$pdf->Cell(48, 10, "2.1", 0, 0, 'C', true);
$pdf->Cell(48, 10, "GOOD", 0, 0, 'C', true);



$pdf->Ln();
// Color and font restoration
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetFont('');
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();

//Cash Flow

$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$w = array(58, 42,42,42);

$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('arial', '', 25);
$pdf->SetTextColor(116, 185, 255);
$pdf->Cell(190, 25, 'Cash Flow Analysis', 0, 0, 'C');

$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetY(30);
$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(0);

$pdf->SetX(50);
$pdf->SetFillColor(195, 230, 203);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('', 'B',10);
// Header

$pdf->SetY(50);
$pdf->SetFont('', 'B',11);
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln();
    $pdf->Cell(58, 11, "Particular", 'LR', 0, 'L', true);
    $pdf->Cell(42, 11, "First Year", 'LR', 0, 'L', true);
    $pdf->Cell(42, 11, "Second Year", 'LR', 0, 'L', true);
    $pdf->Cell(42, 11, "Third Year", 'LR', 0, 'L', true);
    $fill = false;
    $pdf->Ln();
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    
$pdf->SetTextColor(255,5,10);
    $pdf->Cell(58, 10,'Operating Cash Flow', 'LR', 0, 'L', $fill);
    $pdf->SetTextColor(0);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10,'', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10,'', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10,'', 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Net Earnings', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10, number_format($profit1, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($profit2, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($profit3, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Plus:Depreciation', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10, number_format($depval1, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($depval2, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($depval3, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Less:Working Capital', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10, number_format($ass1-$lial1, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($ass2-$lial2, 2,'.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($ass3-$lial3, 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Cash From Operation', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10, number_format($profit1+$depval1-($ass1-$lial1), 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($profit2+$depval2-($ass2-$lial2), 2,'.', ''), 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, number_format($profit3+$depval3-($ass3-$lial3), 2, '.', ''), 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10,'' , 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10,'', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->SetTextColor(255,5,10);
    $pdf->Cell(58, 10,'Investing Cash Flow', 'LR', 0, 'L', $fill);
    $pdf->SetTextColor(0);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10,'' , 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10,'', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    
    $searchword = 'Investment';
$matches = array();
$ret = array_keys(array_filter($ename, function($var) use ($searchword){
    return strpos($var, $searchword) !== false;
}));
$mat=count($ret);
$i1=0;
$i2=0;
$i3=0;
$i=0;
    while($i<$mat)
    {
            $invtname=$ename[$ret[$i]];
            $invt1=$ey1[$ret[$i]];
            $i1+=$ey1[$ret[$i]];
            $invt2=$ey2[$ret[$i]];
            $i2+=$ey2[$ret[$i]];
            $invt3=$ey3[$ret[$i]];
            $i3+=$ey3[$ret[$i]];
            $pdf->Ln();
            $pdf->SetFont('', 'B',11);
            $pdf->Cell(58, 10,$invtname, 'LR', 0, 'L', $fill);
            $pdf->SetFont('', '',10);
            $pdf->Cell(42, 10,number_format($invt1 , 2, '.', ''), 'LR', 0, 'R', $fill);
            $pdf->Cell(42, 10,number_format($invt2, 2, '.', ''), 'LR', 0, 'R', $fill);
            $pdf->Cell(42, 10,number_format($invt3, 2, '.', ''), 'LR', 0, 'R', $fill);
        $i++;
    
    }
    $pdf->Ln();
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'Cash To Investment', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10, '('.number_format($i1, 2, '.', '').')', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '('.number_format($i2, 2,'.', '').')', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '('.number_format($i3, 2, '.', '').')', 'LR', 0, 'R', $fill);
    $pdf->Ln();
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln();
    $pdf->SetFont('', 'B',11);
    $pdf->Cell(58, 10,'', 'LR', 0, 'L', $fill);
    $pdf->SetFont('', '',10);
    $pdf->Cell(42, 10,'' , 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10,'', 'LR', 0, 'R', $fill);
    $pdf->Cell(42, 10, '', 'LR', 0, 'R', $fill);
    $pdf->Ln();












//Mile Stone
$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('arial', '', 25);
$pdf->SetTextColor(116, 185, 255);
$pdf->Cell(190, 35, 'Milestones', 0, 0, 'C');
$pdf->Ln(18);

$pdf->SetFont('arial', '', 14);
$pdf->SetTextColor(0, 0, 0);
for ($j = 0; $j < 1; $j++) {
    if ($edate[$j] != "") {
        $pdf->SetY(50);
        $pdf->SetFillColor(255, 204, 204); //blue
        $pdf->RoundedRect(12, 45, 75, 60, 5, 'F');
        //$pdf->Image('../../logo/milestone.png', 40, 78, 25, 25);

        $pdf->SetTextColor(255, 56, 56);
        $pdf->SetFont('arial', '', 14);
        $pdf->SetX(35);
        $pdf->MultiCell(0, 8, $edate[$j], 0, 'J');
        $pdf->SetX(20);
        $pdf->SetFont('arial', '', 14);
        $pdf->MultiCell(0, 8, $mname[$j], 0, 'J');
        $pdf->Ln(10);
    }
}
for ($j = 1; $j < 2; $j++) {
    if ($edate[$j] != "") {
        $pdf->SetY(50);
        $pdf->SetFillColor(250, 211, 144); //blue
        $pdf->RoundedRect(117, 45, 75, 60, 5, 'F');
        //$pdf->Image('../../logo/milestone.png', 145, 78, 25, 25);

        $pdf->SetTextColor(238, 90, 36);
        $pdf->SetFont('arial', '', 14);
        $pdf->SetX(140);
        $pdf->MultiCell(0, 8, $edate[$j], 0, 'J');
        $pdf->SetX(120);
        $pdf->SetFont('arial', '', 14);
        $pdf->MultiCell(0, 8, $mname[$j], 0, 'J');
        $pdf->Ln(10);
    }
}
for ($j = 2; $j < 3; $j++) {
    if ($edate[$j] != "") {
        $pdf->SetY(120);
        $pdf->SetFillColor(126, 214, 223); //blue
        $pdf->RoundedRect(65, 112, 75, 60, 5, 'F');
        //$pdf->Image('../../logo/milestone.png', 90, 143, 25, 25);
        $pdf->SetTextColor(27, 20, 100);
        $pdf->SetFont('arial', '', 14);
        $pdf->SetX(90);
        $pdf->MultiCell(0, 8, $edate[$j], 0, 'J');
        $pdf->SetX(70);
        $pdf->SetFont('arial', '', 14);
        $pdf->MultiCell(0, 8, $mname[$j], 0, 'J');
        $pdf->Ln(10);
    }
}
for ($j = 3; $j < 4; $j++) {
    if ($edate[$j] != "") {
        $pdf->SetY(190);
        $pdf->SetFillColor(149, 175, 192); //blue
        $pdf->RoundedRect(12, 180, 75, 60, 5, 'F');
        //$pdf->Image('../../logo/milestone.png', 40, 212, 25, 25);
        $pdf->SetTextColor(27, 20, 100);
        $pdf->SetFont('arial', '', 14);
        $pdf->SetX(37);
        $pdf->MultiCell(0, 8, $edate[$j], 0, 'J');
        $pdf->SetX(20);
        $pdf->SetFont('arial', '', 14);
        $pdf->MultiCell(0, 8, $mname[$j], 0, 'J');
        $pdf->Ln(10);
    }
}
for ($j = 4; $j < 5; $j++) {
    if ($edate[$j] != "") {
        $pdf->SetY(190);
        $pdf->SetFillColor(214, 162, 232); //blue
        $pdf->RoundedRect(117, 180, 75, 60, 5, 'F');
        //$pdf->Image('../../logo/milestone.png', 145, 212, 25, 25);
        $pdf->SetTextColor(179, 57, 57);
        $pdf->SetFont('arial', '', 14);
        $pdf->SetX(140);
        $pdf->MultiCell(0, 8, $edate[$j], 0, 'J');
        $pdf->SetX(125);
        $pdf->SetFont('arial', '', 14);
        $pdf->MultiCell(0, 8, $mname[$j], 0, 'J');
        $pdf->Ln(10);
    }
}

//Acheivement

$pdf->AddPage();
//$pdf->Image('../../logo/achieve.png', 10, 35, 190, 230);

$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('arial', '', 25);
$pdf->SetTextColor(116, 185, 255);
$pdf->Cell(190, 35, 'Our Core Teams', 0, 0, 'C');
$pdf->Ln(18);
$pdf->SetFont('arial', '', 11);
$pdf->SetTextColor(255, 255, 255);

for ($i = 0; $i < 1; $i++) {
    if ($empname[$i] != "") {
        $pdf->SetY(53);
        $pdf->SetX(82);
        $pdf->MultiCell(70, 6, $empname[$i]);
       
        $pdf->SetX(82);
        $pdf->MultiCell(45, 6, $design[$i]);
    
        $pdf->SetX(82);
        $pdf->MultiCell(70, 6, "Exp : " . $exp[$i] . " Years");
        
        $pdf->SetX(82);
        $pdf->MultiCell(45, 6, $about[$i]);
    }else{
        $pdf->SetY(53);
        $pdf->SetX(82);
        $pdf->MultiCell(45, 6,"Not Decided Yet the Core Team ");
    }
}

for ($i = 1; $i < 2; $i++) {
    if ($empname[$i] != "") {
        $pdf->SetY(113);
        $pdf->SetX(12);
        $pdf->MultiCell(70, 6, $empname[$i]);
  
        $pdf->SetX(12);
        $pdf->MultiCell(45, 6, $design[$i]);
       
        $pdf->SetX(12);
        $pdf->MultiCell(70, 6, "Exp : " . $exp[$i] . " Years");
       
        $pdf->SetX(12);
        $pdf->MultiCell(45, 6, $about[$i]);
    }else{
        $pdf->SetY(113);
        $pdf->SetX(12);
        $pdf->MultiCell(45, 6,"Not Decided Yet the Core Team ");
    }
}

for ($i = 2; $i < 3; $i++) {
    if ($empname[$i] != "") {
        $pdf->SetY(113);
        $pdf->SetX(152);
        $pdf->MultiCell(70, 6, $empname[$i]);
       
        $pdf->SetX(152);
        $pdf->MultiCell(45, 6, $design[$i]);
       
        $pdf->SetX(152);
        $pdf->MultiCell(70, 6, "Exp : " . $exp[$i] . " Years");
      
        $pdf->SetX(152);
        $pdf->MultiCell(45, 6, $about[$i]);
    }else{
        $pdf->SetY(113);
        $pdf->SetX(152);
        $pdf->MultiCell(45, 6,"Not Decided Yet the Core Team ");
    }
}

for ($i = 3; $i < 4; $i++) {
    if ($empname[$i] != "") {
        $pdf->SetY(207);
        $pdf->SetX(39);
        $pdf->MultiCell(70, 6, $empname[$i]);
        
        $pdf->SetX(39);
        $pdf->MultiCell(45, 6, $design[$i]);
        
        $pdf->SetX(39);
        $pdf->MultiCell(70, 6, "Exp : " . $exp[$i] . " Years");
        
        $pdf->SetX(39);
        $pdf->MultiCell(45, 6, $about[$i]);
    }else{
        $pdf->SetY(207);
        $pdf->SetX(39);
        $pdf->MultiCell(45, 6,"Not Decided Yet the Core Team ");
    }
}
for ($i = 4; $i < 5; $i++) {
    if ($empname[$i] != "") {
        $pdf->SetY(207);
        $pdf->SetX(125);
        $pdf->MultiCell(70, 6, $empname[$i]);
        
        $pdf->SetX(125);
        $pdf->MultiCell(45, 6, $design[$i]);
       
        $pdf->SetX(125);
        $pdf->MultiCell(70, 6, "Exp : " . $exp[$i] . " Years");
       
        $pdf->SetX(125);
        $pdf->MultiCell(45, 6, $about[$i]);
    }else{
        $pdf->SetY(207);
        $pdf->SetX(125);
        $pdf->MultiCell(45, 6,"Not Decided Yet the Core Team ");
    }
}



$pdf->AddPage();
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor(116, 185, 255, 1.0, 0, 0, 0);
$pdf->Rect(5, 5, 200, 287, 'D');
$pdf->SetFont('', 'B',36);
$pdf->SetXY(20,100);
$pdf->SetTextColor(50, 65, 203);
$pdf->Cell(170,10,'THANK YOU !!!',0,1,'C');
$pdf->Output();
?>