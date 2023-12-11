<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar stock del producto';
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $product = find_by_id('products',(int)$_GET['id']);
   $all_categories = find_all('categories');

   $user = current_user(); 
   $usuario = $user['id'];

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $hora_actual=date('H:i:s',time());

   if(!$product){
      $session->msg("d","Missing product id.");
      redirect('simple_product.php');
   }

   $idCatAux = isset($_POST['categoria']) ? $_POST['categoria']:$product['categorie_id'];

  if ($idCatAux != "")
     $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCatAux);
  else
     $subcategorias = array();

   if(isset($_POST['product'])){
      $req_fields = array('comentario','categoria','codigo');
      validate_fields($req_fields);

      if(empty($errors)){
         $p_name  = remove_junk($db->escape($_POST['product-title']));
         $p_comentario  = remove_junk($db->escape($_POST['comentario']));
         $p_sucur   = (int)$_POST['product_sucursal'];
         $p_qty   = remove_junk($db->escape($_POST['cantidad']));
         $p_stock = remove_junk($db->escape($_POST['stock']));
         $p_categoria = remove_junk($db->escape($_POST['categoria']));
         $p_precCompra = remove_junk($db->escape($_POST['precioCompra']));
         $p_precVenta = remove_junk($db->escape($_POST['precioVenta']));
         $p_cantCaja = remove_junk($db->escape($_POST['cantidadCaja']));
         $p_precioCaja = remove_junk($db->escape($_POST['precioCaja']));
         $p_subcat  = remove_junk($db->escape($_POST['subcats']));
         $p_codigo  = remove_junk($db->escape($_POST['codigo']));

         if ($p_qty == "")
       	    $p_qty = 0;

         $nuevoStock = $p_qty + $p_stock;

         $resultado = actDatosProducto($p_name,$nuevoStock,$fecha_actual,$p_categoria,$p_precCompra,$p_precVenta,$p_cantCaja,$p_precioCaja,$product['id'],$p_subcat,$p_codigo);
       
         $inicial=remove_junk($product['quantity']);

         altaHistorico('2',$product['id'],$inicial,$nuevoStock,$p_comentario,$p_sucur,$usuario,'',$fecha_actual,$hora_actual);

         if($resultado){
            $session->msg('s',"Producto ha sido actualizado. ");
            redirect('simple_product.php', false);
         }else{
            $session->msg('d',' Lo siento, falló la actualización.');
            redirect('edit_verStockProduct.php?id='.$product['id'], false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('edit_verStockProduct.php?id='.$product['id'], false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function recarga(){
  document.form1.action = "edit_verStockProduct.php?id=<?php echo (int)$product['id'] ?>";
  document.form1.submit();
}

function valorOrig(){
   document.form1.categoria.value = document.form1.idCatAux.value;
}

</script>
<body onload="valorOrig();">
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
 <div class="row">
    <div class="panel panel-default">
       <div class="panel-heading">
          <strong>
             <span class="glyphicon glyphicon-th"></span>
             <span>Editar stock del producto</span>
          </strong>
       </div>
       <div class="panel-body">
          <div class="col-md-7">
          <form name="form1" method="post" action="edit_verStockProduct.php?id=<?php echo (int)$product['id'] ?>">
             <div class="form-group">
                <div class="input-group">
                   <span class="input-group-addon">
                      <i class="glyphicon glyphicon-th-large"></i>
                   </span>
                   <input type="text" class="form-control" name="product-title" value="<?php echo isset($_POST['product-title']) ? $_POST['product-title']:$product['name'] ?>" readonly>
                </div>
             </div>
             <div class="form-group">
                <div class="row">
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Cantidad</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-shopping-cart"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control" name="cantidad" value="<?php echo isset($_POST['cantidad']) ? $_POST['cantidad']:'' ?>">
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Precio compra</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-usd"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control" name="precioCompra" value="<?php echo isset($_POST['precioCompra']) ? $_POST['precioCompra']:$product['buy_price'] ?>" >
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Precio venta</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-usd"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control" name="precioVenta" value="<?php echo isset($_POST['precioVenta']) ? $_POST['precioVenta']:$product['sale_price'] ?>" >
                         </div>
                      </div>
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Cantidad por caja</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-shopping-cart"></i>
                            </span>
                            <input type="number" class="form-control" name="cantidadCaja" value="<?php echo isset($_POST['cantidadCaja']) ? $_POST['cantidadCaja']:$product['cantidadCaja'] ?>">
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Precio por caja</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-usd"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control" name="precioCaja" value="<?php echo isset($_POST['precioCaja']) ? $_POST['precioCaja']:$product['precioCaja'] ?>">
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Código</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-usd"></i>
                            </span>
                            <input type="text" class="form-control" name="codigo" value="<?php echo isset($_POST['Codigo']) ? $_POST['Codigo']:$product['Codigo'] ?>">
                         </div>
                      </div>
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                      <label for="qty">Categoría</label>
                      <select class="form-control" name="categoria" onchange="recarga();">
                         <option value="">Selecciona una categoría</option>
                         <?php  foreach ($all_categories as $cat): ?>
                         <option value="<?php echo $cat['id'] ?>">
                         <?php echo $cat['name'] ?></option>
                         <?php endforeach; ?>
                      </select>
                   </div>
                   <div class="col-md-6">
                      <label for="qty">Subcategoría</label>
                      <select class="form-control" name="subcats">
                         <option value="">Selecciona una Subcategoría</option>
                         <?php  foreach ($subcategorias as $subcat): ?>
                         <option value="<?php echo (int)$subcat['idSubCategoria']; ?>" <?php if($product['idSubcategoria'] === $subcat['idSubCategoria'] && $product['categorie_id'] === $subcat['idCategoria']): echo "selected"; endif; ?> >
                         <?php echo remove_junk($subcat['nombre']); ?></option>
                         <?php endforeach; ?>
                      </select>
                   </div>
                 </div>
             </div>
             <div class="form-group">
                <div class="input-group">
                   <span class="input-group-addon">
                      <i class="glyphicon glyphicon-barcode"></i>
                   </span>
                   <input type="text" class="form-control" name="comentario" placeholder="comentario">
                </div>
             </div>
             <input type="hidden" name="stock" value="<?php echo remove_junk($product['quantity']);?>">
             <input type="hidden" name="product_sucursal" value="<?php echo remove_junk($product['idSucursal']);?>">
             <input type="hidden" class="form-control" name="idCatAux" value="<?php echo $idCatAux; ?>">
             <button type="submit" name="product" class="btn btn-danger">Actualizar</button>
          </form>
          </div>
       </div>
    </div>
 </div>
</body>
<?php include_once('../layouts/footer.php'); ?>
