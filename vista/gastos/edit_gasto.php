<?php
   $page_title = 'Editar gasto';
   require_once('../../modelo/load.php');
   
   page_require_level(1);
   $user = current_user(); 
   $usuario = $user['id'];
   $idSucursal = $user['idSucursal'];
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $hora_actual=date('H:i:s',time());

   $gastos = find_by_id('gastos',(int)$_GET['id']);
   $gastoActual = $gastos['total'];
   $proveedor = buscaRegistroPorCampo('proveedor','idProveedor',(int)$_GET['idProveedor']);
   $tipo_pago = buscaRegistroPorCampo('tipo_pago','id_pago',(int)$_GET['id_pago']);
   $all_proveedor = find_all('proveedor');
   $all_categoria = find_all('categories');
   $tipos_pago = find_all('tipo_pago');
   $all_sucursal = find_all('sucursal');
   $categoria = find_by_id('categories',(int)$_GET['idCategoria']);
   $parametros = find_by_id('parametros','1');

   if(!$gastos){
      $session->msg("d","Missing gasto id.");
      redirect('gastos.php');
   }

   $idProvAux = isset($_POST['idProveedor']) ? $_POST['idProveedor']:$gastos['idProveedor'];
   $idCatAux = isset($_POST['idCategoria']) ? $_POST['idCategoria']:$gastos['categoria'];
   $idTipoPagoAux = isset($_POST['idTipoPago']) ? $_POST['idTipoPago']:$gastos['tipo_pago'];
   $idSucAux = isset($_POST['sucursal']) ? $_POST['sucursal']:$gastos['idSucursal'];

   if ($idCatAux != "")
      $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCatAux);

   if(isset($_POST['gastos']) && $_POST['gastos'] == "1"){
      $respuesta = actGasto(remove_junk($db->escape($_POST['descripcion'])),
                            remove_junk($db->escape($_POST['precioCompra'])),
                            remove_junk($db->escape($_POST['idProveedor'])),
                            remove_junk($db->escape($_POST['idCategoria'])),
                            remove_junk($db->escape($_POST['subcats'])),
                            remove_junk($db->escape($_POST['idTipoPago'])),
                            remove_junk($db->escape($_POST['fecha'])),
                            remove_junk($db->escape($_POST['iva'])),
                            remove_junk($db->escape($_POST['total'])),
                            $gastos['id'],
                            remove_junk($db->escape($_POST['factura']))
                           );

      if($respuesta){
         if($p_tipoPago == 1){
            $consMonto = buscaRegistroMaximo("caja","id");
            $montoActual=$consMonto['monto'];
            $idCaja = $consMonto['id'];

            $totEfec = $gastoActual - $p_total;

            if ($totEfec > 0)
               $mov = "12";
            else
               $mov = "13";

            $montoFinal = $montoActual + $totEfec;

            $respCaja = actCaja($montoFinal,$fecha_actual,$idCaja);

            if($respCaja)
               altaHisEfectivo($mov,
                               $montoActual,
                               $montoFinal,
                               $idSucursal,
                               $usuario,
                               '',
                               $fecha_actual,
                               $hora_actual);
         }
         $session->msg('s',"Gasto ha sido actualizado. ");
         redirect('gastos.php?id='.$gastos['id'], false);
      }else{
         $session->msg('d','Lo siento, falló la actualización.');
         redirect('edit_gasto.php?id='.$gastos['id'].'&idProveedor='.$gastos['idProveedor'].'&id_pago='.$gastos['tipo_pago'].'&idCategoria='.$gastos['categoria'], false);
      }
   }
?>

<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script language="Javascript">
   function recarga(){
     document.form1.action = "edit_gasto.php?id=<?php echo (int)$gastos['id'] ?>&idProveedor=<?php echo $gastos['idProveedor'] ?>&id_pago=<?php echo $gastos['tipo_pago'] ?>&idCategoria=<?php echo $gastos['categoria'] ?>";
     document.form1.submit();
   }
   function valorOrig(){
      document.form1.idCategoria.value = document.form1.idCatAux.value;
      document.form1.idProveedor.value = document.form1.idProvAux.value;
      document.form1.idTipoPago.value = document.form1.idTipoPagoAux.value;
      document.form1.sucursal.value = document.form1.idSucAux.value;
   }
   function subSubmit(){
      var formulario = document.forms["form1"];
      
      var proveedor = formulario.elements["idProveedor"];
      var sucursal = formulario.elements["sucursal"];
      var categoria = formulario.elements["idCategoria"];
      var forma = formulario.elements["idTipoPago"];
      var titulo = formulario.elements["descripcion"];
      var precioCompra = formulario.elements["precioCompra"];
      var fecha = formulario.elements["fecha"];

      if (proveedor.value === "") {
         alert("Proveedor no puede estar vacío");
         document.forms["form1"]["idProveedor"].focus();
         return false;
      }         
      if (sucursal.value === "") {
         alert("Sucursal no puede estar vacío");
         document.forms["form1"]["sucursal"].focus();
         return false;
      }
      if (categoria.value === "") {
         alert("Categoría no puede estar vacío");
         document.forms["form1"]["idCategoria"].focus();
         return false;
      }
      if (forma.value === "") {
         alert("Forma de pago no puede estar vacío");
         document.forms["form1"]["idTipoPago"].focus();
         return false;
      }
      if (titulo.value === "") {
         alert("Descripción del gasto no puede estar vacío");
         document.forms["form1"]["descripcion"].focus();
         return false;
      }
      if (precioCompra.value === "") {
         alert("Precio de compra no puede estar vacío");
         document.forms["form1"]["precioCompra"].focus();
         return false;
      }
      if (fecha.value === "") {
         alert("Fecha no puede estar vacío");
         document.forms["form1"]["fecha"].focus();
         return false;
      }

      document.getElementsByName("gastos")[0].value = "1";
      document.form1.action = "edit_gasto.php?id=<?php echo (int)$gastos['id'] ?>&idProveedor=<?php echo $gastos['idProveedor'] ?>&id_pago=<?php echo $gastos['tipo_pago'] ?>&idCategoria=<?php echo $gastos['categoria'] ?>";
      document.form1.submit();   
   }

