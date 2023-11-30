<?php
  $page_title = 'Editar venta';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $user = current_user(); 
  $idUsuario = $user['id'];

  $idVenta = $_GET['id'];
  $vendedor = $_GET['vendedor'];
  $fecha    = $_GET['fecha'];

  $fechaFin = strtotime ($fecha);
  $fechaFin = date ('d-m-Y',$fechaFin);

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  $sale = find_by_id('sales',(int)$_GET['id']);
  if(!$sale){
    $session->msg("d","No se encontró la venta.");
    redirect('sales.php');
  }

  $idProducto = $sale['product_id'];
  $idSucursal = $sale['idSucursal'];
  $dateVenta = $sale['date'];
  $fechaVenta = date("Y-m-d", strtotime($dateVenta));
  $idTicket = $sale['id_ticket']; 

  $producto = find_by_id("products",$idProducto);
  $cantProd = $producto['quantity'];

  $Efectivo = tipoPagoTTP($idTicket,"1");
  $Transferencia = tipoPagoTTP($idTicket,"2");
  $Deposito = tipoPagoTTP($idTicket,"3");
  $Tarjeta = tipoPagoTTP($idTicket,"4");

  $cantEfec = 0;
  $tipoEfec = "";
  $cantTrans = 0;
  $tipoTrans = "";
  $cantDep = 0;
  $tipoDep = "";
  $cantTar = 0;
  $tipoTar = "";
  $movimiento = "";
  $actEfec = "";
  $altaEfec = "";
  $borraPago = "";
  $actTrans = "";
  $borraTrans = "";
  $altaTrans = "";
  $actDep = "";
  $altaDep = "";
  $borraDep = "";
  $actTar = "";
  $altaTar = "";
  $borraTar = "";
  $fechaPagos = "";

  if ($Efectivo != null){
     $cantEfec = $Efectivo['cantidad'];
     $tipoEfec = $Efectivo['tipo_pago'];
  }

  if ($Transferencia != null){
     $cantTrans = $Transferencia['cantidad'];
     $tipoTrans = $Transferencia['tipo_pago'];
  }

  if ($Deposito != null){
     $cantDep = $Deposito['cantidad'];
     $tipoDep = $Deposito['tipo_pago'];
  }

  if ($Tarjeta != null){
     $cantTar = $Tarjeta['cantidad'];
     $tipoTar = $Tarjeta['tipo_pago'];
  }

  $total = $cantEfec + $cantTrans + $cantDep + $cantTar;
  $totalOrig = $total;

  if(isset($_POST['update_sale'])){
     $req_fields = array('precio','fecha','comentario');
     validate_fields($req_fields);
     if(empty($errors)){
        $s_efectivo = $db->escape($_POST['efectivo']);
        $s_transferencia = $db->escape($_POST['transferencia']);
        $s_deposito = $db->escape($_POST['deposito']);
        $s_tarjeta = $db->escape($_POST['tarjeta']);
        $s_comentario = $db->escape($_POST['comentario']);
        $s_precio = $db->escape($_POST['precio']);
        $date = $db->escape($_POST['fecha']);
        $s_fecha = date("Y-m-d", strtotime($date));
        $s_totalPago = $db->escape($_POST['totalPago']);
        $s_totalVenta = $db->escape($_POST['totalVenta']);

        if ($s_efectivo == "" && $s_transferencia == "" && $s_deposito == "" && $s_tarjeta == ""){
           $session->msg('d','Debe proporcionar al menos una cantidad para una forma de pago.');
           redirect('edit_sale.php?id='.$sale['id'].'&vendedor='.$sale['vendedor'].'&fecha='.$sale['date'],false);
        }

        if ($s_totalPago != $s_totalVenta){
           $session->msg('d','El Total de venta es diferente al Total de pago.');
           redirect('edit_sale.php?id='.$sale['id'].'&vendedor='.$sale['vendedor'].'&fecha='.$sale['date'],false);
        }

        $consMonto = buscaRegistroMaximo("caja","id");
        $montoActual = $consMonto['monto'];
        $idCaja = $consMonto['id'];

        if ($s_efectivo != "" && $cantEfec > 0 && $s_efectivo != $cantEfec){
           $actEfec = "1";

           $totEfec = $s_efectivo - $cantEfec;

           if ($totEfec > 0)
              $movimiento = "9";
           else
              $movimiento = "10";

           $montoFinal = $montoActual + $totEfec;
        }

        if ($s_efectivo != "" && $cantEfec == 0){
           $altaEfec = "1";
           $movimiento = "9";

           $montoFinal = $montoActual + $s_efectivo;
        }

        if ($cantEfec > 0 && $s_efectivo == ""){
           $borraPago = "1";
           $movimiento = "10";

           $montoFinal = $montoActual - $cantEfec;
        }

        if ($s_transferencia != "" && $cantTrans > 0 && $s_transferencia != $cantTrans){
           $actTrans = "1";
        }

        if ($s_transferencia != "" && $cantTrans == 0){
           $altaTrans = "1";
        }

        if ($cantTrans > 0 && $s_transferencia == ""){
           $borraTrans = "1";
        }

        if ($s_deposito != "" && $cantDep > 0 && $s_deposito != $cantDep){
           $actDep = "1";
        }

        if ($s_deposito != "" && $cantDep == 0){
           $altaDep = "1";
        }

        if ($cantDep > 0 && $s_deposito == ""){
           $borraDep = "1";
        }

        if ($s_tarjeta != "" && $cantTar > 0 && $s_tarjeta != $cantTar){
           $actTar = "1";
        }

        if ($s_tarjeta != "" && $cantTar == 0){
           $altaTar = "1";
        }

        if ($cantTar > 0 && $s_tarjeta == ""){
           $borraTar = "1";
        }

        if ($s_fecha != $fechaVenta){
           $fechaPagos = "1";
        }

        $resVentas = actVentaPrecioFecha($s_precio,$s_fecha,$sale['id']);
        //if ($resVentas){
           if ($actEfec == "1"){
              actCantFechaPagos($s_efectivo,$s_fecha,$sale['id_ticket'],'1');
           }
           if ($altaEfec == "1"){
              altaPago($idTicket,$s_efectivo,'1',$s_fecha,$idSucursal,'0');
           }
           if ($borraPago == "1"){
              borraPagoTicketTipo($idTicket,'1');
           }
           if ($actEfec == "1" || $altaEfec == "1" || $borraPago == "1"){
              if ($s_fecha == $fecha_actual)
                 actCaja($montoFinal,$fecha_actual,$idCaja);
              registrarEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$idUsuario,'',$fecha_actual,$hora_actual);
           }
           if ($actTrans == "1"){
              actCantFechaPagos($s_transferencia,$s_fecha,$sale['id_ticket'],'2');
           }
           if ($altaTrans == "1"){
              altaPago($idTicket,$s_transferencia,'2',$s_fecha,$idSucursal,'0');
           }
           if ($borraTrans == "1"){
              borraPagoTicketTipo($idTicket,'2');
           }
           if ($actDep == "1"){
              actCantFechaPagos($s_deposito,$s_fecha,$sale['id_ticket'],'3');
           }
           if ($altaDep == "1"){
              altaPago($idTicket,$s_deposito,'3',$s_fecha,$idSucursal,'0');
           }
           if ($borraDep == "1"){
              borraPagoTicketTipo($idTicket,'3');
           }
           if ($actTar == "1"){
              actCantFechaPagos($s_tarjeta,$s_fecha,$sale['id_ticket'],'4');
           }
           if ($altaTar == "1"){
              altaPago($idTicket,$s_tarjeta,'4',$s_fecha,$idSucursal,'0');
           }
           if ($borraTar == "1"){
              borraPagoTicketTipo($idTicket,'4');
           }
           if ($fechaPagos == "1"){
              actRegistroPorCampo('pagos','fecha',$s_fecha,'id_ticket',$sale['id_ticket']);
           }
              
           actRegistroPorCampo('sales','date',$s_fecha,'id_ticket',$sale['id_ticket']);
           altaHistorico('2',$idProducto,$cantProd,$cantProd,$s_comentario,$idSucursal,$idUsuario,'',$fecha_actual,$hora_actual);

           $session->msg('s',"Venta actualizada.");
           redirect('edit_sale.php?id='.$sale['id'].'&vendedor='.$sale['vendedor'].'&fecha='.$sale['date'], false);
        /*}else{
           $session->msg('d','Falló la actualización!');
           redirect('sales.php', false);
        }*/
     }else{
        $session->msg("d", $errors);
        redirect('edit_sale.php?id='.$sale['id'].'&vendedor='.$sale['vendedor'].'&fecha='.$sale['date'],false);
     }
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script language="Javascript">

