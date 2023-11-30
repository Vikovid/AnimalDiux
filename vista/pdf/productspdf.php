<?php
require('../../libs/fpdf/fpdf.php');
require_once ('../../modelo/load.php');

$p_scu = isset($_POST['categoria']) ? $_POST['categoria']:'';
$codigo = isset($_POST['Codigo']) ? $_POST['Codigo']:'';

class PDF_MC_Table extends FPDF {
    // variable to store widths and aligns of cells, and line height
    var $widths;
    var $aligns;
    var $lineHeight;

    //Set the array of column widths
    function SetWidths($w){
        $this->widths=$w;
    }

    //Set the array of column alignments
    function SetAligns($a){
        $this->aligns=$a;
    }

    //Set line height
    function SetLineHeight($h){
        $this->lineHeight=$h;
    }

    //Calculate the height of the row
    function Row($data)
    {
        // number of line
        $nb=0;
        
        // loop each data to find out greatest line number in a row.
        for($i=0;$i<count($data);$i++){
            // NbLines will calculate how many lines needed to display text wrapped in specified width.
            // then max function will compare the result with current $nb. Returning the greatest one. And reassign the $nb.
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        }
        
        //multiply number of line with line height. This will be the height of current row
        $h=$this->lineHeight * $nb;
        
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        
        //Draw the cells of current row
        for($i=0;$i<count($data);$i++)
        {
            // width of the current col
            $w=$this->widths[$i];
            
            // alignment of the current col. if unset, make it left.
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //calculate the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

// Cabecera de página
function Header()
{

    // Arial bold 15
    $this->SetFont('Helvetica','B',18);
    // Movernos a la derecha
    $this->Cell(60);
    // Título
    $this->Cell(70,10,'Reporte de productos',0,0,'C');

    $this->SetDrawColor(13,19,213);
    $this->SetLineWidth(0.5);
    // Salto de línea
    $this->Line(63,20,147,20);
   
    $this->Ln(20);
    $this->Image('../../libs/imagenes/Logo.png' , 175 ,5, 20 , 20,'PNG',);

    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(0.1);

    $this-> SetFillColor(21,201,209);
    $this->SetFont('Arial','B',9);
    $this->Cell(90,6,('Nombre'),1,0,'C',1);
    $this->Cell(32,6,'Categoria',1,0,'C',1);
    $this->Cell(16,6,'Cantidad',1,0,'C',1);
    $this->Cell(16,6,'Costo',1,0,'C',1);
    $this->Cell(16,6,'Precio',1,0,'C',1);
    $this->Cell(27,6,'Sucursal',1,1,'C',1);
    $this->SetFont('Arial','',10);


    //$this->SetFont('Arial','',10);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
}


}

if ($codigo!="" & $p_scu!="") {
   $resultado = productosCodCatPDF($codigo,$p_scu);
}
elseif ($p_scu!="") {
   $resultado = productosCatPDF($p_scu);
}
elseif ($codigo!="") {
   $resultado = productosCodPDF($codigo);
}
else{
   $resultado = productosPDF();
}

$pdf = new PDF_MC_Table();

//add page, set font
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

//set width for each column (6 columns)
$pdf->SetWidths(Array(90,32,16,16,16,27));

//set alignment
$pdf->SetAligns(Array('C','C','R','R','R','C'));

//set line height. This is the height of each lines, not rows.
$pdf->SetLineHeight(5);

//load json data
//$row = mysqli_fetch_array($resultado);
foreach ($resultado as $row) {

    $pdf->Row(Array(
        utf8_decode($row['name']),
        utf8_decode($row['categories']),
        $row['quantity'],
        $row['buy_price'],
        $row['sale_price'],
        $row['nom_sucursal'],
    ));
}

$pdf->Output('D');
?>