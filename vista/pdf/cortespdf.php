<?php
   require('../../libs/fpdf/pdf.php');
   require_once ('../../modelo/load.php');

   $c_idEncargado = isset($_POST['encargado']) ? $_POST['encargado']:'';

   $parametros = find_by_id("parametros","1");

   $porcComision = $parametros['comision'];

   if ($c_idEncargado!=""){
      $result = find_by_id("users",$c_idEncargado);
      $resultados = corteVendedor($result['username']);
   }else{
      $resultados =  corte();
   }


   class PDF_MC_Table extends FPDF{
      // Cabecera de página
      function Header(){
         // Arial bold 15
         $this->SetFont('Arial','B',18);
         // Movernos a la derecha
         $this->Cell(60);
         // Título
         $this->Cell(70,10,'Reporte de cortes',0,0,'C');
         // Salto de línea
         $this->Ln(20);
      }

      // Pie de página
      function Footer(){
         // Posición: a 1,5 cm del final
         $this->SetY(-15);
         // Arial italic 8
         $this->SetFont('Arial','I',8);
         // Número de página
         $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
      }
   }

   $pdf =new PDF_MC_Table();
   $pdf->AddPage();
   $pdf->AliasNBPages();
   $pdf-> SetFillColor(232,232,232);
   $pdf->SetFont('Arial','B',9);
   $pdf->Cell(50,6,('Nombre'),1,0,'C',1);
   $pdf->Cell(30,6,'Sucursal',1,0,'C',1);
   $pdf->Cell(30,6,'Venta/$',1,0,'C',1);
   $pdf->Cell(30,6,'Comision/$',1,0,'C',1);
   $pdf->Cell(35,6,'Fecha',1,1,'C',1);
   $pdf->SetFont('Arial','',10);
   $pdf->SetFont('Arial','',7);

   foreach ($resultados as $row) {

      $comision = $row['venta'] * ($porcComision/100);

      $pdf->Cell(50,6,utf8_decode($row['vendedor']),1,0,'C',0);
      $pdf->Cell(30,6,$row['nom_sucursal'],1,0,'C',0);
      $pdf->Cell(30,6,money_format("%.2n",$row['venta']),1,0,'C',0);
      $pdf->Cell(30,6,money_format("%.2n",$comision),1,0,'C',0);
      $pdf->Cell(35,6,date("d-m-Y", strtotime ($row['date'])),1,1,'C',0);
    
   }

   $pdf->Output('D');
?>