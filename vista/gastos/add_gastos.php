<?php
  $page_title = 'Agregar producto';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  $all_categories = find_all('categories');
  $all_photo = find_all('media');
  $all_proveedor = find_all('proveedor');  
  $all_sucursal = find_all('sucursal');
  $all_tipo_pagos = find_all('tipo_pago');
  $parametros = find_by_id('parametros','1');

  $user = current_user(); 
  $usuario = $user['id'];
  $idSucursal = $user['idSucursal'];
  ini_set('date.timezone','America/Mexico_City');
  $date=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  $idCategoria= isset($_POST['categoria']) ? $_POST['categoria']:'';
  if ($idCategoria != "")
     $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCategoria);
  else
     $subcategorias = array();

  if(isset($_POST['add_gastos'])){
     $req_fields = array('gasto-proveedor','gasto-sucursal','categoria','forma','product-title','precioCompra','fecha' );
     validate_fields($req_fields);
     if(empty($errors)){
        $p_name = remove_junk($db->escape($_POST['product-title']));
        $p_precioCompra = remove_junk($db->escape($_POST['precioCompra']));
        $p_proveedor = remove_junk($db->escape($_POST['gasto-proveedor']));
        $p_sucursal = remove_junk($db->escape($_POST['gasto-sucursal']));
        $p_forma = remove_junk($db->escape($_POST['forma']));
        $p_categoria = remove_junk($db->escape($_POST['categoria']));
        $p_fecha = remove_junk($db->escape($_POST['fecha']));
        $p_iva = remove_junk($db->escape($_POST['iva']));
        $p_total = remove_junk($db->escape($_POST['total']));
        $p_subcat = remove_junk($db->escape($_POST['subcats']));

        $respuesta = altaGasto($p_name,$p_precioCompra,$p_fecha,$p_proveedor,$p_sucursal,$p_forma,$p_categoria,$p_iva,$p_total,$p_subcat);

        if($respuesta){
           if ($p_forma == 1){
              $consMonto = buscaRegistroMaximo("caja","id");
              $montoActual=$consMonto['monto'];
              $idCaja = $consMonto['id'];

       	     $montoFinal = $montoActual - $p_total;

              if ($p_fecha == $date){
                 actCaja($montoFinal,$date,$idCaja);
                 altaHisEfectivo('11',$montoActual,$montoFinal,$idSucursal,$usuario,'',$date,$hora_actual);
              }
           }
           $session->msg('s',"Gasto agregado exitosamente. ");
           redirect('add_gastos.php', false);
        }else{
           $session->msg('d','Lo siento, Falló el registro.');
           redirect('add_gastos.php', false);
        }

     }else{
        $session->msg("d", $errors);
        redirect('add_gastos.php',false);
     }
  }
?>

<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script language="Javascript">

function recarga(){
  document.form1.action = "add_gastos.php";
  document.form1.submit();
}

</script>

<form name="form1" method="post" action="add_gastos.php">
<div class="row">
  <div class="col-md-7">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-7">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar gasto</span>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-5">
                        <select class="form-control" name="gasto-proveedor">
                           <option value="">Selecciona un proveedor</option>
                           <?php  foreach ($all_proveedor as $id): ?>
                           <?php if(isset($_POST["gasto-proveedor"]) && $_POST["gasto-proveedor"]==$id['idProveedor']){ ?>
                              <option value="<?php echo $id['idProveedor'] ?>" selected><?php echo $id['nom_proveedor'] ?></option>
                           <?php } else { ?>
                              <option value="<?php echo $id['idProveedor'] ?>"><?php echo $id['nom_proveedor'] ?></option>
                           <?php } ?>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-5">                  
                        <select class="form-control" name="gasto-sucursal">
                           <option value="">Selecciona una sucursal</option>
                           <?php  foreach ($all_sucursal as $idSuc): ?>
                           <?php if(isset($_POST["gasto-sucursal"]) && $_POST["gasto-sucursal"]==$idSuc['idSucursal']){ ?>
                              <option value="<?php echo $idSuc['idSucursal'] ?>" selected><?php echo $idSuc['nom_sucursal'] ?></option>
                           <?php } else { ?>
                              <option value="<?php echo $idSuc['idSucursal'] ?>"><?php echo $idSuc['nom_sucursal'] ?></option>
                           <?php } ?>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-5">
                        <select class="form-control" name="categoria" onchange="recarga();">
                           <option value="">Selecciona una categoría</option>
                           <?php  foreach ($all_categories as $cats): ?>
                           <?php if(isset($_POST["categoria"]) && $_POST["categoria"]==$cats['id']){ ?>
                              <option value="<?php echo $cats['id'] ?>" selected><?php echo $cats['name'] ?></option>
                           <?php } else { ?>
                              <option value="<?php echo $cats['id'] ?>"><?php echo $cats['name'] ?></option>
                           <?php } ?>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-5">
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
                     <div class="col-md-5">
                        <select class="form-control" name="forma">
                           <option value="">Selecciona forma de pago</option>
                           <?php  foreach ($all_tipo_pagos as $id_pago): ?>
                           <?php if(isset($_POST["forma"]) && $_POST["forma"]==$id_pago['id_pago']){ ?>
                              <option value="<?php echo $id_pago['id_pago'] ?>" selected><?php echo $id_pago['tipo_pago'] ?></option>
                           <?php } else { ?>
                              <option value="<?php echo $id_pago['id_pago'] ?>"><?php echo $id_pago['tipo_pago'] ?></option>
                           <?php } ?>
                           <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-5">
                         <input type="date" name="fecha" value="<?php echo isset($_POST['fecha']) ? $_POST['fecha']:'' ?>">
                      </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="product-title" placeholder="Descripción" value="<?php echo isset($_POST['product-title']) ? $_POST['product-title']:'' ?>">
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-2">
                        <span><strong>Subtotal</strong></span>
                     </div>
                     <div class="col-md-3">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-usd"></i>
                           </span>
                           <input type="number" step="0.01" min="1" class="form-control" name="precioCompra" onkeyup="asignar();" value="<?php echo isset($_POST['precioCompra']) ? $_POST['precioCompra']:'' ?>">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-2">
                        <span><strong>IVA <?php echo $parametros['iva'] ?> %</strong></span>
                     </div>
                  <div class="col-md-3">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-usd"></i>
                        </span>
                        <input type="number" class="form-control" name="iva" value="<?php echo isset($_POST['iva']) ? $_POST['iva']:'' ?>" readonly>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <input type="checkbox" name="aplicaIva" onclick="calculoIva();">
                     <span>Aplicar IVA</span>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <div class="row">
                  <div class="col-md-2">
                     <span><strong>Total</strong></span>
                  </div>
                  <div class="col-md-3">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-usd"></i>
                        </span>
                        <input type="number" class="form-control" name="total" value="<?php echo isset($_POST['total']) ? $_POST['total']:'' ?>" readonly>
                     </div>
                  </div>
               </div>
            </div>
            <input type="hidden" name="porcIva" value="<?php echo $parametros['iva']; ?>">
            <button type="submit" name="add_gastos" class="btn btn-danger">Agregar gasto</button>
         </div>
      </div>
   </div>
</div>
</form>
<?php include_once('../layouts/footer.php'); ?>
