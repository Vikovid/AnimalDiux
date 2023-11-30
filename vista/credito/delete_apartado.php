<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  $user = current_user(); 
  $idUsuario = $user['id'];
  $idSucursal = $user['idSucursal'];

  if(isset($_GET['id'])) {
     $id = $_GET['id'];

  $cuenta = buscaRegistroPorCampo('cuenta','id',$id);
  $id_Product = $cuenta['productId'];
  $cantidad = $cuenta['cantidad'];

  $producto = buscaRegistroPorCampo('products','id',$id_Product);
  $cantProd = $producto['quantity'];
  $nomProducto = $producto['name'];

  $cantTotal = $cantProd + $cantidad;

  actStockProducto($cantTotal,$fecha_actual,$id_Product,'');

  altaHistorico('2',$id_Product,$cantProd,$cantTotal,'Credito eliminado',$idSucursal,$idUsuario,'',$fecha_actual,$hora_actual);
     
  $result = delete_by_id('cuenta',$id);

  if(!$result){
     die("falló la eliminación.");
  }
  $session->msg("s","Producto eliminado correctamente.");
  redirect('apartados.php');
}
?>