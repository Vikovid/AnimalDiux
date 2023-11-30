<?php
  $page_title = 'Abono';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual = date('Y-m-d',time());
  $hora_actual = date('H:i:s',time());
  $fecha = date('d/m/Y',time());
  $diaSemana = date("N", strtotime($fecha_actual));

  $consPagos = buscaRegistroMaximo('pagos','id_ticket');
  $cveTemporal = $consPagos['id_ticket'];

  if ($cveTemporal == ""){
     $consVenta = buscaRegistroMaximo('sales','id_ticket');
     $id_ticket = $consVenta['id_ticket'] + 1;
  }else{
     $id_ticket = $cveTemporal + 1;
  }

  $apartado = sumApartadosXCliente((int)$_GET['idCredencial']);
  $apartCliente = apartadosXCliente((int)$_GET['idCredencial']);
  $vendedores = find_all('users');
  $tipos_pago = find_all('tipo_pago');

  $parametros = find_by_id('parametros','1');
  $comision = $parametros['comision'];

  $user = current_user(); 
  $usuario = $user['name'];
  $sucursal = $user['idSucursal'];
  $idUsuario = $user['id'];

  $nom_cliente = "";
  $dir_cliente = "";
  $tel_cliente = "";
  $ventaLV = 0;
  $comisionLV = 0;
  $ventaSD = 0;
  $comisionSD = 0;
