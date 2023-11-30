<?php
require_once('../../modelo/load.php');
ini_set('date.timezone','America/Mexico_City');
$fecha_actual=date('Y-m-d',time());
$hora_actual=date('H:i:s',time());

$cliente = "";

$usuario= isset($_POST['user']) ? $_POST['user']:'';
$idSucursal= isset($_POST['idSuc']) ? $_POST['idSuc']:'';
$idCliente = isset($_POST['idCliente']) ? $_POST['idCliente']:'';
$idUsuario = isset($_POST['idUsu']) ? $_POST['idUsu']:'';


$productos = buscaProductosVentas($usuario,$idSucursal);

$consCliente = buscaRegistroPorCampo('cliente','idcredencial',$idCliente);

if ($consCliente != null)
   $cliente = $consCliente['nom_cliente'];

if ($cliente != ""){
   
   $consIdCredito = buscaRegistroMaximo('cuenta','idCredito');
   $idCredito = $consIdCredito['idCredito'] + 1;

   foreach ($productos as $producto):
      $idProducto = $producto['product_id'];
      $cantTemp = $producto['qty'];
      $cantProd = $producto['quantity'];
      $total = $producto['precio'];

      $resta = $cantProd - $cantTemp;

      actProdIdSucursal($resta,$fecha_actual,$idProducto,$idSucursal);

      altaCuenta($cliente,$total,$idCliente,$idProducto,$cantTemp,$total,$idCredito,$fecha_actual,$hora_actual);

      altaHistorico('3',$idProducto,$cantProd,$resta,'Credito',$idSucursal,$idUsuario,'',$fecha_actual,$hora_actual);
   endforeach;

   borraRegistroPorCampo('temporal','usuario',$usuario);
   echo '<script> window.location="apartados.php";</script>';
}else{
   echo "<script> alert('El id proporcionado no existe.');</script>";
   echo '<script> window.location="../ventas/add_sale.php";</script>';
}
?>
