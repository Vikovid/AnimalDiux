<?php
   $page_title = 'Agregar producto';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);
   $all_categories = find_all('categories');
   $all_provedor = find_all('proveedor');
   $all_sucursal = find_all('sucursal');
   $user = current_user(); 
   $usuario = $user['id'];
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $hora_actual=date('H:i:s',time());  

   if(isset($_POST['add_product'])){
      $req_fields = array('nom_producto',
                          'categoria',
                          'cantidad',
                          'precioCompra',
                          'precioVenta',
                          'codigo',
                          'proveedor',
                          'sucursal');
      validate_fields($req_fields);
      if(empty($errors)){
         $p_name  = remove_junk($db->escape($_POST['nom_producto']));
         $p_cat   = remove_junk($db->escape($_POST['categoria']));
         $p_qty   = remove_junk($db->escape($_POST['cantidad']));
         $p_scu   = remove_junk($db->escape($_POST['sucursal']));
         $p_buy   = remove_junk($db->escape($_POST['precioCompra']));
         $p_sale  = remove_junk($db->escape($_POST['precioVenta']));
         $p_codigo  = remove_junk($db->escape($_POST['codigo']));
         $p_proveedor = remove_junk($db->escape($_POST['proveedor']));
         $p_fecCad = remove_junk($db->escape($_POST['fecha_caducidad']));
         $p_cantCaja = remove_junk($db->escape($_POST['cantidadCaja']));
         $p_porcMay = remove_junk($db->escape($_POST['porcMayoreo']));
         $foto = "";
         $p_linea = remove_junk($db->escape($_POST['precioLinea']));
         $p_ligaInfo = remove_junk($db->escape($_POST['ligaInfo']));

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
                     redirect('add_product.php', false);
                  }
               }else{
                  $session->msg('d','Formato de archivo no válido.');
                  redirect('add_product.php', false);
               }
            }
         } 

         $resultado = altaProducto($p_name,
                                   $p_qty,
                                   $p_buy,
                                   $p_sale,
                                   $p_cat,
                                   $foto,
                                   $fecha_actual,
                                   $p_codigo,
                                   $p_proveedor,
                                   $p_scu,
                                   $p_fecCad,
                                   $p_cantCaja,
                                   $p_porcMay,
                                   $fecha_actual,
                                   $p_ligaInfo,
                                   $p_linea);

         if($resultado){
            $product = buscaRegistroMaximo('products','id');
            $id = $product['id'];
       
            altaHistorico('1',
                          $id,
                          '0',
                          $p_qty,
                          'Producto Nuevo',
                          $p_scu,
                          $usuario,
                          '',
                          $fecha_actual,
                          $hora_actual);

            $session->msg('s',"Producto agregado exitosamente. ");
            redirect('product.php', false);
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('add_product.php', false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('add_product.php',false);
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
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar producto</span>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form name="form" method="post" action="add_product.php" enctype="multipart/form-data">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nom_producto" placeholder="Descripción" oninput="mayusculas(event)">
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-6">
                        <select class="form-control" name="categoria">
                           <option value="">Selecciona una categoría</option>
                           <?php  foreach ($all_categories as $cat): ?>
                           <option value="<?php echo (int)$cat['id'] ?>">
                              <?php echo $cat['name'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
					      <div class="col-md-6">
                        <select class="form-control" name="proveedor">
                           <option value="">Selecciona un proveedor</option>
                           <?php  foreach ($all_provedor as $id): ?>
                           <option value="<?php echo (int)$id['idProveedor'] ?>">
                              <?php echo $id['nom_proveedor'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-6">
                        <select class="form-control" name="sucursal">
                           <option value="">Selecciona una sucursal</option>
                           <?php  foreach ($all_sucursal as $id): ?>
                           <option value="<?php echo (int)$id['idSucursal'] ?>">
                              <?php echo $id['nom_sucursal'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-6">     
                        <label class="col-sm-5 col-form-label">Fecha de caducidad:</label>
                           <div class="col-sm-3">
                              <input type="date" name="fecha_caducidad" min="<?php echo $fecha_actual ?>">
                           </div>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-shopping-cart"></i>
                           </span>
                           <input type="number" step="0.01" class="form-control" name="cantidad" placeholder="Cantidad">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-usd"></i>
                           </span>
                           <input type="number" step="0.01" class="form-control" name="precioCompra" placeholder="Precio de compra">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-usd"></i>
                           </span>
                           <input type="number" step="0.01" class="form-control" name="precioVenta" placeholder="Precio de venta">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">           
                     <div class="col-md-4">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-shopping-cart"></i>
                           </span>
                           <input type="number" class="form-control" name="cantidadCaja" placeholder="Cantidad por caja">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-shopping-cart"></i>
                           </span>
                           <input type="number" step="0.000001" class="form-control" name="porcMayoreo" placeholder="Porcentaje mayoreo">
                        </div>
                     </div>
                     <div class="col-md-4">     
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-barcode"></i>
                           </span>
                           <input type="text" class="form-control" name="codigo" placeholder="Código">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-usd"></i>
                           </span>
                           <input type="number" step="0.01" class="form-control" name="precioLinea" placeholder="Precio en linea">
                        </div>
                     </div>
                     <div class="col-md-8">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-link"></i>
                           </span>
                           <input type="text" name="ligaInfo" class="form-control" placeholder="Liga de descripción"> 
                        </div>      
                     </div>
                  </div>
               </div>  
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-btn">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <label for="archivo">Seleccione el archivo:</label>
                     <input name="producto" type="file" multiple="multiple" class="btn btn-primary btn-file">
                  </div>
               </div>    
               <button type="submit" name="add_product" class="btn btn-danger">Agregar producto</button>
            </form>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>