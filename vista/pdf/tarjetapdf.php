<?php
require('../../libs/fpdf/fpdf.php');
require_once ('../../modelo/load.php');

$idmasc = isset($_GET['id']) ? $_GET['id']:'';

class PDF extends FPDF
{
   // Cabecera de página
   function Header()
   {
     // Arial bold 15
     $this->SetFont('Arial','B',8);
     // Movernos a la derecha
     $this->Cell(40);
     // Título

     // Salto de línea
     $this->Ln(10);
     $this->Image('../../libs/imagenes/tarjeta.jpg' , 20 ,25, 170 , 52,'JPG',);//fondo
   }

   // Pie de página
   function Footer()
   {
     // Posición: a 1,5 cm del final
     $this->SetY(-15);
     // Arial italic 8
     $this->SetFont('Arial','I',7);
     // Número de página
     $this->Cell(0,5,'Page '.$this->PageNo().'/{nb}',0,0,'C');//fot del perrito
   }
}

$mascota = buscaClienteMascota($idmasc);
                
$foto = "";

$nom_cliente=$mascota['nom_cliente'];
$dir_cliente=$mascota['dir_cliente'];
$tel_cliente=$mascota['tel_cliente'];
$id=$mascota['idcredencial'];
$nombre=$mascota['nombre'];
$foto=$mascota['foto'];

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNBPages();
$pdf->SetXY(9,49);
$pdf->Text(56,50,utf8_decode($nombre));//nombre del perrito

$pdf->SetFont('Arial','B',5);
$pdf->SetXY(55,54);
$pdf->MultiCell(45,3,utf8_decode($dir_cliente),"0",'J',false);
$pdf->Cell(90,6,'  ',0,1,'C');

$pdf->SetFont('Arial','B',8);
$pdf->SetXY(51,36);
$pdf->Cell(48,57,($tel_cliente),0,0,'C',0);// telefono
$pdf->Cell(90,6,'  ',0,1,'C');

$pdf->Cell(104,56,utf8_decode($id),0,0,'C',0);//id 
$pdf->Cell(90,6,'  ',0,1,'C');

if ($foto != "")
   $pdf->Image("data:image/png;base64,".base64_encode($foto), 20 ,45 , 33 , 31,'JPG',);//perrito
$pdf->Output('D');