function foco(){
   document.form1.precio.focus();
}

function suma(){
   var efectivo = 0;
   var transferencia = 0;
   var deposito = 0;
   var tarjeta = 0;
   var sumaTotal = 0;

   if (document.form1.efectivo.value != "")
      efectivo = parseFloat(document.form1.efectivo.value);
   if (document.form1.transferencia.value != "")
      transferencia = parseFloat(document.form1.transferencia.value);
   if (document.form1.deposito.value != "")
      deposito = parseFloat(document.form1.deposito.value);
   if (document.form1.tarjeta.value != "")
      tarjeta = parseFloat(document.form1.tarjeta.value);
  
   sumaTotal = efectivo + transferencia + deposito + tarjeta;
   
   document.form1.totalPago.value = sumaTotal;
}

function sumaTotal(){
   var precioMod = 0;
   var precioOrig = 0;
   var totalOrig = 0;
   var sumaTotal = 0;

   if (document.form1.precio.value != ""){
      precioMod = parseFloat(document.form1.precio.value);
      precioOrig = parseFloat(document.form1.precioOrig.value);
      totalOrig = parseFloat(document.form1.totalOrig.value);
   }

   sumaTotal = totalOrig - precioOrig + precioMod;

   document.form1.totalVenta.value = sumaTotal;
}

