<?php
  require_once('../../modelo/load.php');

  $user = current_user(); 
  $idSucursal = $user['idSucursal'];

  $idTicket = $_GET['idTicket'];

  $total = 0;
  $porPagar = 0;

  include ('../../libs/fpdf/pdf2.php');

  $consFolio= buscaRegistroPorCampo('folio','dato',$idTicket);
  $folio = $consFolio['id_folio'];

  $consHistCred = buscaRegistroPorCampo('histcredito','id_ticket',$idTicket);

  if ($consHistCred != null){
     $idCliente = $consHistCred['idCliente'];
     $fecha = date("d/m/Y", strtotime ($consHistCred['fechaPago']));
  }

  $consCliente= buscaRegistroPorCampo('cliente','idcredencial',$idCliente);

  if ($consCliente != null){
     $nom_cliente=$consCliente['nom_cliente'];
     $dir_cliente=$consCliente['dir_cliente'];
     $tel_cliente=$consCliente['tel_cliente'];
  }

  $productos = buscaProductosTicket($idTicket);

  $consPago = buscaPagoCredito($idTicket,$idSucursal);

  if ($consPago != null){
     $tipoPago = $consPago['id_tipo'];
     $abono = $consPago['cantidad'];
  }

  $pdf=new PDF2($orientation='P',$unit='mm');
  $pdf->AddPage('portrait','legal');
  $pdf->SetMargins(5, 5 , 5,5);
  $pdf->SetAutoPageBreak(true,5); 
  $pdf->SetFont('Arial','B',70);
  $pdf->Image('../../libs/imagenes/Logo.png' , 0 ,0, 40 , 40,'PNG', );
  $pdf->Cell(120,10,'               TICKET',0,1,'C');
  $pdf->Cell(80,15,'    ',0,1,'C');
  $pdf->Cell(80,15,'                      Veterinaria',0,1,'C');
  $pdf->Cell(80,8,'    ',0,1,'C');
  $pdf->Cell(80,15,'                       Animal Diux  ',0,1,'C');
  $pdf->Cell(80,15,'    ',0,1,'C');
  $pdf->SetFillColor(232,232,232);
  $pdf->SetFont('Arial','B',18);
  $pdf->Text(5,83,'Blvd. de los Dioses Mz. 319 Lt. 2 Ciudad Azteca Poniente, Ecatepec');
  $pdf->Text(18,91,'Tel. 55 6875 4535 / 55 6286 3495 Mail: animaldiux@gmail.com');
  $pdf->Cell(80,8,'    ',0,1,'C');
  $pdf->SetFont('Arial','B',25);
  $pdf->Cell(50,15,utf8_decode('Remisión:'),1,0,'C',1);
  $pdf->CellFitScale(30,15,utf8_decode($folio),1,0,'C',0);
  $pdf->Cell(45,15,'Fecha: ',1,0,'C',1);
  $pdf->Cell(50,15,utf8_decode($fecha),1,0,'C',0);
  $pdf->Cell(80,15,'  ',0,1,'C');
  $pdf->Cell(50,15,'Cliente',1,0,'C',1);
  if ($nom_cliente != ""){
     $pdf->CellFitScale(155,15,utf8_decode($nom_cliente),1,0,'L',1);
  }else{
     $pdf->Cell(155,15,utf8_decode($nom_cliente),1,0,'L',1);   
  }
  $pdf->Cell(80,15,'  ',0,1,'C');
  $pdf->Cell(50,15,utf8_decode('Dirección'),1,0,'C',1);
  if ($dir_cliente != ""){
    $pdf->CellFitScale(155,15,utf8_decode($dir_cliente),1,0,'L',1);
  }else{
    $pdf->Cell(155,15,utf8_decode($dir_cliente),1,0,'L',1);
  }
  $pdf->Cell(80,15,'  ',0,1,'C');
  $pdf->Cell(50,15,utf8_decode('Teléfono'),1,0,'C',1);
  $pdf->Cell(155,15,utf8_decode($tel_cliente),1,0,'L',1);
  $pdf->Cell(80,15,'  ',0,1,'C');
  $pdf->Cell(80,10,'  ',0,1,'C');
  $pdf->SetFillColor(232,232,232);
  $pdf->SetFont('Arial','B',31);
  $pdf->Cell(10,15,utf8_decode('N'),1,0,'C',1);
  $pdf->Cell(155,15,'Concepto',1,0,'C',1);
  $pdf->CellFitScale(40,15,'Precio',1,1,'C',1);
  $pdf->SetFont('Arial','B',28);

  //set width for each column (3 columns)
  $pdf->SetWidths(Array(10,155,40));

  //set alignment
  $pdf->SetAligns(Array('C','L','R'));

  //set line height. This is the height of each lines, not rows.
  $pdf->SetLineHeight(11);

  //$row = mysqli_fetch_array($resultado);
  foreach ($productos as $producto) {
     $pdf->Row(Array(
               $producto['contador'],
               utf8_decode($producto['nomProducto']),
               $producto['precio'],
              ));
     $total = $producto['descPuntos'];
     $porPagar = $producto['descPorc'];
  }

  $pdf->SetFont('Arial','B',25);
  $pdf->Cell(165,18,'Total',1,0,'C',1);
  $pdf->CellFitScale(40,18,money_format('%.2n',$total),1,1,'R',1);
  $pdf->SetFont('Arial','B',25);
  $pdf->CellFitScale(165,18,'Tipo de pago',1,0,'C',1);
  $pdf->Cell(40,18,'',1,1,'C',1);

  if($tipoPago == "1"){
     $pdf->Cell(165,18,'Abono Efectivo',1,0,'C',0);
  }
  if($tipoPago == "2"){
     $pdf->Cell(165,18,'Abono Transferencia',1,0,'C',0);
  }
  if($tipoPago == "3"){
     $pdf->Cell(165,18,utf8_decode('Abono Depósito'),1,0,'C',0);
  }
  if($tipoPago == "4"){
     $pdf->Cell(165,18,'Abono Tarjeta',1,0,'C',0);
     $pdf->Cell(40,18,money_format('%.2n',$abono),1,1,'R',0);

     $comision = $abono * 0.05;
     $pdf->Cell(165,18,utf8_decode('Comisión'),1,0,'C',0);
     $pdf->Cell(40,18,money_format('%.2n',$comision),1,1,'R',0);                
  }else{
     $pdf->Cell(40,18,money_format('%.2n',$abono),1,1,'R',0);
  }

  $pdf->Cell(165,18,'Por pagar',1,0,'C',0);
  $pdf->Cell(40,18,money_format('%.2n',$porPagar),1,1,'R',0);

  $pdf->SetFont('Arial','B',35);
  $pdf->Cell(80,15,'                                    "Gracias por su Preferencia".',0,1,'C');
  $pdf->Cell(80,15,'                                     "ANIMAL DIUX".',0,1,'C');

  $Name_PDF="ticket_".$folio.".pdf";
  $pdf->Output('D',$Name_PDF);
?>