</script>

<body onload="valorOrig();">
<div class="row">
   <div class="col-md-7">
      <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-9">   
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Editar gasto</span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <form name="form1" method="post" action="edit_gasto.php?id=<?php echo (int)$gastos['id'] ?>&idProveedor=<?php echo $gastos['idProveedor'] ?>&id_pago=<?php echo $gastos['tipo_pago'] ?>&idCategoria=<?php echo $gastos['categoria'] ?>">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-5">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-list-alt"></i>
                              </span>
                              <input type="text" class="form-control" name="factura" placeholder="Factura" value="<?php echo isset($_POST['factura']) ? $_POST['factura']:$gastos['factura'] ?>">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-5">
                           <select class="form-control" name="idProveedor">
                              <option value="">Selecciona un proveedor</option>
                              <?php  foreach ($all_proveedor as $id): ?>
                              <?php if(isset($_POST["idProveedor"]) && $_POST["idProveedor"]==$id['idProveedor']){ ?>
                                 <option value="<?php echo $id['idProveedor'] ?>" selected><?php echo $id['nom_proveedor'] ?></option>
                              <?php } else { ?>
                                 <option value="<?php echo $id['idProveedor'] ?>"><?php echo $id['nom_proveedor'] ?></option>
                              <?php } ?>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-5">                  
                           <select class="form-control" name="sucursal">
                              <option value="">Selecciona una sucursal</option>
                              <?php  foreach ($all_sucursal as $idSuc): ?>
                              <?php if(isset($_POST["sucursal"]) && $_POST["sucursal"]==$idSuc['idSucursal']){ ?>
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
                           <select class="form-control" name="idCategoria" onchange="recarga();">
                              <option value="">Selecciona una categoría</option>
                              <?php  foreach ($all_categoria as $cat): ?>
                              <option value="<?php echo $cat['id'] ?>">
                              <?php echo $cat['name'] ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-5">
                           <select class="form-control" name="subcats">
                              <option value="">Selecciona una Subcategoría</option>
                              <?php  foreach ($subcategorias as $subcat): ?>
                              <option value="<?php echo (int)$subcat['idSubCategoria']; ?>" <?php if($gastos['subCategoria'] === $subcat['idSubCategoria'] && $gastos['categoria'] === $subcat['idCategoria']): echo "selected"; endif; ?> >
                              <?php echo remove_junk($subcat['nombre']); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-5">
                           <select class="form-control" name="idTipoPago">
                              <option value="">Selecciona una forma de pago</option>
                              <?php  foreach ($tipos_pago as $tipo): ?>
                              <?php if(isset($_POST["forma"]) && $_POST["forma"]==$id_pago['id_pago']){ ?>
                                 <option value="<?php echo $tipo['id_pago'] ?>" selected><?php echo $tipo['tipo_pago'] ?></option>
                              <?php } else { ?>
                                 <option value="<?php echo $tipo['id_pago'] ?>"><?php echo $tipo['tipo_pago'] ?></option>
                              <?php } ?>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-5">
                           <input type="date" name="fecha" value="<?php echo isset($_POST['fecha']) ? $_POST['fecha']:$gastos['fecha'] ?>">
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md-10">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-th-large"></i>
                              </span>
                              <input type="text" class="form-control" name="descripcion" value="<?php echo isset($_POST['descripcion']) ? $_POST['descripcion']:$gastos['descripcion'] ?>">
                           </div>
                        </div>
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
                              <input type="number" step="0.01" min="1" class="form-control" name="precioCompra" value="<?php echo isset($_POST['precioCompra']) ? $_POST['precioCompra']:$gastos['monto'] ?>" onkeyup="asignar();">
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
                              <input type="number" class="form-control" name="iva" value="<?php echo isset($_POST['iva']) ? $_POST['iva']:$gastos['iva'] ?>" readonly>
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
                              <input type="number" class="form-control" name="total" value="<?php echo isset($_POST['total']) ? $_POST['total']:$gastos['total'] ?>" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
                  <input type="hidden" name="porcIva" value="<?php echo $parametros['iva']; ?>">
                  <input type="hidden" name="idProvAux" value="<?php echo $idProvAux; ?>">
                  <input type="hidden" name="idCatAux" value="<?php echo $idCatAux; ?>">
                  <input type="hidden" name="idTipoPagoAux" value="<?php echo $idTipoPagoAux; ?>">
                  <input type="hidden" name="idSucAux" value="<?php echo $idSucAux; ?>">
                  <a href="#" onclick="subSubmit();" class="btn btn-danger">Actualizar</a>
                  <input type="hidden" name="gastos" value="0">
               </form>            
            </div>
         </div>
      </div>
   </div>
</div>
</body>
<?php include_once('../layouts/footer.php'); ?>
