<?php
   $page_title = 'Lista de productos';
   require_once('../../modelo/load.php');
  
   page_require_level(1);

   $categorias =     find_all('categories');
   $subcategorias =  array();
   $regCat =         '';
   $regSubCat =      '';

   if (isset($_POST['categoria'])){
      $regCat =  remove_junk($db->escape($_POST['categoria']));
      if($regCat != '')
         $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$regCat);
   }
   if(isset($_POST['subcategoria']))
      $regSubCat = remove_junk($db->escape($_POST['subcategoria']));
?>
<?php include_once('../layouts/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Lista de productos</title>
</head>
<script language="Javascript">
   function producto(){
      document.form1.action = "product.php";
      document.getElementsByName("buscar")[0].value = "1";
      document.form1.submit();
   }
   function recarga(){
      document.form1.action = "product.php";
      document.form1.submit();
   }
   function productospdf(){
      document.form1.action = "../pdf/productspdf.php";
      document.form1.submit();
   }

   function excel(){
      document.form1.action = "../excel/excel.php";
      document.form1.submit();
   }

   function foco(){
      document.form1.Codigo.focus();
   }
   function mayusculas(e) {
      var ss = e.target.selectionStart;
      var se = e.target.selectionEnd;
      e.target.value = e.target.value.toUpperCase();
      e.target.selectionStart = ss;
      e.target.selectionEnd = se;
   }
</script>
<body onload="foco();">
<form name="form1" method="post" action="product.php">
<?php
   $codigo = isset($_POST['Codigo']) ? $_POST['Codigo']:'';

   if ($codigo != "" && $regCat != "")
      $totales = totalesProductosCodCat($codigo,$regCat);
   elseif ($regCat != "") 
      $totales = totalesProductosCat($regCat);
   elseif ($codigo != "") 
      $totales = totalesProductosCod($codigo);
   else
      $totales = totalesProductos();

   $totalPrecio =    $totales['totalPrecio'];
   $cantidadTotal =  $totales['cantidadTotal'];
   $totalVenta =     $totales['totalVenta'];

   echo("<span>Total de Inversion: </span>");
   echo number_format($totalPrecio,2);
   echo("<br>");
   echo("<span>Total de Producto: </span>");
   echo number_format($cantidadTotal,2);
   echo("<br>");
   echo("<span>Total de Venta: </span>");
   echo number_format($totalVenta,2);

   if (isset($_POST['buscar']) && $_POST['buscar'] == "1") {

      $_SESSION['codigo'] =    $codigo;
      $_SESSION['regCat'] =    $regCat;
      $_SESSION['regSubCat'] = $regSubCat;

      if ($codigo != '' || $regCat != '' || $regSubCat != ''){
         if (is_numeric($codigo))
            $products = join_product_table1($codigo,$regCat,$regSubCat);
         else
            $products = join_product_table2($codigo,$regCat,$regSubCat);
      } else
         $products = join_product_table();

   } elseif (isset($_POST['buscar']) && 
             $_POST['buscar'] == 0 &&
             isset($_SESSION['codigo']) &&
             isset($_SESSION['regCat'] ) &&
             isset($_SESSION['regSubCat']) ) {
      
      if ($_SESSION['codigo'] != '' || $_SESSION['regCat'] != '' || $_SESSION['regSubCat'] != ''){
         if (is_numeric($_SESSION['codigo']))
            $products = join_product_table1($_SESSION['codigo'],$_SESSION['regCat'],$_SESSION['regSubCat']);
         else
            $products = join_product_table2($_SESSION['codigo'],$_SESSION['regCat'],$_SESSION['regSubCat']);
      } else
         $products = join_product_table();

   } else{
      $_SESSION['codigo'] =    $codigo;
      $_SESSION['regCat'] =    $regCat;
      $_SESSION['regSubCat'] = $regSubCat;
      
      $products = join_product_table();
   }
?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="pull-right">
               <div class="form-group">
                  <div class="col-md-3">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-barcode"></i>
                        </span>
                        <input type="text" class="form-control" name="Codigo" long="21" value="<?php echo isset($_POST['Codigo']) && $_POST['buscar'] == 0 ? $_POST['Codigo'] : '' ?>" oninput="mayusculas(event)" onkeydown="if (event.keyCode === 13) producto();">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control" name="categoria" onchange="recarga();">
                        <option value="">Selecciona Categoria</option>
                        <?php  foreach ($categorias as $categoria): ?>
                           <?php if (isset($_POST['categoria']) && ($_POST['categoria'] == $categoria['id']) && $_POST['buscar'] == 0) { ?>
                              <option value="<?php echo $categoria['id'] ?>" selected> <?php echo utf8_encode($categoria['name']) ?> </option>   
                           <?php } else { ?>

                           <?php } ?>
                              <option value="<?php echo $categoria['id'] ?>"> <?php echo utf8_encode($categoria['name']) ?> </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control" name="subcategoria">
                        <option value="">Selecciona SubCategoria</option>
                        <?php foreach ($subcategorias as $subcategoria): ?>
                           <option value="<?php echo $subcategoria['idSubCategoria'] ?>"> <?php echo $subcategoria['nombre'] ?> </option>
                        <?php endforeach ?>
                     </select>
                  </div>  
                  <a href="#" onclick="producto();" class="btn btn-primary">Buscar</a> 
                  <a href="add_product.php" class="btn btn-primary">Agregar Producto</a>  
                  <a href="#" onclick="productospdf();" class="btn btn-xs btn-danger">PDF</a>
                  <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>            
                  <input type="hidden" name="buscar" value="0">
               </div>   
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th class="text-center" style="width: 3%;">#</th>
                  <th> Imagen</th>
                  <th> Descripción </th>
                  <th class="text-center" style="width: 10%;"> Categoría </th>
                  <th class="text-center" style="width: 10%;"> Subcategoría </th>
                  <th class="text-center" style="width: 10%;"> Stock </th>
                  <th class="text-center" style="width: 10%;"> Precio de compra </th>
                  <th class="text-center" style="width: 10%;"> Precio de venta </th>
                  <th class="text-center" style="width: 10%;"> Agregado </th>
                  <th class="text-center" style="width: 10%;"> Sucursal </th>
                  <th class="text-center" style="width: 10%;"> Acciones </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($products as $product):?>
               <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td class="text-center">
                     <?php 
                        if ($product['foto'] != "")
                           echo "<img src='data:image/jpg; base64,".base64_encode($product['foto'])."' width='45' height='50'>";
                     ?> 
                  </td>
                  <td> <?php echo utf8_encode($product['name']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['nombre']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['buy_price']); ?></td> 
                  <td class="text-center"> <?php echo remove_junk($product['sale_price']); ?></td>
                  <td class="text-center"><?php echo date("d-m-Y", strtotime ($product['fechaRegistro'])); ?></td>
                  <td class="text-center"> <?php echo remove_junk($product['sucursal']); ?></td>
                  <td class="text-center">
                     <div class="btn-group">
                        <a href="edit_stockProduct.php?id=<?php echo (int)$product['id'];?>" class="btn btn-success btn-xs" title="Stock" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                        <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-trash"></span>
                        </a>
                     </div>
                  </td>
               </tr>
               <?php endforeach?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>