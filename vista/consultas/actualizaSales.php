<?php
$db_host="localhost";
$db_nombre="animaldiux";
$db_usuario="root";
$db_contra="";

$conexion=mysqli_connect($db_host,$db_usuario,$db_contra);

if (mysqli_connect_errno()) {
   echo "falló la conexión";
   exit();
}

mysqli_select_db($conexion,$db_nombre) or die("Error de conexión con la BD");
mysqli_set_charset($conexion,"utf8"); 

$consMonto = "SELECT id, categorie_id, idSubcategoria FROM products";   
$resultado = mysqli_query($conexion,$consMonto);
   
while($consulta=mysqli_fetch_array($resultado)){
	$product_id = $consulta['id'];
	$idCategoria = $consulta['categorie_id'];
	$idSubCategoria = $consulta['idSubcategoria'];

	$actSale = "UPDATE sales 
					SET idCategoria = '{$idCategoria}', 
					idSubcategoria = '{$idSubCategoria}' 

					WHERE product_id = '{$product_id}' AND
					idCategoria = 0 AND
					idSubcategoria = 0";

	$respActSale = mysqli_query($conexion, $actSale);
}

echo '<script> window.location="monthly_sales_categoria.php";</script>';   
?>