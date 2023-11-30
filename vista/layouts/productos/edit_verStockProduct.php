<?php
  $page_title = 'Editar stock del producto';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  $product = find_by_id('products',(int)$_GET['id']);
  $foto = $product['foto'];
  
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
        $nuevaEspecificacion = remove_junk($db->escape($_POST['ligaInfo']));

        if ($p_qty == "")
           $p_qty = 0;

        $nuevoStock = $p_qty + $p_stock;

        if(is_uploaded_file($_FILES['producto']['tmp_name'])){
           $file_name = $_FILES['producto']['name'];

           if ($file_name != '' || $file_name != null) {
              $file_type = $_FILES['producto']['type'];
              list($type, $extension) = explode('/', $file_type);

              if ($extension == "gif" || $extension == "jpg" || 
                 $extension == "jpeg" || $extension == "png"){

                 $file_tmp_name = $_FILES['producto']['tmp_name'];

                 $fp = fopen($file_tmp_name, 'r+b');
                 $data = fread($fp, filesize($file_tmp_name));
                 fclose($fp);            

                 $foto = $db->escape($data);

                 if (empty($file_name) || empty($file_tmp_name)){
                    $session->msg('d','La ubicación del archivo no se encuenta disponible.');
                    redirect('edit_product.php?id='.$product['id'], false);
                 }

                 if ($product['foto'] != ''){
                    $borrado = actRegistroPorCampo('products','foto','','id',$product['id']);
                    if (!$borrado){
                       $session->msg('d','Error al borrar el archivo original.');
                       redirect('edit_product.php?id='.$product['id'], false);
                    }
                 }
              }else{
                 $session->msg('d','Formato de archivo no válido.');
                 redirect('edit_product.php?id='.$product['id'], false);
              }
           }
           $actProducto =  actStockProducto($nuevoStock,
                                            $fecha_actual,
                                            $product['id'],
                                            $foto,
                                            $nuevaEspecificacion);
        }else{
           $actProducto = actStockProducto($nuevoStock,
                                           $fecha_actual,
                                           $product['id'],
                                           '',
                                           $nuevaEspecificacion);
        }
       
        $inicial=remove_junk($product['quantity']);

        altaHistorico('2',
                      $product['id'],
                      $inicial,
                      $nuevoStock,
                      $p_comentario,
                      $p_sucur,
                      $usuario,
                      '',
                      $fecha_actual,
                      $hora_actual);

        if($actProducto){
           $session->msg('s',"Producto ha sido actualizado. ");
           redirect('../consultas/simple_product.php', false);
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
         <form name="form1" method="post" action="edit_verStockProduct.php?id=<?php echo (int)$product['id'] ?>" enctype="multipart/form-data">
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
               </div>
            </div>
            <div class="row">
               <div class="col-md-4">
                  <div class="panel profile">
                     <?php if ($foto != ""){ 
                        echo "<img src='data:image/jpg; base64,".base64_encode($foto)."' width='150' height='200'>";
                     } ?>
                  </div>
               </div>
               <div class="col-md-8">
                  <div class="form-group">
                     <input type="file" name="producto" multiple="multiple" class="btn btn-primary btn-file"/>
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
            <div class="form-group">
              <div class="input-group">
                <label>Enlace de especificaciones:</label>
                <input type="text" name="ligaInfo" class="form-control" value="<?php echo remove_junk($product['ligaInfo']);?>">
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