?>
<?php
  include ('../../libs/fpdf/pdf2.php');
  if(isset($_POST['apartado'])){
     $req_fields = array('vendedor','tipoPago','abono');
     validate_fields($req_fields);

     if(empty($errors)){
        $a_abono     = remove_junk($db->escape($_POST['abono']));
        $a_vendedor  = remove_junk($db->escape($_POST['vendedor']));
        $a_tipoPago  = remove_junk($db->escape($_POST['tipoPago']));

        $result = false;

        if ($apartado != false){

           if ($a_abono <= $apartado['monto']){

              $idCredencial = $apartado['idCredencial'];
              $total = $apartado['monto'];
              $porPagar = $total - $a_abono;

              altaFolio($id_ticket);

              $consFolio = buscaRegistroMaximo('folio','id_folio');
              $folio = $consFolio['id_folio'];

              $consCliente = buscaRegistroPorCampo('cliente','idcredencial',$idCredencial);

              if ($consCliente != null){
                 $nom_cliente=$consCliente['nom_cliente'];
                 $dir_cliente=$consCliente['dir_cliente'];
                 $tel_cliente=$consCliente['tel_cliente'];
              }

              $idSales = buscaRegistroMaximo('sales','id');
              $id = $idSales['id'] + 1;
          
              $productos = buscaProdsCredito($sucursal,$idCredencial);

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

              //set width for each column (6 columns)
              $pdf->SetWidths(Array(10,155,40));

              //set alignment
              $pdf->SetAligns(Array('C','L','R'));

              //set line height. This is the height of each lines, not rows.
              $pdf->SetLineHeight(11);

              //$row = mysqli_fetch_array($res);
              foreach ($productos as $producto) {
                 $pdf->Row(Array(
                          $producto['contador'],
                          utf8_decode($producto['name']),
                          $producto['total'],
                         ));

                 altaTicket($id_ticket,$producto['name'],$producto['total'],'0',$total,$porPagar,'0');
              }

              $pdf->SetFont('Arial','B',25);
              $pdf->Cell(165,18,'Total',1,0,'C',1);
              $pdf->CellFitScale(40,18,money_format('%.2n',$total),1,1,'R',1);
              $pdf->SetFont('Arial','B',25);
              $pdf->CellFitScale(165,18,'Tipo de pago',1,0,'C',1);
              $pdf->Cell(40,18,'',1,1,'C',1);

              if($a_tipoPago == "1"){
                 $pdf->Cell(165,18,'Abono Efectivo',1,0,'C',0);
              }
              if($a_tipoPago == "2"){
                 $pdf->Cell(165,18,'Abono Transferencia',1,0,'C',0);
              }
              if($a_tipoPago == "3"){
                 $pdf->Cell(165,18,utf8_decode('Abono Depósito'),1,0,'C',0);
              }
              if($a_tipoPago == "4"){
                 $pdf->Cell(165,18,'Abono Tarjeta',1,0,'C',0);
                 $pdf->Cell(40,18,money_format('%.2n',$a_abono),1,1,'R',0);

                 $comision = $a_abono * 0.05;
                 $pdf->Cell(165,18,utf8_decode('Comisión'),1,0,'C',0);
                 $pdf->Cell(40,18,money_format('%.2n',$comision),1,1,'R',0);                
              }else{
                 $pdf->Cell(40,18,money_format('%.2n',$a_abono),1,1,'R',0);
              }

              $pdf->Cell(165,18,'Por pagar',1,0,'C',0);
              $pdf->Cell(40,18,money_format('%.2n',$porPagar),1,1,'R',0);

              $pdf->SetFont('Arial','B',35);
              $pdf->Cell(80,15,'                                    "Gracias por su Preferencia".',0,1,'C');
              $pdf->Cell(80,15,'                                     "ANIMAL DIUX".',0,1,'C');

              $cliente = $_GET['idCredencial'];
              $cont = 0;

              foreach ($apartCliente as $apart):

                 $primerApart = find_by_id("cuenta",$apart['id']);

                 $productId = $primerApart['productId'];
                 $idCredito = $primerApart['idCredito'];

                 $datosProducto = find_by_id("products",$productId);
                 $categoria = $datosProducto['categorie_id'];

                 if ($cont == 0){
                    $resta = $primerApart['total'] - $a_abono;
                    $abonoProd = $a_abono;
                    $cont++;
                 }else{
                    $abonoProd = $resta;
                    $resta = $primerApart['total'] - $resta;
                 }

                 $idCuenta = $primerApart['id'];

                 if ($resta > 0){
                    $result = actRegistroPorCampo('cuenta','total',$resta,'id',$primerApart['id']);

                    if ($abonoProd > 0){
                       $insResult = altaVenta($id,$productId,'0',$abonoProd,$fecha_actual,$usuario,$sucursal,$a_vendedor,$cliente,'0',$a_tipoPago,$id_ticket,$idCredito,$categoria,$subcategoria);

                       actRegistroPorCampo('products','fechaMod',$fecha_actual,'id',$productId);

                       if ($categoria == "26"){
                          if ($diaSemana < 6){
                             $ventaLV = $abonoProd;
                             $comisionLV = $abonoProd * ($comision/100);
                          }else{
                             $ventaSD = $abonoProd;
                             $comisionSD = $abonoProd * ($comision/100);
                          }

                          altaHistEstetica($productId,$sucursal,$idUsuario,$a_vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$cliente,$fecha_actual,$hora_actual,$id_ticket);

                          altaMovsEstetica($productId,$sucursal,$idUsuario,$a_vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$cliente,$id_ticket,'Abono estetica',$fecha_actual,$hora_actual);
                       }
                    }

                    break;
                 }

                 if ($resta < 0 || $resta == 0){
                    if ($resta < 0){
                       $resta = $resta * -1;
                       $abonoProd = $primerApart['total'];
                    }
                   
                    actCuenta('0','1',$idCuenta);

                    if ($abonoProd > 0){
                       $insResult = altaVenta($id,$productId,'0',$abonoProd,$fecha_actual,$usuario,$sucursal,$a_vendedor,$cliente,'0',$a_tipoPago,$id_ticket,$idCredito,$categoria,$subcategoria);

                       actRegistroPorCampo('products','fechaMod',$fecha_actual,'id',$productId);

                       if ($categoria == "26"){
                          if ($diaSemana < 6){
                             $ventaLV = $abonoProd;
                             $comisionLV = $abonoProd * ($comision/100);
                          }else{
                             $ventaSD = $abonoProd;
                             $comisionSD = $abonoProd * ($comision/100);
                          }

                          altaHistEstetica($productId,$sucursal,$idUsuario,$a_vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$cliente,$fecha_actual,$hora_actual,$id_ticket);

                          altaMovsEstetica($productId,$sucursal,$idUsuario,$a_vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$cliente,$id_ticket,'Abono estetica',$fecha_actual,$hora_actual);
                       }
                    }
                 }

                 $id++;
              endforeach;

              if ($a_tipoPago == "1"){
                 $consMonto = buscaRegistroMaximo("caja","id");
                 $montoActual = $consMonto['monto'];
                 $idCaja = $consMonto['id'];

                 $montoFinal = $montoActual + $a_abono;

                 actCaja($montoFinal,$fecha_actual,$idCaja);

                 altaHisEfectivo('6',$montoActual,$montoFinal,$sucursal,$idUsuario,$a_vendedor,$fecha_actual,$hora_actual);
              }

              altaHisCredito($cliente,$a_abono,$sucursal,$nom_cliente,$fecha_actual,$hora_actual,'0',$id_ticket);

              if ($resta == 0){
                 actRegistroPorCampo('histcredito','pagado','1','idCliente',$cliente);
              }

              altaPago($id_ticket,$a_abono,$a_tipoPago,$fecha_actual,$sucursal,'1');

              if($result || $db->affected_rows() === 1 || $insResult){
                 $Name_PDF="ticket_".$folio.".pdf";
                 $pdf->Output('D',$Name_PDF);
              }else{
                 $pdf->close();
                 $session->msg('d',' Lo siento, falló la actualización.');
                 redirect('edit_apartado.php?idCredencial='.$apartado['idCredencial'], false);
              }
           }else{
              $session->msg('d','El abono es mayor al total de la deuda.');
              redirect('edit_apartado.php?idCredencial='.$apartado['idCredencial'], false);
           }
        }else{
           $session->msg('d','Crédito liquidado.');
           redirect('apartados.php', false);
        }
    //aqui esta ok
     }else{
        $session->msg("d", $errors);
        redirect('edit_apartado.php?idCredencial='.$apartado['idCredencial'], false);
     }
  }
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Abonar al crédito</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-3">
            <form method="post" action="edit_apartado.php?idCredencial=<?php echo (int)$apartado['idCredencial'] ?>">
            <div class="form-group">
               <label for="cliente">Cliente</label>
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <label for="cliente2"><?php echo remove_junk($apartado['cliente']);?></label>
               </div>
            </div>

            <div class="form-group">
               <label for="total">Adeudo</label>
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <label for="total2"><?php echo remove_junk($apartado['monto']);?></label>
               </div>
            </div>

            <div class="form-group">
               <label for="vendedor">Vendedor</label>
               <select class="form-control" name="vendedor">
                  <option value="">Selecciona vendedor</option>
                  <?php  foreach ($vendedores as $id): ?>
                  <option value="<?php echo $id['username'] ?>">
                  <?php echo $id['name'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>

            <div class="form-group">
               <label for="tipoPago">Tipo pago</label>
               <select class="form-control" name="tipoPago">
                  <option value="">Selecciona forma de pago</option>
                  <?php  foreach ($tipos_pago as $id_pago): ?>
                  <option value="<?php echo (int)$id_pago['id_pago'] ?>">
                  <?php echo $id_pago['tipo_pago'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>

            <div class="form-group">
               <label for="Abono">Abono</label>
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-usd"></i>
                  </span>
                  <input type="number" step="0.01" class="form-control" name="abono">
               </div>
            </div>
            <button type="submit" name="apartado" class="btn btn-danger">Actualizar</button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
