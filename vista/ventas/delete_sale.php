<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $user = current_user(); 
  $idUsuario = $user['id'];
  $idSucursal = $user['idSucursal'];
  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $venta = find_by_id('sales',$id);
  
    $id_Ticket = $venta['id_ticket'];
    $productId = $venta['product_id'];
    $cantidad = $venta['qty'];
    $precio = $venta['price'];

    $pagoEfectivo = "";
    $pagoTrans = "";
    $pagoDeposito = "";
    $pagoTarjeta = "";
    $idPagoEfec = "";
    $idPagotrans = "";
    $idPagoDep = "";
    $idPagoTar = "";
    $cantFinal = -1;
  
    $producto = find_by_id('products',$productId);
    $cantProd = $producto['quantity'];
    $nomProducto = $producto['name'];
    $categoria = $producto['categorie_id'];

    $cantTotal = $cantidad + $cantProd;

    actStockProducto($cantTotal,$fecha_actual,$productId,'');

    $consPagos = tipoPagoTTP($id_Ticket,'1');
    if ($consPagos != null){
       $pagoEfectivo = $consPagos['cantidad'];
       $idPagoEfec = $consPagos['id_pago'];
    }

    $consPagos = tipoPagoTTP($id_Ticket,'2');
    if ($consPagos != null){
       $pagoTrans = $consPagos['cantidad'];
       $idPagotrans = $consPagos['id_pago'];
    }  

    $consPagos = $consPagos = tipoPagoTTP($id_Ticket,'3');
    if ($consPagos != null){
       $pagoDeposito = $consPagos['cantidad'];
       $idPagoDep = $consPagos['id_pago'];
    }

    $consPagos = $consPagos = tipoPagoTTP($id_Ticket,'4');
    if ($consPagos != null){
       $pagoTarjeta = $consPagos['cantidad'];
       $idPagoTar = $consPagos['id_pago'];
    }

    if ($pagoEfectivo > 0){
       $cantFinal = $pagoEfectivo - $precio;
       if ($cantFinal > 0)
          actRegistroPorCampo('pagos','cantidad',$cantFinal,'id_pago',$idPagoEfec);
       else
          borraRegistroPorCampo('pagos','id_pago',$idPagoEfec);
    }

    if ($pagoTrans > 0 && $cantFinal < 0){
       $cantFinal = $pagoTrans - $precio;
       if ($cantFinal > 0)
          actRegistroPorCampo('pagos','cantidad',$cantFinal,'id_pago',$idPagotrans);
       else
          borraRegistroPorCampo('pagos','id_pago',$idPagotrans);
    }

    if ($pagoDeposito > 0 && $cantFinal < 0){
       $cantFinal = $pagoDeposito - $precio;
       if ($cantFinal > 0)
          actRegistroPorCampo('pagos','cantidad',$cantFinal,'id_pago',$idPagoDep);
       else
          borraRegistroPorCampo('pagos','id_pago',$idPagoDep);
    }

    if ($pagoTarjeta > 0 && $cantFinal < 0){
       $cantFinal = $pagoTarjeta - $precio;
       if ($cantFinal > 0)
          actRegistroPorCampo('pagos','cantidad',$cantFinal,'id_pago',$idPagoTar);
       else
          borraRegistroPorCampo('pagos','id_pago',$idPagoTar);
    }

    if ($pagoEfectivo > 0){
       $consMonto = buscaRegistroMaximo("caja","id");
       $montoActual = $consMonto['monto'];
       $idCaja = $consMonto['id'];

       $montoFinal = $montoActual - $precio;

       actCaja($montoFinal,$fecha_actual,$idCaja);

       registrarEfectivo('8',$montoActual,$montoFinal,$idSucursal,$idUsuario,'',$fecha_actual,$hora_actual);
    }

    altaHistorico('8',$productId,$cantProd,$cantTotal,'Actualización de Stock',$idSucursal,$idUsuario,'',$fecha_actual,$hora_actual);

    borraRegistroPorCampo('tickets','idVenta',$id);

    if ($categoria == "26"){
       $datosEstetica = buscaProdHistEstetica($productId,$id_Ticket);
       $idHistEstetica = $datosEstetica['idHistEstetica'];
       $ventaLV = $datosEstetica['ventaLV'];
       $ventaSD = $datosEstetica['ventaSD'];
       $comisionLV = $datosEstetica['comisionLV'];
       $comisionSD = $datosEstetica['comisionSD'];
       $idCliente = $datosEstetica['cliente'];
       $vendedor = $datosEstetica['vendedor'];

       altaMovsEstetica($productId,$idSucursal,$idUsuario,$vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$idCliente,$id_Ticket,'Venta eliminada',$fecha_actual,$hora_actual);

       borraRegistroPorCampo('histestetica','idHistEstetica',$idHistEstetica);
    }

    $resDelSales = borraRegistroPorCampo('sales','id',$id);

    if(!$resDelSales) {
       die("falló la eliminación.");
    }
    $session->msg("s","Venta eliminada correctamente.");
    redirect('sales.php');
  }
?>