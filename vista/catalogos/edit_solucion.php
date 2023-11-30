<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar Medicamento Inyectable';
   // Checkin What level user has permission to view this page
   page_require_level(3);
   $solucion = find_by_id('soluciones', (int)$_GET['id']);

   if (!$solucion) {
      $session->msg("d", "Missing solucion id.");
      redirect('solucion.php');
   }
   if (isset($_POST['solucion'])) {
      $req_fields = array('nombre', 'cantidad');
      validate_fields($req_fields);

      if (empty($errors)) {
         $s_nombre = remove_junk($db->escape($_POST['nombre']));
         $s_cantidad = remove_junk($db->escape($_POST['cantidad']));

         $resultado = actSolucion($s_nombre, $s_cantidad, $solucion['id']);

         if ($resultado) {
            $session->msg('s', "Medicamento Inyectable ha sido actualizado. ");
            redirect('solucion.php', false);
         } else {
            $session->msg('d', ' Lo siento, falló la actualización.');
            redirect('edit_solucion.php?id=' . $solucion['id'], false);
         }
      } else {
         $session->msg("d", $errors);
         redirect('edit_solucion.php?id=' . $solucion['id'], false);
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
            <span>Editar Medicamento Inyectable</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
            <form method="post" action="edit_solucion.php?id=<?php echo (int)$solucion['id'] ?>">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" value="<?php echo remove_junk($solucion['nombre']); ?>">
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-shopping-cart"></i>
                              </span>
                              <input type="number" class="form-control" name="cantidad" value="<?php echo remove_junk($solucion['cantidad']) ?>">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <button type="submit" name="solucion" class="btn btn-danger">Actualizar</button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>