<?php
   require_once('../../modelo/load.php');
   $page_title = 'Agregar vacuna';
   // Checkin What level user has permission to view this page
   page_require_level(3);

   if(isset($_POST['add_vacunas'])){
      $req_fields = array('nombre');
      validate_fields($req_fields);

      if(empty($errors)){
         $v_nombre  = remove_junk($db->escape($_POST['nombre']));

         $resultado = altaVacunas($v_nombre);

         if($resultado)
            $session->msg('s',"Vacuna agregada exitosamente.");
         else
            $session->msg('d',' Lo siento, falló el registro.');
            
         redirect('vacunas.php', false);
      } else {
         $session->msg("d", $errors);
         redirect('add_vacunas.php',false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>

<div class="row col-md-9">
   <?php echo display_msg($msg); ?>
</div>
<div class="row col-md-9">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Agregar Vacuna</span>
            <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-12">
            <form method="post" action="add_vacunas.php" class="clearfix">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                  </div>
               </div>
               <button type="submit" name="add_vacunas" class="btn btn-danger">Agregar vacuna</button>
            </form>
         </div>
      </div>
   </div>
</div>

<?php include_once('../layouts/footer.php'); ?>