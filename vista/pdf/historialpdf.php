<?php
   require('../../libs/fpdf/pdf.php');
   require_once ('../../modelo/load.php');

   $usuario= isset($_POST['user']) ? $_POST['user']:'';
   $idmasc = isset($_GET['idMas']) ? $_GET['idMas']:'';


   class PDFH extends FPDF{
   // Cabecera de página
      function Header(){
         // Arial bold 15
         $this->SetFont('Arial','B',18);
         // Movernos a la derecha
         $this->Cell(60);
         // Título
         // Salto de línea
         $this->Ln(10);
         $this->Image('../../libs/imagenes/Logo.png',170,14,40,40,'PNG',);
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
   
   $item = 0;
   $item2 = 0;
   $item4 = 0;

   $consultas = buscaConsultasAsc($idmasc);
   $vacunas = buscaVacunasAsc($idmasc);                    
   $desparasitaciones = buscaDesparasitaciones($idmasc);
   $cliente = buscaClienteMascota($idmasc);

   $nom_cliente=$cliente['nom_cliente'];
   $dir_cliente=$cliente['dir_cliente'];
   $tel_cliente=$cliente['tel_cliente'];
   $correo=$cliente['correo'];
   $nombre=$cliente['nombre'];

   $pdf = new PDFH();
   $pdf->AddPage();
   $pdf->AliasNBPages();
   $pdf->Cell(190,10,utf8_decode('Historial clínico de:'),0,1,'C');
   $pdf->Cell(190,7,$nombre,0, 1 ,'C');
   $pdf->Cell(190,7,utf8_decode('Información del cliente'),0, 1 ,'C');
   $pdf-> SetFillColor(135,207,235);
   $pdf->SetFont('Arial','B',9);
   $pdf->SetFont('Arial','',10);
   $pdf->SetFont('Arial','',12);
   $pdf->Cell(30,6,utf8_decode('Cliente'),1,0,'C',1);
   $pdf->Cell(90,6,utf8_decode($nom_cliente),1,0,'L',0);
   $pdf->Cell(90,6,'  ',0,1,'C');
   $pdf->Cell(30,6,utf8_decode('Dirección'),1,0,'C',1);
   $pdf->Cell(90,6,utf8_decode($dir_cliente),1,0,'L',0);
   $pdf->Cell(90,6,'  ',0,1,'C');
   $pdf->Cell(30,6,utf8_decode('Teléfono'),1,0,'C',1);
   $pdf->Cell(90,6,($tel_cliente),1,0,'L',0);
   $pdf->Cell(90,6,'  ',0,1,'C');
   $pdf->Cell(30,6,'Correo',1,0,'C',1);
   $pdf->Cell(90,6,utf8_decode($correo),1,0,'L',0);
   $pdf->Cell(90,6,'  ',0,1,'C');
   $pdf->SetFont('Arial','B',18);
   $pdf->Cell(40,6,(''),0,1,'C',0);
   $pdf->Cell(40,6,(''),0,1,'C',0);
   $pdf->Cell(190,7,'Historial de vacunas',0, 1 ,'C');
   $pdf-> SetFillColor(135,207,235);
   $pdf->SetFont('Arial','B',12);
   $pdf->Cell(8,6,'No.',1,0,'C',1);
   $pdf->Cell(28,6,'Fecha',1,0,'C',1);
   $pdf->Cell(55,6,'Vacuna',1,0,'C',1);
   $pdf->Cell(104,6,'Nota',1,1,'C',1);
   $pdf->SetFont('Arial','',12);

   while ($row=mysqli_fetch_array($vacunas)){
      $item=$item+1;
      
      $pdf->Cell(8,6,$item,1,0,'C',0);
      $pdf->Cell(28,6,utf8_decode($row['fecha']),1,0,'C',0);
      $pdf->Cell(55,6,utf8_decode($row['vacuna']),1,0,'C',0);
      $pdf->MultiCell(104,6,utf8_decode($row['nota']),1,'C',0);
   }

   $pdf->SetFont('Arial','B',18);
   $pdf->Cell(40,6,(''),0,1,'C',0);
   $pdf->Cell(40,6,(''),0,1,'C',0);
   $pdf->Cell(190,7,utf8_decode('Historial de Desparasitación'),0, 1 ,'C');
   $pdf-> SetFillColor(135,207,235);
   $pdf->SetFont('Arial','B',12);
   $pdf->Cell(8,6,utf8_decode('No.'),1,0,'C',1);
   $pdf->Cell(28,6,('Fecha'),1,0,'C',1);
   $pdf->Cell(55,6,utf8_decode('Desparacitación'),1,0,'C',1);
   $pdf->Cell(104,6,('Nota'),1,1,'C',1);
   $pdf->SetFont('Arial','',12);

   while ($row=mysqli_fetch_array($desparasitaciones)){
      $item2=$item2+1;
      
      $pdf->Cell(8,6,$item2,1,0,'C',0);
      $pdf->Cell(28,6,utf8_decode($row['fecha']),1,0,'C',0);
      $pdf->Cell(55,6,utf8_decode($row['desparasitante']),1,0,'C',0);
      $pdf->MultiCell(104,6,utf8_decode($row['nota']),1,'C',0);
   }

   $pdf->SetFont('Arial','B',18);
   $pdf->Cell(40,6,(''),0,1,'C',0);
   $pdf->Cell(40,6,(''),0,1,'C',0);
   $pdf->Cell(190,7,'Historial de consultas',0, 1 ,'C');
   $pdf-> SetFillColor(135,207,235);
   $pdf->SetFont('Arial','B',12);
   $pdf->SetFont('Arial','',10);
   $pdf->SetFont('Arial','',10);

   while ( $row=mysqli_fetch_array($consultas)){
 	    $item4=$item4+1;

 	    $pdf->Cell(25,6,'No. Consulta',1,0,'C',1);
 		  $pdf->Cell(10,6,$item4,1,0,'C',0);
      $pdf->Cell(20,6,'Peso',1,0,'C',1);
      $pdf->Cell(19,6,$row['peso'],1,0,'C',0);
      $pdf->Cell(6,6,'Kg',1,0,'C',0);
      $pdf->Cell(25,6,('Temperatura'),1,0,'C',1);
      $pdf->Cell(19,6,utf8_decode($row['temperatura']),1,0,'C',0);
      $pdf->Cell(6,6,'C',1,0,'C',0);
      $pdf->Cell(30,6,'Fecha',1,0,'C',1);
      $pdf->Cell(30,6,$row['fecha'],1,1,'C',0);
      $pdf->Cell(40,6,('Problema'),1,0,'C',1);
      $pdf->MultiCell( 150, 6, utf8_decode($row['problema']), 1,'I',0);
      $pdf->Cell(40,6,utf8_decode('Diagnóstico'),1,0,'C',1);
      $pdf->MultiCell( 150, 6, utf8_decode($row['diagnostico']), 1,'I');
      $pdf->Cell(40,6,('Consulta'),1,0,'C',1);
      $pdf->MultiCell(150,6,utf8_decode($row['consulta']),1,'I'); 
      $pdf->Cell(40,6,(''),0,1,'C',0);
      $pdf->Cell(40,6,(''),0,1,'C',0);
   }
   $pdf->Output('D');
?>