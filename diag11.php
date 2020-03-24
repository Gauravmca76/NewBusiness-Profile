<?php
require('sector.php');

class PDF_Diag extends PDF_Sector {
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
                $this->Sector($XDiag-70, $YDiag+10, $radius, $angleStart, $angleEnd);
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
            $this->Rect($x1-70, $y1, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-70,$y1);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }

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
    
    function ColumnChart($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
    {
        $colors[0][0] = 155;
        $colors[0][1] = 75;
        $colors[0][2] = 155;

        $colors[1][0] = 0;
        $colors[1][1] = 155;
        $colors[1][2] = 0;

        $colors[2][0] = 75;
        $colors[2][1] = 155;
        $colors[2][2] = 255;

        $colors[3][0] = 75;
        $colors[3][1] = 0;
        $colors[3][2] = 155;
        $this->SetFont('Courier', '', 10);
        //$this->SetLegends($data,$format);
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2; 
        $YDiag = $YPage + $margin;
        $hDiag = floor($h - $margin * 2);
        $XDiag = $XPage + $margin;
        $lDiag = floor($w - $margin * 3 - $this->wLegend);
        if($color == null)
            $color=array(155,155,155);
        if ($maxVal == 0) 
        {
            foreach($data as $val)
            {
                if(max($val) > $maxVal)
                {
                    $maxVal = max($val);
                }
            }
        }
        $valIndRepere = ceil($maxVal / $nbDiv);
        $maxVal = $valIndRepere * $nbDiv;
        $hRepere = floor($hDiag / $nbDiv);
        $hDiag = $hRepere * $nbDiv;
        $unit = $hDiag / $maxVal;
        $lBar = floor($lDiag / ($this->NbVal + 1));
        $lDiag = $lBar * ($this->NbVal + 1)-10;
        $eColumn = floor($lBar * 80 / 100);
        $this->SetLineWidth(0.2);
        $this->Rect($XDiag-75, $YDiag+10, $lDiag, $hDiag);

        $this->SetFont('Courier', '', 10);
        $this->SetFillColor($color[0],$color[1],$color[2]);
        $i=0; $dt=2020; 
        foreach($data as $val) 
        {
            //Column
            $yval = $YDiag + $hDiag;
            $xval = $XDiag + ($i + 1) * $lBar - $eColumn/2;
            $lval = floor($eColumn/(count($val)));
            $j=0;  
            foreach($val as $v)
            {
                $hval = (int)($v * $unit);
                $this->SetFillColor($colors[$j][0], $colors[$j][1], $colors[$j][2]);
                $this->Rect($xval+($lval*$j)-75, $yval+10, $lval, -$hval, 'DF');
                $j++; 
            }
            //$this->Text($xval+($lval*$j)-12, $yval+15,$dt);//X-axises
            $i++; $dt+=1;
        }
        //Legends
        $this->SetFont('Courier', 'I', 10);
        $x1 = $XPage + 2 * 34 + 3 * -10;//right and left legends move
        $x2 = $x1 + 15 + $margin;
        $y1 = $YDiag - 34 + (2 * 34 - $this->NbVal*(-40 + $margin)) / 2;//down and up side legends move
        for($i = 0; $i < 1; $i++) 
        {
        $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->Rect($xval+($lval*$j)-130, $yval+20, 5, 5, 'DF');//legends symbol
        $y1+= 5 + $margin;
        }
        
        for($i = 0; $i < 1; $i++) 
        {
        $this->SetFillColor($colors[1][0],$colors[1][1],$colors[1][2]);
        $this->Rect($xval+($lval*$j)-100, $yval+20, 5, 5, 'DF');//legends symbol
        $y1+= 5 + $margin;
        }
        $this->SetXY($xval+($lval*$j)-123, $yval+20);//bartext position
        $this->Cell(0,5,'Income',0,1);//bartext
        $this->ln(2);
        $this->SetXY($xval+($lval*$j)-93, $yval+20);//bartext position
        $this->Cell(0,5,'Expense',0,0);//bartext

        //Scales
        for ($i = 0; $i <= $nbDiv; $i++) 
        {
            $ypos = $YDiag + $hRepere * $i;
            $this->Line($XDiag-75, $ypos+10, $XDiag + $lDiag-75, $ypos+10);
            $val = ($nbDiv - $i) * $valIndRepere;
            $ypos = $YDiag + $hRepere * $i;
            $xpos = $XDiag - $margin - $this->GetStringWidth($val);
            $this->Text($xpos-76, $ypos+12 , $val);
        }
    }

    function ColumnChart1($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
    {
        $colors[0][0] = 155;
        $colors[0][1] = 75;
        $colors[0][2] = 155;

        $colors[1][0] = 0;
        $colors[1][1] = 155;
        $colors[1][2] = 0;

        $colors[2][0] = 75;
        $colors[2][1] = 155;
        $colors[2][2] = 255;

        $colors[3][0] = 75;
        $colors[3][1] = 0;
        $colors[3][2] = 155;
        $this->SetFont('Courier', '', 10);
        //$this->SetLegends($data,$format);
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2; 
        $YDiag = $YPage + $margin;
        $hDiag = floor($h - $margin * 2);
        $XDiag = $XPage + $margin;
        $lDiag = floor($w - $margin * 3 - $this->wLegend);
        if($color == null)
            $color=array(155,155,155);
        if ($maxVal == 0) 
        {
            foreach($data as $val)
            {
                if(max($val) > $maxVal)
                {
                    $maxVal = max($val);
                }
            }
        }
        $valIndRepere = ceil($maxVal / $nbDiv);
        $maxVal = $valIndRepere * $nbDiv;
        $hRepere = floor($hDiag / $nbDiv);
        $hDiag = $hRepere * $nbDiv;
        $unit = $hDiag / $maxVal;
        $lBar = floor($lDiag / ($this->NbVal + 1));
        $lDiag = $lBar * ($this->NbVal + 1)-10;
        $eColumn = floor($lBar * 80 / 100);
        $this->SetLineWidth(0.2);
        $this->Rect($XDiag-75, $YDiag+10, $lDiag, $hDiag);

        $this->SetFont('Courier', '', 10);
        $this->SetFillColor($color[0],$color[1],$color[2]);
        $i=0; $dt=2020; 
        foreach($data as $val) 
        {
            //Column
            $yval = $YDiag + $hDiag;
            $xval = $XDiag + ($i + 1) * $lBar - $eColumn/2;
            $lval = floor($eColumn/(count($val)));
            $j=0;  
            foreach($val as $v)
            {
                $hval = (int)($v * $unit);
                $this->SetFillColor($colors[$j][0], $colors[$j][1], $colors[$j][2]);
                $this->Rect($xval+($lval*$j)-75, $yval+10, $lval, -$hval, 'DF');
                $j++; 
            }
            //$this->Text($xval+($lval*$j)-12, $yval+15,$dt);//X-axises
            $i++; $dt+=1;
        }
        //Legends
        $this->SetFont('Courier', 'I', 10);
        $x1 = $XPage + 2 * 34 + 3 * -10;//right and left legends move
        $x2 = $x1 + 15 + $margin;
        $y1 = $YDiag - 34 + (2 * 34 - $this->NbVal*(-40 + $margin)) / 2;//down and up side legends move
        for($i = 0; $i < 1; $i++) 
        {
        $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->Rect($xval+($lval*$j)-130, $yval+20, 5, 5, 'DF');//legends symbol
        $y1+= 5 + $margin;
        }
        
        for($i = 0; $i < 1; $i++) 
        {
        $this->SetFillColor($colors[1][0],$colors[1][1],$colors[1][2]);
        $this->Rect($xval+($lval*$j)-100, $yval+20, 5, 5, 'DF');//legends symbol
        $y1+= 5 + $margin;
        }
        $this->SetXY($xval+($lval*$j)-123, $yval+20);//bartext position
        $this->Cell(0,5,'Asset',0,1);//bartext
        $this->ln(2);
        $this->SetXY($xval+($lval*$j)-93, $yval+20);//bartext position
        $this->Cell(0,5,'Liability',0,0);//bartext

        //Scales
        for ($i = 0; $i <= $nbDiv; $i++) 
        {
            $ypos = $YDiag + $hRepere * $i;
            $this->Line($XDiag-75, $ypos+10, $XDiag + $lDiag-75, $ypos+10);
            $val = ($nbDiv - $i) * $valIndRepere;
            $ypos = $YDiag + $hRepere * $i;
            $xpos = $XDiag - $margin - $this->GetStringWidth($val);
            $this->Text($xpos-76, $ypos+12 , $val);
        }
    }
}
?>