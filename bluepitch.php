<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
include('database.php');
require('fpdf\fpdf.php');
class PDF extends FPDF
{
    function data($value)
    {

        // Times 12
        $this->SetFont('Times', 'B', 14);
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(243, 156, 18);
        // Mention in italics
        $this->Cell(0, 5, "$value", 0, 1, 'L', true);
        // $this->Ln(4);
    }
    function databig($value)
    {
        // Times 12
        $this->SetFont('Times', 'B', 34);
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(235, 47, 6);
        // Mention in italics
        $this->Cell(0, 5, "$value", 0, 1, 'L', true);
        // $this->Ln(4);
    }


    function Sector($xc, $yc, $r, $a, $b, $style = 'FD', $cw = true, $o = 90)
    {
        $d0 = $a - $b;
        if ($cw) {
            $d = $b;
            $b = $o - $a;
            $a = $o - $d;
        } else {
            $b += $o;
            $a += $o;
        }
        while ($a < 0)
            $a += 360;
        while ($a > 360)
            $a -= 360;
        while ($b < 0)
            $b += 360;
        while ($b > 360)
            $b -= 360;
        if ($a > $b)
            $b += 360;
        $b = $b / 360 * 2 * M_PI;
        $a = $a / 360 * 2 * M_PI;
        $d = $b - $a;
        if ($d == 0 && $d0 != 0)
            $d = 2 * M_PI;
        $k = $this->k;
        $hp = $this->h;
        if (sin($d / 2))
            $MyArc = 4 / 3 * (1 - cos($d / 2)) / sin($d / 2) * $r;
        else
            $MyArc = 0;
        //first put the center
        $this->_out(sprintf('%.2F %.2F m', ($xc) * $k, ($hp - $yc) * $k));
        //put the first point
        $this->_out(sprintf('%.2F %.2F l', ($xc + $r * cos($a)) * $k, (($hp - ($yc - $r * sin($a))) * $k)));
        //draw the arc
        if ($d < M_PI / 2) {
            $this->_Arc(
                $xc + $r * cos($a) + $MyArc * cos(M_PI / 2 + $a),
                $yc - $r * sin($a) - $MyArc * sin(M_PI / 2 + $a),
                $xc + $r * cos($b) + $MyArc * cos($b - M_PI / 2),
                $yc - $r * sin($b) - $MyArc * sin($b - M_PI / 2),
                $xc + $r * cos($b),
                $yc - $r * sin($b)
            );
        } else {
            $b = $a + $d / 4;
            $MyArc = 4 / 3 * (1 - cos($d / 8)) / sin($d / 8) * $r;
            $this->_Arc(
                $xc + $r * cos($a) + $MyArc * cos(M_PI / 2 + $a),
                $yc - $r * sin($a) - $MyArc * sin(M_PI / 2 + $a),
                $xc + $r * cos($b) + $MyArc * cos($b - M_PI / 2),
                $yc - $r * sin($b) - $MyArc * sin($b - M_PI / 2),
                $xc + $r * cos($b),
                $yc - $r * sin($b)
            );
            $a = $b;
            $b = $a + $d / 4;
            $this->_Arc(
                $xc + $r * cos($a) + $MyArc * cos(M_PI / 2 + $a),
                $yc - $r * sin($a) - $MyArc * sin(M_PI / 2 + $a),
                $xc + $r * cos($b) + $MyArc * cos($b - M_PI / 2),
                $yc - $r * sin($b) - $MyArc * sin($b - M_PI / 2),
                $xc + $r * cos($b),
                $yc - $r * sin($b)
            );
            $a = $b;
            $b = $a + $d / 4;
            $this->_Arc(
                $xc + $r * cos($a) + $MyArc * cos(M_PI / 2 + $a),
                $yc - $r * sin($a) - $MyArc * sin(M_PI / 2 + $a),
                $xc + $r * cos($b) + $MyArc * cos($b - M_PI / 2),
                $yc - $r * sin($b) - $MyArc * sin($b - M_PI / 2),
                $xc + $r * cos($b),
                $yc - $r * sin($b)
            );
            $a = $b;
            $b = $a + $d / 4;
            $this->_Arc(
                $xc + $r * cos($a) + $MyArc * cos(M_PI / 2 + $a),
                $yc - $r * sin($a) - $MyArc * sin(M_PI / 2 + $a),
                $xc + $r * cos($b) + $MyArc * cos($b - M_PI / 2),
                $yc - $r * sin($b) - $MyArc * sin($b - M_PI / 2),
                $xc + $r * cos($b),
                $yc - $r * sin($b)
            );
        }
        //terminate drawing
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'b';
        else
            $op = 's';
        $this->_out($op);
    }
    function BarDiagram($w, $h, $data, $format, $color, $maxVal = 0, $nbDiv = 4)
    {
        $this->SetFont('Arial', '', 10);
        $this->SetLegends($data, $format);

        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $YDiag = $YPage + $margin;
        $hDiag = floor($h - $margin * 2);
        $XDiag = $XPage + $margin * 2 + $this->wLegend;
        $lDiag = floor($w - $margin * 3 - $this->wLegend);
        if ($color == null)
            $color = array(45, 43, 35);
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

        $this->SetFont('Arial', '', 10);

        $i = 0;
        foreach ($data as $val) {
            //Bar
            $xval = $XDiag;
            $lval = (int) ($val * $unit);
            $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
            $hval = $eBaton;
            $this->Rect($xval, $yval, $lval, $hval, 'DF');
            //Legend
            $this->SetXY(0, $yval);
            $this->Cell($xval - $margin, $hval, $this->legends[$i], 0, 0, 'R');
            $i++;
            $this->SetFillColor($color[3][$i], $color[3][$i], $color[2][$i]);
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

    function SetLegends($data, $format)
    {
        $this->legends = array();
        $this->wLegend = 0;
        $this->sum = array_sum($data);
        $this->NbVal = count($data);
        foreach ($data as $l => $val) {
            $p = sprintf('%.2f', $val / $this->sum * 100) . '%';
            $legend = str_replace(array('%l', '%v', '%p'), array($l, $val, $p), $format);
            $this->legends[] = $legend;
            $this->wLegend = max($this->GetStringWidth($legend), $this->wLegend);
        }
    }
   
}

$pdf = new PDF();
$pdf->AddFont('calibri-bold', '', 'calibri-bold.php');
$pdf->AddFont('Calibri', '', 'calibri.php');

$pdf->AddPage();
$pdf->Image('../../logo/coverpitch.png', 0, 0, 210, 60);
$pdf->SetFont('calibri-bold', '', 35);
$pdf->SetTextColor(136, 133, 238);
$pdf->Cell(190, 0, 'Sarestates Private Ltd.', 0, 0, 'C');
$pdf->SetTextColor(0, 0, 0);

$pdf->Image('../../logo/pitchimg.png', 0, 62, 110, 80);
$pdf->SetY(5);
$pdf->SetX(2);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Calibri', '', 10);
$pdf->Image('sicon/LogoTM.png', 10, 65, 25, 15);
$pdf->SetXY(2,90);
$pdf->MultiCell(50, 5, "Product-Ready". " Stage");
$pdf->Ln(2);
$pdf->SetX(2);
$pdf->MultiCell(50, 5, "Manufacturing"." Company");
$pdf->Ln(2);
$pdf->SetX(2);
$pdf->MultiCell(50, 5, "Industry "."Pharmaceutical");
$pdf->Ln(2);
$pdf->SetX(2);
$pdf->MultiCell(47, 5, "CEO " . "Venkatraman Subramany");
$pdf->Ln(2);
$pdf->SetX(2);
$pdf->MultiCell(47, 5, "Est Date: " . "2012");
$pdf->SetTextColor(29, 36, 93);
$pdf->SetXY(53,65);
$pdf->SetFont('calibri', '', 11);
$pdf->MultiCell(48, 5, "The company profile usually includes the products & services
provided by the company, current position, short term and long
term goals.The company profile usually includes the products &
services provided by the company, The company profile usually
includ", 0, 'J');

$pdf->Image('../../logo/pitchimgflag.png', 111, 62, 98, 53);
$pdf->SetY(65);
$pdf->SetX(135);
$pdf->SetFont('calibri-bold', '', 14);
$pdf->MultiCell(50, 5, "Milestone",0,'C');
$pdf->SetFont('calibri', '', 11);
$pdf->Ln(4);
$pdf->SetX(120);
$pdf->MultiCell(80, 4, "1)  Books Purchased done" . "- 2019-10-31");
$pdf->Ln(2);
$pdf->SetX(120);
$pdf->MultiCell(80, 4, "2)  Cars Purchased done now" . "- 2019-10-24");
$pdf->Ln(2);
$pdf->SetX(120);
$pdf->MultiCell(80, 4, "3)  Registration done" . "- 2019-10-31");
$pdf->Ln(2);
$pdf->SetX(120);
$pdf->MultiCell(80, 4, "4)  Books Purchased done" . "- 2019-10-31");
$pdf->Ln(2);
$pdf->SetX(120);
$pdf->MultiCell(80, 4, "5)  Books Purchased done" . "- 2019-10-31");
$pdf->Ln(2);

$pdf->Image('../../logo/pitchimgfund.png', 111, 114, 98, 27);
$pdf->SetXY(140,120);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Calibri', '', 18);
$pdf->MultiCell(60, 6, "You need a Funding of " . "Rs. 5000000/-");
$pdf->Ln(2);


$pdf->Image('../../logo/pitchimgmrket.png', 0, 142, 210, 60);
$pdf->SetY(159);
$pdf->SetX(0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('calibri-bold', '', 12);

$pdf->MultiCell(50, 5, "Problem Faced By Customer", 0, 'C');
$pdf->SetFont('calibri', '', 11);
$pdf->Ln(4);
$pdf->SetX(0);
$pdf->MultiCell(50, 4, "1)  Books Purchased done");
$pdf->Ln(1);
$pdf->SetX(0);
$pdf->MultiCell(50, 4, "2)  Books Purchased done");


$pdf->SetY(159);
$pdf->SetX(57);
$pdf->SetFont('calibri-bold', '', 12);
$pdf->MultiCell(40, 5, "Solution given to Customer", 0, 'C');
$pdf->SetFont('calibri', '', 11);
$pdf->Ln(4);
$pdf->SetX(52);
$pdf->MultiCell(50, 4, "1)  Books Purchased done");
$pdf->Ln(1);
$pdf->SetX(52);
$pdf->MultiCell(50, 4, "2)  Books Purchased done");

$pdf->SetY(159);
$pdf->SetX(110);
$pdf->SetFont('calibri-bold', '', 12);
$pdf->MultiCell(40, 5, "Market Gap Finding", 0, 'C');
$pdf->SetFont('calibri', '', 11);
$pdf->Ln(4);
$pdf->SetX(105);
$pdf->MultiCell(50, 4, "1)  Books Purchased done");
$pdf->Ln(1);
$pdf->SetX(105);
$pdf->MultiCell(50, 4, "2)  Books Purchased done");

$pdf->SetY(159);
$pdf->SetX(160);
$pdf->SetFont('calibri-bold', '', 12);
$pdf->MultiCell(40, 5, "Who is your target market", 0, 'C');
$pdf->SetFont('calibri', '', 11);
$pdf->Ln(4);
$pdf->SetX(157);
$pdf->MultiCell(50, 4, "1)  Books Purchased done");
$pdf->Ln(1);
$pdf->SetX(157);
$pdf->MultiCell(50, 4, "2)  Books Purchased done");

// $csales = "SELECT * FROM sales where id='vifalo'";
// $result3 = mysqli_query($conn, $csales);
// $rows3 = mysqli_fetch_array($result3, MYSQLI_NUM);
// $salesoln = json_decode($rows3[1], true);
// $salesol = json_decode($rows3[2], true);
// $salesol1 = json_decode($rows3[3], true);
// $salesol2 = json_decode($rows3[4], true);
// $salesofn = json_decode($rows3[5], true);
// $salesof = json_decode($rows3[6], true);
// $salesof1 = json_decode($rows3[7], true);
// $salesof2 = json_decode($rows3[8], true);
// $ds1 = $rows3[9];
// $ds2 = $rows3[10];
// $ds3 = $rows3[11];
// $chs1 = $rows3[12];
// $chs2 = $rows3[13];
// $chs3 = $rows3[14];
$col[0] = array(71, 71, 135);
$col[1] = array(44, 44, 84);
$col[2] = array(19, 15, 64);
$col[3] = array(44, 44, 84);

$ds1 = 40;
$ds2 = 30;
$ds3 = 60;
$chs1 = 10;
$chs2 = 30;
$chs3 = 60;
$data3 = array('1st Year' => $ds1, '2nd Year' => $ds2, '3rd Year' => $ds3);
$pdf->SetTextColor(30, 39, 46);

$pdf->SetDrawColor(255, 255, 255);
$pdf->SetXY(5, 205);

$pdf->SetFont('calibri-bold', '', 12);
$pdf->MultiCell(90, 5, "Describe your sales - Direct sales", 0, 'C');
$pdf->BarDiagram(80, 70, $data3, '%l', $col);

$pdf->SetXY(110, 205);
$pdf->SetTextColor(30, 39, 46);
$pdf->SetFont('calibri-bold', '', 12);
$pdf->MultiCell(100, 5, "Describe your sales - Channel sales", 0, 'C');
$data4 = array('1st Year' => $chs1, '2nd Year' => $chs2, '3rd Year' => $chs3);
$pdf->SetXY(110, 210);
$pdf->BarDiagram(80, 70, $data4, '%l', $col);

$pdf->Output();
?>