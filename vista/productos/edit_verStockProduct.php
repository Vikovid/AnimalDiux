<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar stock del producto';
   // Checkin What level user has permission to view this page
   page_require_level(2);

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

   if(isset($_POST['product'])){
      $req_fields = array('comentario');
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
         $p_porcMay = remove_junk($db->escape($_POST['porcMay']));

         if ($p_qty == "")
       	    $p_qty = 0;

         $nuevoStock = $p_qty + $p_stock;

         $resultado = actDatosProducto($p_name,$nuevoStock,$fecha_actual,$p_categoria,$p_precCompra,$p_precVenta,$p_cantCaja,$p_porcMay,$product['id']);
       
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
          <form method="post" action="edit_verStockProduct.php?id=<?php echo (int)$product['id'] ?>">
             <div class="form-group">
                <div class="input-group">
                   <span class="input-group-addon">
                      <i class="glyphicon glyphicon-th-large"></i>
                   </span>
                   <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']);?>" readonly>
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
                            <input type="number" step="0.01" class="form-control" name="cantidad">
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Precio compra</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-shopping-cart"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control" name="precioCompra" value="<?php echo remove_junk($product['buy_price']);?>" >
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Precio venta</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-shopping-cart"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control" name="precioVenta" value="<?php echo remove_junk($product['sale_price']);?>" >
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
                            <input type="number" class="form-control" name="cantidadCaja" value="<?php echo remove_junk($product['cantidadCaja']) ?>">
                         </div>
                      </div>
                   </div>
                   <div class="col-md-4">
                      <div class="form-group">
                         <label for="qty">Porcentaje mayoreo</label>
                         <div class="input-group">
                            <span class="input-group-addon">
                               <i class="glyphicon glyphicon-usd"></i>
                            </span>
                            <input type="number" step="0.000001" class="form-control" name="porcMay" value="<?php echo remove_junk($product['porcentajeMayoreo']) ?>">
                         </div>
                      </div>
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                      <label for="qty">Categoría</label>
                      <select class="form-control" name="categoria">
                         <?php  foreach ($all_categories as $cat): ?>
                         <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                         <?php echo remove_junk($cat['name']); ?></option>
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
             <button type="submit" name="product" class="btn btn-danger">Actualizar</button>
          </form>
          </div>
       </div>
    </div>
 </div>
<?php include_once('../layouts/footer.php'); ?>
