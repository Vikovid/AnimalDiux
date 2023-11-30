<?php
   $page_title = 'Editar producto';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $product = find_by_id('products',(int)$_GET['id']);
   $foto = $product['foto'];
   $idProduct = $product['id'];
   $all_categories = find_all('categories');
   $all_proveedor = find_all('proveedor');
   $all_sucursal = find_all('sucursal');
   $user = current_user(); 
   $usuario = $user['id'];
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $hora_actual=date('H:i:s',time());

   if(!$product){
      $session->msg("d","Missing product id.");
      redirect('product.php');
   }

   if(isset($_POST['product'])){
      $req_fields = array('nom_producto',
                          'categoria',
                          'cantidad',
                          'precioCompra',
                          'precioVenta',
                          'precioLinea',
                          'Codigo',
                          'sucursal',
                          'proveedor',
                          'comentario');

      validate_fields($req_fields);

      if(empty($errors)){
         $p_name  = remove_junk($db->escape($_POST['nom_producto']));
         $p_codigo  = remove_junk($db->escape($_POST['Codigo']));
         $p_comentario  = remove_junk($db->escape($_POST['comentario']));
         $p_cat   = (int)$_POST['categoria'];
         $p_sucur   = (int)$_POST['sucursal'];
         $p_prov   = (int)$_POST['proveedor'];
         $p_qty   = remove_junk($db->escape($_POST['cantidad']));
         $p_buy   = remove_junk($db->escape($_POST['precioCompra']));
         $p_sale  = remove_junk($db->escape($_POST['precioVenta']));
         $p_linea  = remove_junk($db->escape($_POST['precioLinea']));
         $p_fecCad  = remove_junk($db->escape($_POST['fecha_caducidad']));
         $p_cantCaja  = remove_junk($db->escape($_POST['cantidadCaja']));
         $p_porcMay  = remove_junk($db->escape($_POST['porcMayoreo']));
         $p_ligaInfo = remove_junk($db->escape($_POST['ligaInfo']));
         $foto = "";
         $Id = "";

         if ($product['name'] != $p_name){
            $consId = buscaRegistroPorCampo('products','name',$p_name);
            $Id = $consId['id'];
         }

         if ($Id == ""){
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
                        $borrado = $db->query("UPDATE products SET foto = '' WHERE id = $idProduct");
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

               $resultado = actProducto($p_name,
                                        $p_qty,
                                        $p_buy,
                                        $p_sale,
                                        $p_cat,
                                        $p_codigo,
                                        $p_sucur,
                                        $p_prov,
                                        $p_fecCad,
                                        $p_cantCaja,
                                        $p_porcMay,
                                        $fecha_actual,
                                        $foto,
                                        $product['id'],
                                        $p_ligaInfo,
                                        $p_linea);

            }else{
               $resultado = actProducto($p_name,
                                        $p_qty,
                                        $p_buy,
                                        $p_sale,
                                        $p_cat,
                                        $p_codigo,
                                        $p_sucur,
                                        $p_prov,
                                        $p_fecCad,
                                        $p_cantCaja,
                                        $p_porcMay,
                                        $fecha_actual,
                                        '',
                                        $product['id'],
                                        $p_ligaInfo,
                                        $p_linea);
            }

            $inicial=remove_junk($product['quantity']);

            altaHistorico('2',
                          $product['id'],
                          $inicial,
                          $p_qty,
                          $p_comentario,
                          $p_sucur,
                          $usuario,
                          '',
                          $fecha_actual,
                          $hora_actual);

            if($resultado){
               $session->msg('s',"Producto ha sido actualizado. ");
               redirect('product.php', false);
            }else{
               $session->msg('d',' Lo siento, actualización falló.');
               redirect('edit_product.php?id='.$product['id'], false);
            }
         }else{
            $session->msg('d','Lo siento, Nombre de producto ya registrado.');      
            redirect('edit_product.php?id='.$product['id'], false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('edit_product.php?id='.$product['id'], false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

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
            <span>Editar producto</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-8">
         <form name="form1" method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>" enctype="multipart/form-data">
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="nom_producto" value="<?php echo utf8_encode($product['name']);?>" oninput="mayusculas(event)">
               </div>
            </div>
            <div class="form-group">
               <div class="row">
                  <div class="col-md-6">
                     <select class="form-control" name="categoria">
                        <option value="">Selecciona una categoría</option>
                        <?php  foreach ($all_categories as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                        <?php echo remove_junk($cat['name']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-md-6">
                     <select class="form-control" name="proveedor">
                        <option value=""> Sin proveedor</option>
                        <?php  foreach ($all_proveedor as $proveedor): ?>
                        <option value="<?php echo (int)$proveedor['idProveedor'];?>" <?php if($product['idProveedor'] === $proveedor['idProveedor']): echo "selected"; endif; ?> >
                        <?php echo $proveedor['nom_proveedor'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <div class="row">
                  <div class="col-md-6">
                     <select class="form-control" name="sucursal">
                        <option value=""> Sin sucursal</option>
                        <?php  foreach ($all_sucursal as $sucursal): ?>
                        <option value="<?php echo (int)$sucursal['idSucursal'];?>" <?php if($product['idSucursal'] === $sucursal['idSucursal']): echo "selected"; endif; ?> >
                        <?php echo $sucursal['nom_sucursal'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  <div class="col-md-6">     
                     <label class="col-sm-6 col-form-label">Fecha de caducidad:</label>
                     <div class="col-sm-3">
                        <input type="date" name="fecha_caducidad" value="<?php echo remove_junk($product['fecha_caducidad']);?>">
                     </div>
                  </div>
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
                           <input type="number" step="0.01" class="form-control" name="cantidad" value="<?php echo remove_junk($product['quantity']) ?>">
                        </div>
                     </div>
                  </div>
               <div class="col-md-4">
               <div class="form-group">
                  <label for="qty">Precio de compra</label>
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-usd"></i>
                        </span>
                        <input type="number" step="0.01" class="form-control" name="precioCompra" value="<?php echo remove_junk($product['buy_price']);?>">
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label for="qty">Precio de venta</label>
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-usd"></i>
                        </span>
                        <input type="number"step="0.01" class="form-control" name="precioVenta" value="<?php echo remove_junk($product['sale_price']);?>">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="form-group">
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
                           <i class="glyphicon glyphicon-shopping-cart"></i>
                        </span>
                        <input type="number" step="0.000001" class="form-control" name="porcMayoreo" value="<?php echo remove_junk($product['porcentajeMayoreo']) ?>">
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <label for="qty">Código</label>
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-barcode"></i>
                     </span>
                     <input type="text" class="form-control" name="Codigo" value="<?php echo remove_junk($product['Codigo']);?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="row">
               <div class="col-md-4">
                  <label for="qty">Precio en línea</label>
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="precioLinea" value="<?php echo remove_junk($product['precio_linea'])?>">
                  </div>
               </div>
               <div class="col-md-8">
                  <label for="qty">Liga de descripción:</label>
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="text" name="ligaInfo" class="form-control" value="<?php echo remove_junk($product['ligaInfo'])?>"> 
                  </div>      
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
         <button type="submit" name="product" class="btn btn-danger">Actualizar</button>
         </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
