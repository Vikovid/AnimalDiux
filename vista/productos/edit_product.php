<?php
   $page_title = 'Editar producto';
   require_once('../../modelo/load.php');

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

   $idCatAux =  isset($_POST['categoria']) ? $_POST['categoria']:$product['categorie_id'];
   $idProvAux = isset($_POST['proveedor']) ? $_POST['proveedor']:$product['idProveedor'];
   $idSucAux =  isset($_POST['sucursal'])  ? $_POST['sucursal'] :$product['idSucursal'];

   if ($idCatAux != "")
      $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCatAux);
   else
      $subcategorias = array();

   if(isset($_POST['product']) && $_POST['product'] == "1"){
      $p_name  =        remove_junk($db->escape($_POST['nom_producto']));
      $p_codigo  =      remove_junk($db->escape($_POST['Codigo']));
      $p_comentario  =  remove_junk($db->escape($_POST['comentario']));
      $p_cat   =        (int)$_POST['categoria'];
      $p_sucur   =      (int)$_POST['sucursal'];
      $p_prov   =       (int)$_POST['proveedor'];
      $p_qty   =        remove_junk($db->escape($_POST['cantidad']));
      $p_buy   =        remove_junk($db->escape($_POST['precioCompra']));
      $p_sale  =        remove_junk($db->escape($_POST['precioVenta']));
      $p_fecCad  =      remove_junk($db->escape($_POST['fecha_caducidad']));
      $p_cantCaja  =    remove_junk($db->escape($_POST['cantidadCaja']));
      $p_precioCaja  =  remove_junk($db->escape($_POST['precioCaja']));
      $p_subcat  =      remove_junk($db->escape($_POST['subcats']));
      $foto =           "";   
      $Id =             "";

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
               } else {
                  $session->msg('d','Formato de archivo no válido.');
                  redirect('edit_product.php?id='.$product['id'], false);
               }
            }
            $resultado = actProducto($p_name,
                                     $p_qty,
                                     $p_buy,
                                     $p_sale,
                                     $p_cat,
                                     $p_subcat,
                                     $p_codigo,
                                     $p_sucur,
                                     $p_prov,
                                     $p_fecCad,
                                     $p_cantCaja,
                                     $p_precioCaja,
                                     $fecha_actual,
                                     $foto,
                                     $product['id']);
         } else {
               $resultado = actProducto($p_name,
                                        $p_qty,
                                        $p_buy,
                                        $p_sale,
                                        $p_cat,
                                        $p_subcat,
                                        $p_codigo,
                                        $p_sucur,
                                        $p_prov,
                                        $p_fecCad,
                                        $p_cantCaja,
                                        $p_precioCaja,
                                        $fecha_actual,
                                        '',
                                        $product['id']);
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
         if ($resultado) {
            $session->msg('s',"Producto ha sido actualizado. ");
            redirect('product.php', false);
         } else {
            $session->msg('d',' Lo siento, actualización falló.');
            redirect('edit_product.php?id='.$product['id'], false);
         }
      } else {
         $session->msg('d','Lo siento, Nombre de producto ya registrado.');      
         redirect('edit_product.php?id='.$product['id'], false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script language="Javascript">
   function recarga(){
      document.form1.action = "edit_product.php?id=<?php echo (int)$product['id'] ?>";
      document.form1.submit();
   }

   function valorOrig(){
      document.form1.categoria.value = document.form1.idCatAux.value;
      document.form1.proveedor.value = document.form1.idProvAux.value;
      document.form1.sucursal.value = document.form1.idSucAux.value;
   }
   function subSubmit() {
      var formulario = document.forms["form1"];

      var nom_producto = formulario.elements["nom_producto"];
      var cats = formulario.elements["categoria"];
      var cantidad = formulario.elements["cantidad"];
      var precioCompra = formulario.elements["precioCompra"];
      var precioVenta = formulario.elements["precioVenta"];
      var codigo = formulario.elements["Codigo"];
      var proveedor = formulario.elements["proveedor"];
      var sucursal = formulario.elements["sucursal"];
      var comentario = formulario.elements["comentario"];

      if (nom_producto.value === "") {
         alert("El campo 'Descripción' no puede estar vacío");
         document.form1.nom_producto.focus();
         return false;
      }
      if (cats.value === "") {
         alert("El campo 'Categoría' no puede estar vacío");
         document.form1.cats.focus();
         return false;
      }
      if (cantidad.value === "") {
         alert("El campo 'Cantidad' no puede estar vacío");
         document.form1.cantidad.focus();
         return false;
      }
      if (precioCompra.value === "") {
         alert("El campo 'Precio de compra' no puede estar vacío");
         document.form1.precioCompra.focus();
         return false;
      }
      if (precioVenta.value === "") {
         alert("El campo 'Precio de venta' no puede estar vacío");
         document.form1.precioVenta.focus();
         return false;
      }
      if (codigo.value === "") {
         alert("El campo 'Código' no puede estar vacío");
         document.form1.codigo.focus();
         return false;
      }
      if (proveedor.value === "") {
         alert("El campo 'Proveedor' no puede estar vacío");
         document.form1.proveedor.focus();
         return false;
      }
      if (sucursal.value === "") {
         alert("El campo 'Sucursal' no puede estar vacío");
         document.form1.sucursal.focus();
         return false;
      }
      if (comentario.value === "") {
         alert("El campo 'Comentario' no puede estar vacío");
         document.form1.comentario.focus();
         return false;
      }


      document.getElementsByName("product")[0].value = "1";
      document.form1.action = "edit_product.php?id=<?php echo (int)$product['id'] ?>";
      document.form1.submit();
   }
</script>
<body onload="valorOrig();">
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-11">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Editar producto</span>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form name="form1" method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>" enctype="multipart/form-data">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nom_producto" value="<?php echo isset($_POST['nom_producto']) ? $_POST['nom_producto']:$product['name'] ?>" oninput="mayusculas(event)">
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-4">
                        <select class="form-control" name="categoria" onchange="recarga();">
                           <option value="">Selecciona una categoría</option>
                           <?php  foreach ($all_categories as $cat): ?>
                           <option value="<?php echo $cat['id'] ?>">
                           <?php echo $cat['name'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-4">
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
                  <div class="row">
                     <div class="col-md-4">
                        <select class="form-control" name="proveedor">
                           <option value="">Sin proveedor</option>
                           <?php  foreach ($all_proveedor as $proveedor): ?>
                           <option value="<?php echo $proveedor['idProveedor'] ?>">
                           <?php echo $proveedor['nom_proveedor'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-4">
                        <select class="form-control" name="sucursal">
                           <option value="">Sin sucursal</option>
                           <?php  foreach ($all_sucursal as $sucursal): ?>
                           <option value="<?php echo $sucursal['idSucursal'] ?>">
                           <?php echo $sucursal['nom_sucursal'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-3">     
                        <label class="col-sm-5 col-form-label">Fecha de caducidad:</label>
                        <div class="col-sm-2">
                           <input type="date" name="fecha_caducidad" value="<?php echo isset($_POST['fecha_caducidad']) ? $_POST['fecha_caducidad']:$product['fecha_caducidad'] ?>">
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
                              <input type="number" step="0.01" class="form-control" name="cantidad" value="<?php echo isset($_POST['cantidad']) ? $_POST['cantidad']:$product['quantity'] ?>">
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
                           <input type="number" step="0.01" class="form-control" name="precioCompra" value="<?php echo isset($_POST['precioCompra']) ? $_POST['precioCompra']:$product['buy_price'] ?>">
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
                           <input type="number" step="0.01" class="form-control" name="precioVenta" value="<?php echo isset($_POST['precioVenta']) ? $_POST['precioVenta']:$product['sale_price'] ?>">
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
                     <label for="qty">Código</label>
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-barcode"></i>
                        </span>
                        <input type="text" class="form-control" name="Codigo" value="<?php echo isset($_POST['Codigo']) ? $_POST['Codigo']:$product['Codigo'] ?>">
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-barcode"></i>
                  </span>
                  <input type="text" class="form-control" name="comentario" placeholder="comentario" value="<?php echo isset($_POST['comentario']) ? $_POST['comentario']:'' ?>">
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
            <input type="hidden" class="form-control" name="idCatAux" value="<?php echo $idCatAux; ?>">
            <input type="hidden" class="form-control" name="idProvAux" value="<?php echo $idProvAux; ?>">
            <input type="hidden" class="form-control" name="idSucAux" value="<?php echo $idSucAux; ?>">
            <a href="#" onclick="subSubmit();" class="btn btn-danger"> Actualizar </a>
            <input type="hidden" name="product" value="0">
            </form>
            </div>
         </div>
      </div>
   </div>
</div>
</body>
<?php include_once('../layouts/footer.php'); ?>