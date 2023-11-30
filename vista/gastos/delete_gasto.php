<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $user = current_user(); 
  $usuario = $user['id'];
  $idSucursal = $user['idSucursal'];

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  $gastos = find_by_id('gastos',(int)$_GET['id']);
  $montoGasto = $gastos['total'];
  $tipoPago = $gastos['tipo_pago'];
  if(!$gastos){
     $session->msg("d","ID vacío");
     redirect('gastos.php');
  }

  $delete_id = delete_by_id('gastos',(int)$gastos['id']);
  
  if($delete_id){
     if ($tipoPago == "1"){
        $consMonto = buscaRegistroMaximo("caja","id");
        $montoActual=$consMonto['monto'];
        $idCaja = $consMonto['id'];

        $montoFinal = $montoActual + $montoGasto;

        $respCaja = actCaja($montoFinal,$fecha_actual,$idCaja);

       if($respCaja)
          altaHisEfectivo('14',$montoActual,$montoFinal,$idSucursal,$usuario,'',$fecha_actual,$hora_actual);
    }
    $session->msg("s","Gasto eliminado");
    redirect('gastos.php');
  }else{
    $session->msg("d","Falló la eliminación");
    redirect('gastos.php');
  }
?>
