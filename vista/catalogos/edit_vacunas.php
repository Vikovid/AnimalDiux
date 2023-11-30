<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar vacuna';
   // Checkin What level user has permission to view this page
   page_require_level(3);
   $vacuna = find_by_id('vacunas', (int)$_GET['id']);

   if (!$vacuna) {
      $session->msg("d", "Missing vacuna id.");
      redirect('vacunas.php');
   }
   if (isset($_POST['vacuna'])) {
      $req_fields = array('nombre');
      validate_fields($req_fields);

      if (empty($errors)) {
         $v_nombre  = remove_junk($db->escape($_POST['nombre']));

         $resultado = actRegistroPorCampo('vacunas', 'nombre', $v_nombre, 'id', $vacuna['id']);

         if ($resultado) {
            $session->msg('s', "vacuna ha sido actualizada.");
            redirect('vacunas.php', false);
         } else {
            $session->msg('d', ' Lo siento, falló la actualización.');
            redirect('edit_vacunas.php?id=' . $vacuna['id'], false);
         }
      } else {
         $session->msg("d", $errors);
         redirect('edit_vacunas.php?id=' . $vacuna['id'], false);
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
            <span>Editar vacuna</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
            <form method="post" action="edit_vacunas.php?id=<?php echo (int)$vacuna['id'] ?>">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" value="<?php echo remove_junk($vacuna['nombre']); ?>">
                  </div>
               </div>
               <button type="submit" name="vacuna" class="btn btn-danger">Actualizar</button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>