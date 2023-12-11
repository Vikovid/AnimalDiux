<?php
   $page_title = 'Agregar producto';
   require_once('../../modelo/load.php');

   page_require_level(3);
   $all_categories = find_all('categories');
   $all_proveedor = find_all('proveedor');
   $all_sucursal = find_all('sucursal');
   $user = current_user(); 
   $usuario = $user['id'];
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $hora_actual=date('H:i:s',time());  

   $idCategoria= isset($_POST['cats']) ? $_POST['cats']:'';
   if ($idCategoria != "")
      $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCategoria);
   else
      $subcategorias = array();

   if(isset($_POST['add_product']) && ($_POST['add_product'] == "1")){
      $foto = "";

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

      $resultado = altaProducto(remove_junk($db->escape($_POST['nom_producto'])),
                                remove_junk($db->escape($_POST['cantidad'])),
                                remove_junk($db->escape($_POST['precioCompra'])),
                                remove_junk($db->escape($_POST['precioVenta'])),
                                remove_junk($db->escape($_POST['cats'])),
                                $foto,
                                $fecha_actual,
                                remove_junk($db->escape($_POST['codigo'])),
                                remove_junk($db->escape($_POST['proveedor'])),
                                remove_junk($db->escape($_POST['sucursal'])),
                                remove_junk($db->escape($_POST['fecha_caducidad'])),
                                remove_junk($db->escape($_POST['cantidadCaja'])),
                                remove_junk($db->escape($_POST['precioCaja'])),
                                $fecha_actual,
                                remove_junk($db->escape($_POST['subcats']))
                               );

      if($resultado){
         $product = buscaRegistroMaximo('products','id');
         altaHistorico('1',
                       $product['id'],
                       '0',
                       remove_junk($db->escape($_POST['cantidad'])),
                       'Producto Nuevo',
                       remove_junk($db->escape($_POST['sucursal'])),
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
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script language="Javascript">
   function recarga(){
      document.form1.action = "add_product.php";
      document.form1.submit();
   }
   function subSubmit(){
      var formulario = document.forms["form1"];

      var nom_producto = formulario.elements["nom_producto"];
      var cats = formulario.elements["cats"];
      var cantidad = formulario.elements["cantidad"];
      var precioCompra = formulario.elements["precioCompra"];
      var precioVenta = formulario.elements["precioVenta"];
      var codigo = formulario.elements["codigo"];
      var proveedor = formulario.elements["proveedor"];
      var sucursal = formulario.elements["sucursal"];
      
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

      document.getElementsByName("add_product")[0].value = "1";
      document.form1.action = "add_product.php";
      document.form1.submit();   
   }
</script>
<body>
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
               <span>Agregar producto</span>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <form name="form1" method="post" action="add_product.php" enctype="multipart/form-data">
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-th-large"></i>
                        </span>
                        <input type="text" class="form-control" name="nom_producto" placeholder="Descripción" oninput="mayusculas(event)" value="<?php echo isset($_POST['nom_producto']) ? $_POST['nom_producto']:'' ?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <select class="form-control" name="cats" onchange="recarga();">
                              <option value="">Selecciona una categoría</option>
                              <?php  foreach ($all_categories as $cats): ?>
                              <?php if(isset($_POST["cats"]) && $_POST["cats"]==$cats['id']){ ?>
                                 <option value="<?php echo $cats['id'] ?>" selected><?php echo $cats['name'] ?></option>
                              <?php } else { ?>
                                 <option value="<?php echo $cats['id'] ?>"><?php echo $cats['name'] ?></option>
                              <?php } ?>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-4">
                           <select class="form-control" name="subcats">
                              <option value="">Selecciona una subcategoría</option>
                              <?php  foreach ($subcategorias as $subcat): ?>
                                 <option value="<?php echo $subcat['idSubCategoria'] ?>">
                              <?php echo $subcat['nombre'] ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-4">
                           <select class="form-control" name="proveedor">
                              <option value="">Selecciona un proveedor</option>
                              <?php  foreach ($all_proveedor as $id): ?>
                              <?php if(isset($_POST["proveedor"]) && $_POST["proveedor"]==$id['idProveedor']){ ?>
                                 <option value="<?php echo $id['idProveedor'] ?>" selected><?php echo $id['nom_proveedor'] ?></option>
                              <?php } else { ?>
                                 <option value="<?php echo $id['idProveedor'] ?>"><?php echo $id['nom_proveedor'] ?></option>
                              <?php } ?>
                              <?php endforeach; ?>
                           </select>
                        </div>                  
                        <div class="col-md-4">
                           <select class="form-control" name="sucursal">
                              <option value="">Selecciona una sucursal</option>
                              <?php  foreach ($all_sucursal as $id): ?>
                              <?php if(isset($_POST["sucursal"]) && $_POST["sucursal"]==$id['idSucursal']){ ?>
                                 <option value="<?php echo $id['idSucursal'] ?>" selected><?php echo $id['nom_sucursal'] ?></option>
                              <?php } else { ?>
                                 <option value="<?php echo $id['idSucursal'] ?>"><?php echo $id['nom_sucursal'] ?></option>
                              <?php } ?>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-3">     
                           <label class="col-sm-5 col-form-label">Fecha de caducidad:</label>
                           <div class="col-sm-2">
                              <input type="date" name="fecha_caducidad" min="<?php echo $fecha_actual ?>" value="<?php echo isset($_POST['fecha_caducidad']) ? $_POST['fecha_caducidad']:'' ?>">
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
                              <input type="number" step="0.01" class="form-control" name="cantidad" placeholder="Cantidad" value="<?php echo isset($_POST['cantidad']) ? $_POST['cantidad']:'' ?>">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-usd"></i>
                              </span>
                              <input type="number" step="0.01" class="form-control" name="precioCompra" placeholder="Precio de compra" value="<?php echo isset($_POST['precioCompra']) ? $_POST['precioCompra']:'' ?>">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-usd"></i>
                              </span>
                              <input type="number" step="0.01" class="form-control" name="precioVenta" placeholder="Precio de venta" value="<?php echo isset($_POST['precioVenta']) ? $_POST['precioVenta']:'' ?>">
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
                              <input type="number" class="form-control" name="cantidadCaja" placeholder="Cantidad por caja" value="<?php echo isset($_POST['cantidadCaja']) ? $_POST['cantidadCaja']:'' ?>">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-shopping-cart"></i>
                              </span>
                              <input type="number" step="0.01" class="form-control" name="precioCaja" placeholder="Precio por caja" value="<?php echo isset($_POST['precioCaja']) ? $_POST['precioCaja']:'' ?>">
                           </div>
                        </div>
                        <div class="col-md-4">     
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-barcode"></i>
                              </span>
                              <input type="text" class="form-control" name="codigo" placeholder="Código" value="<?php echo isset($_POST['codigo']) ? $_POST['codigo']:'' ?>">
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
                  <a href="#" onclick="subSubmit();" class="btn btn-danger"> Agregar producto </a>
                  <input type="hidden" name="add_product" value="0">
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
</body>
<?php include_once('../layouts/footer.php'); ?>