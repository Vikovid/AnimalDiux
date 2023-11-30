<?php
require_once('../../modelo/load.php');

ini_set('date.timezone','America/Mexico_City');
$fecha_actual=date('Y-m-d',time());

$id_producto= isset($_POST['idProd']) ? $_POST['idProd']:'';
$precio= isset($_POST['precio']) ? $_POST['precio']:'';
$usuario= isset($_POST['user']) ? $_POST['user']:'';
$idSucursal= isset($_POST['idSuc']) ? $_POST['idSuc']:'';

$respSuma = sumaCampo('qty','temporal','product_id',$id_producto);

if ($respSuma != null)
   $sumaTemp = $respSuma['total'] + 1;
else
   $sumaTemp = 1;

$producto = find_by_id("products",$id_producto);
$cantProd = $producto['quantity'];
$nomProducto = $producto['name'];

if ($sumaTemp <= $cantProd){

   altaTemporal($id_producto,'1',$precio,$fecha_actual,$usuario,$idSucursal);

   echo '<script> window.location="add_sale.php";</script>';	
}else{
   echo "<script> alert('Está solicitando más ' + '".$nomProducto."' + ' del disponible');</script>";
   echo '<script> window.location="add_sale.php";</script>';   
}
?>