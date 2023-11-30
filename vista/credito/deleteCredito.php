<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $user = current_user(); 
  $usuario = $user['id'];
  $idSucursal = $user['idSucursal'];
  $creditos = array();
  $idCreditoAnt = "";
  $contEstetica = 0;

  $idTicket = (int)$_GET['idTicket'];

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  $consCredito = buscaRegistroPorCampo('histcredito','id_ticket',$idTicket);

  $idCliente = $consCredito['idCliente'];

  $resProdsCreds = buscaRegsPorCampo('sales','id_ticket',$idTicket);

  while($regProdCred=mysqli_fetch_array($resProdsCreds)){

    $productoId = $regProdCred['product_id'];
    $idCredito = $regProdCred['idCredito'];

    $consCuenta = buscaCredito($productoId,$idCredito,$idCliente);

    $abono = $regProdCred['price'] + $consCuenta['total'];

    actCuenta($abono,'0',$consCuenta['id']);

    $datosEstetica = buscaProdHistEstetica($productoId,$idTicket);

    if ($datosEstetica != null){
       $ventaLV = $datosEstetica['ventaLV'];
       $ventaSD = $datosEstetica['ventaSD'];
       $comisionLV = $datosEstetica['comisionLV'];
       $comisionSD = $datosEstetica['comisionSD'];
       $idCliente = $datosEstetica['cliente'];
       $vendedor = $datosEstetica['vendedor'];

       altaMovsEstetica($productoId,$idSucursal,$usuario,$vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$idCliente,$idTicket,'Abono eliminado',$fecha_actual,$hora_actual);

       $contEstetica++;
    }

    if ($idCredito != $idCreditoAnt){
       $creditos[] = $idCredito;
       $idCreditoAnt = $idCredito; 
    }
  }

  $consPago = buscaRegistroPorCampo('pagos','id_ticket',$idTicket);

  $tipoPago = $consPago['id_tipo'];
  $cantidad = $consPago['cantidad'];

  if ($tipoPago == "1"){
     $consMonto = buscaRegistroMaximo("caja","id");
     $montoActual = $consMonto['monto'];
     $idCaja = $consMonto['id'];

     $montoFinal = $montoActual - $cantidad;

     $actCaja = actCaja($montoFinal,$fecha_actual,$idCaja);

     if($actCaja){
        registrarEfectivo('15',$montoActual,$montoFinal,$idSucursal,$usuario,'',$fecha_actual,$hora_actual);
     }
  }
  
  borraRegistroPorCampo('folio','dato',$idTicket);

  borraRegistrosPorCampo('histcredito','id_ticket',$idTicket);  

  foreach ($creditos as $credito):

     $respTickets = buscaRegsPorCampo('sales','idCredito',$credito);

     foreach ($respTickets as $ticket):
        
        actRegistroPorCampo('histcredito','pagado','0','id_ticket',$ticket['id_ticket']);

     endforeach;

  endforeach;

  borraRegistrosPorCampo('pagos','id_ticket',$idTicket);

  borraRegistrosPorCampo('tickets','id_ticket',$idTicket);

  if($contEstetica > 0)
    borraRegistrosPorCampo('histestetica','id_ticket',$idTicket);

  $delSales = borraRegistrosPorCampo('sales','id_ticket',$idTicket);

  if($delSales){
     $session->msg("s","Abono eliminado correctamente.");
     redirect('../ventas/sales.php');
  }else{
     $session->msg("d","Falló la eliminación");
     redirect('../ventas/sales.php');
  }
?>
