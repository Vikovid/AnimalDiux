<?php
  $page_title = 'Editar proveedor';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $proveedor = buscaRegistroPorCampo("proveedor","idProveedor",$_GET['idProveedor']);
  if(!$proveedor){
     $session->msg("d","Missing proveedor idProveedor.");
     redirect('proveedores.php');
  }

  if(isset($_POST['proveedor'])){
     $req_fields = array('nom_proveedor','direccion','telefono','contacto' );
     validate_fields($req_fields);

     if(empty($errors)){
        $nom_proveedor  = remove_junk($db->escape($_POST['nom_proveedor']));
        $direccion  = remove_junk($db->escape($_POST['direccion']));
        $telefono  = remove_junk($db->escape($_POST['telefono']));
        $contacto  = remove_junk($db->escape($_POST['contacto']));

        if(strlen($telefono) === 10) {

           $resultado = actProveedor($nom_proveedor,$direccion,$telefono,$contacto,$proveedor['idProveedor']);

           if($resultado){
              $session->msg('s',"Proveedor ha sido actualizado. ");
              redirect('proveedores.php?id='.$proveedor['idProveedor'], false);
           }else{
              $session->msg('d','Lo siento, falló la actualización.');
              redirect('edit_proveedor.php?idProveedor='.$proveedor['idProveedor'], false);
           }
        }else{
           $session->msg('d',' El número telefónico debe ser de 10 dígitos.');
           redirect('edit_proveedor.php?idProveedor='.$proveedor['idProveedor'], false);
        }
  //aqui esta ok
     }else{
        $session->msg("d", $errors);
        redirect('edit_proveedor.php?id='.$proveedor['idProveedor'], false);
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
            <span>Editar proveedor</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
            <form method="post" action="edit_proveedor.php?idProveedor=<?php echo (int)$proveedor['idProveedor'] ?>">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nom_proveedor" value="<?php echo remove_junk($proveedor['nom_proveedor']);?>">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="direccion" value="<?php echo remove_junk($proveedor['direccion']);?>">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="number" class="form-control" name="telefono" value="<?php echo remove_junk($proveedor['telefono']);?>">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="contacto" value="<?php echo remove_junk($proveedor['contacto']);?>">
                  </div>
               </div>
               <button type="submit" name="proveedor" class="btn btn-danger">Actualizar</button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
