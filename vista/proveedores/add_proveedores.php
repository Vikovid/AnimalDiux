<?php
  $page_title = 'Agregar proveedor';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $all_categories = find_all('categories');
  $all_photo = find_all('media');

  if(isset($_POST['add_proveedores'])){
     $req_fields = array('proveedor-title','proveedor-dir','telefono','proveedor-contacto' );
     validate_fields($req_fields);
     if(empty($errors)){
        $p_proveedor  = remove_junk($db->escape($_POST['proveedor-title']));
        $p_direccion  = remove_junk($db->escape($_POST['proveedor-dir']));
        $p_telefono  = remove_junk($db->escape($_POST['telefono']));
        $p_contacto  = remove_junk($db->escape($_POST['proveedor-contacto']));

        if(strlen($p_telefono) === 10) {

           $resultado = altaProveedor($p_proveedor,$p_direccion,$p_telefono,$p_contacto);

           if($resultado){
              $session->msg('s',"Proveedor agregado exitosamente. ");
              redirect('proveedores.php', false);
           }else{
              $session->msg('d','Lo siento, falló el registro.');
              redirect('add_proveedores.php', false);
           }
        }else{
           $session->msg('d','El número telefónico debe ser de 10 dígitos.');
           redirect('add_proveedores.php', false);
        }
     }else{
        $session->msg("d", $errors);
        redirect('add_proveedores.php',false);
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
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar proveedor</span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <form method="post" action="add_proveedores.php" class="clearfix">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="proveedor-title" placeholder="Proveedor">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="proveedor-dir" placeholder="Dirección">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="number" class="form-control" name="telefono" placeholder="Teléfono">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="proveedor-contacto" placeholder="Contacto">
                  </div>
               </div>
               <button type="submit" name="add_proveedores" class="btn btn-danger">Agregar proveedor</button>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