</script>

<body onload="focoPrecio();">
  <form name="form1" method="post" action="edit_sale.php?id=<?php echo (int)$sale['id'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>">
    <div class="row">
       <div class="col-md-6">
          <?php echo display_msg($msg); ?>
       </div>
    </div>
    <div class="row">
       <div class="col-md-12">
          <div class="panel panel-default">
             <div class="panel-heading">
                <strong>
                   <span class="glyphicon glyphicon-th"></span>
                   <span><?php echo "Vendedor: $vendedor    Fecha: $fechaFin"; ?></span>   
                </strong>
             </div>
          </div>
       </div>
    </div>
    <div class="row">
       <div class="col-md-8">
          <div class="panel panel-default">
             <div class="panel-heading">
                <strong>
                   <span class="glyphicon glyphicon-th"></span>
                   <span>Productos</span>
                </strong>
             </div>
             <div class="panel-body">
                <table class="table table-striped table-bordered table-condensed">
                   <thead>
                      <tr>
                         <th style="width: 70%;">Producto</th>
                         <th style="width: 10%;">Cantidad</th>
                         <th style="width: 20%;">Total</th>
                      </tr>
                   </thead>
                   <tbody>
                      <tr>
                         <?php $ventas = ventas($idTicket); ?>

                         <?php foreach ($ventas as $venta):?>
                            <td> <?php echo remove_junk($venta['name']); ?></td>
                            <td> <?php echo remove_junk($venta['qty']); ?></td>
                            <?php if ($idVenta != $venta['id']){ ?>
                            <td> <?php echo remove_junk($venta['price']); ?></td>
                            <?php }else{ ?>
                            <td><input type="number" step="0.01" name="precio" value="<?php echo remove_junk($venta['price']);?>" onkeyup="sumaTotal();" style="border-color: blue; border-width: 2px;"></td>
                            <?php $precioOrig = $venta['price']; ?>
                         <?php } ?>
                      </tr>
                         <?php endforeach; ?>
                   </tbody>
                </table>
                <table class="table table-striped table-bordered table-condensed">
                   <tbody>
                      <tr>
                         <td style="width: 74%;">Total venta</td>
                         <td><input type="number" step="0.01" class="form-control" name="totalVenta" value="<?php echo remove_junk($total); ?>" readonly></td>
                      </tr>
                   </tbody>
                </table>
             </div>
          </div>
       </div>
       <div class="col-md-4">
          <div class="panel panel-default">
             <div class="panel-heading">
                <strong>
                   <span class="glyphicon glyphicon-th"></span>
                   <span>Información del pago</span>
                </strong>
             </div>
             <div class="panel-body">
                <table class="table table-striped table-bordered table-condensed">
                   <thead>
                      <tr>
                         <th style="width: 60%;">Forma de Pago</th>
                         <th style="width: 40%;">Total</th>
                      </tr>
                   </thead>
                   <tbody>
                      <tr>
                         <?php if ($tipoEfec == ""){ ?>
                            <td>Efectivo</td>
                            <td><input type="number" step="0.01" class="form-control" name="efectivo" value="" onkeyup="suma();"></td>
                         <?php }else{ ?>
                            <td><?php echo remove_junk($Efectivo['tipo_pago']); ?></td>
                            <td><input type="number" step="0.01" class="form-control" name="efectivo" value="<?php echo remove_junk($cantEfec); ?>" onkeyup="suma();"></td>
                         <?php } ?>
                      </tr>
                      <tr>
                         <?php if ($tipoTrans == ""){ ?>
                            <td>Transferencia</td>
                            <td><input type="number" step="0.01" class="form-control" name="transferencia" value="" onkeyup="suma();"></td>
                         <?php }else{ ?>
                            <td><?php echo remove_junk($Transferencia['tipo_pago']); ?></td>
                            <td><input type="number" step="0.01" class="form-control" name="transferencia" value="<?php echo remove_junk($cantTrans); ?>" onkeyup="suma();"></td>
                         <?php } ?>
                      </tr>
                      <tr>
                         <?php if ($tipoDep == ""){ ?>
                            <td>Deposito</td>
                            <td><input type="number" step="0.01" class="form-control" name="deposito" value="" onkeyup="suma();"></td>
                         <?php }else{ ?>
                            <td><?php echo remove_junk($Deposito['tipo_pago']); ?></td>
                            <td><input type="number" step="0.01" class="form-control" name="deposito" value="<?php echo remove_junk($cantDep); ?>" onkeyup="suma();"></td>
                         <?php } ?>
                      </tr>
                      <tr>
                         <?php if ($tipoTar == ""){ ?>
                            <td>Tarjeta</td>
                            <td><input type="number" step="0.01" class="form-control" name="tarjeta" value="" onkeyup="suma();"></td>
                         <?php }else{ ?>
                            <td><?php echo remove_junk($Tarjeta['tipo_pago']); ?></td>
                            <td><input type="number" step="0.01" class="form-control" name="tarjeta" value="<?php echo remove_junk($cantTar); ?>" onkeyup="suma();"></td>
                         <?php } ?>
                      </tr>
                   </tbody>
                </table>
                <table class="table table-striped table-bordered table-condensed">
                   <tbody>
                      <tr>
                         <td>Total pago</td>
                         <td><input type="number" step="0.01" class="form-control" name="totalPago" value="<?php echo remove_junk($total); ?>" readonly></td>
                      </tr>
                      <tr>
                         <td>Fecha</td>
                         <td><input type="date" class="form-control" name="fecha" value="<?php echo remove_junk($sale['date']);?>"></td>
                      </tr>
                      <tr>
                         <td>Comentario</td>
                         <td><input type="text" class="form-control" name="comentario"></td>
                      </tr>
                   </tbody>
                </table>
             </div>
          </div>
          <div class="col-md-4">
             <div class="panel panel-default">
                <input type="hidden" value="<?php echo $precioOrig; ?>" name="precioOrig">
                <input type="hidden" value="<?php echo $totalOrig; ?>" name="totalOrig">
                <button type="submit" name="update_sale" class="btn btn-primary">Actualizar venta</button>
             </div>
          </div>
       </div>
   </form>
</body>
<?php include_once('../layouts/footer.php'); ?>
