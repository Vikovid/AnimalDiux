<?php
  $page_title = 'Lista de productos';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  //Lammar a categorias
  $all_categorias = find_all('categories');

  $p_scu = "";

  if (isset($_POST['categoria'])){  
     $p_scu =  remove_junk($db->escape($_POST['categoria']));
  }
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
   //echo $total_ventas_daily1;
   $codigo= isset($_POST['Codigo']) ? $_POST['Codigo']:'';

   if ($codigo!="" & $p_scu!="") {
      $totales = totalesProductosCodCat($codigo,$p_scu);
   }elseif ($p_scu!="") {
      //Suma del Select 
      $totales = totalesProductosCat($p_scu);
   }elseif ($codigo!="") {
      //Suma del input
      $totales = totalesProductosCod($codigo);
   }else{
      //Suma de todo
      $totales = totalesProductos();
   }

   $totalPrecio = $totales['cantidadTotal'];
   $cantidadTotal = $totales['totalPrecio'];
   $totalVenta = $totales['totalVenta'];

   echo("<span>Total de Inversion: </span>");
   echo number_format($totalPrecio,2);
   echo("<br>");
   echo("<span>Total de Producto: </span>");
   echo number_format($cantidadTotal,2);
   echo("<br>");
   echo("<span>Total de Venta: </span>");
   echo number_format($totalVenta,2);

   if ($p_scu!=""){
      if ($codigo!="") {
         if (is_numeric($codigo)){
            $products = join_product_table1($codigo,$p_scu);
         }else{
            $products = join_product_table2($codigo,$p_scu);
         }
      }else{
         $products = join_select_categories($p_scu);
      }
   }else{
      if ($codigo!="") {
         if (is_numeric($codigo)){
            $products = join_product_table1a($codigo);
         }else{
            $products = join_product_table2a($codigo);
         }
      }else{
         $products = join_product_table();
      }

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
                <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-barcode"></i>
                  </span>
                  <input type="text" class="form-control" name="Codigo" long="21" oninput="mayusculas(event)">
               </div>
              </div>
              <div class="col-md-3">
                    <select class="form-control" name="categoria">
                      <option value="">Selecciona categoria</option>
                    <?php  foreach ($all_categorias as $id): ?>
                      <option value="<?php echo $id['id'] ?>">
                        <?php echo $id['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>  
              <a href="#" onclick="producto();" class="btn btn-primary">Buscar</a> 
              <a href="add_product.php" class="btn btn-primary">Agregar Producto</a>  
              <a href="#" onclick="productospdf();" class="btn btn-xs btn-danger">PDF</a>
              <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>            
              
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
                <?php if ($product['foto'] != ""){ 
                  echo "<img src='data:image/jpg; base64,".base64_encode($product['foto'])."' width='45' height='50'>";
                } ?> 
                </td>
                <td> <?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
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
             <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
