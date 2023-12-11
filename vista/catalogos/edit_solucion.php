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
      $req_fields = array('nombre', 'cantidad', 'fechaCaducidad');
      validate_fields($req_fields);

      if (empty($errors)) {
         $s_fechaCaducidad = remove_junk($db->escape($_POST['fechaCaducidad']));
         $s_cantidad =       remove_junk($db->escape($_POST['cantidad']));
         $s_nombre =         remove_junk($db->escape($_POST['nombre']));

         $resultado = actSolucion($s_nombre, $s_cantidad, $s_fechaCaducidad, $solucion['id']);

         if ($resultado) {
            $session->msg('s', "Medicamento Inyectable actualizado exitosamente.");
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

<form method="post" action="edit_solucion.php?id=<?php echo (int)$solucion['id'] ?>">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-edit"></span>
               <span>Editar Medicamento Inyectable</span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <div class="form-group row col-md-9">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" value="<?php echo remove_junk($solucion['nombre']); ?>">
                  </div>
               </div>
               <div class="form-group row col-md-9">
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-shopping-cart"></i>
                        </span>
                        <input type="number" class="form-control" name="cantidad" value="<?php echo remove_junk($solucion['cantidad']) ?>">
                     </div>
                  </div>
               </div>
               <div class="form-group row col-md-9">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                     </span>
                     <label class="col-md-6">Fecha de caducidad:</label>
                     <div class="col-md-3">
                        <input type="date" name="fechaCaducidad" value="<?php echo remove_junk($solucion['fechaCaducidad']) ?>">
                     </div>
                  </div>
               </div>
               <div class="form-group row col-md-9">
                  <button type="submit" name="solucion" class="btn btn-danger">Actualizar</button>
                  <a href="solucion.php" class="btn btn-primary"> Regresar </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>

<?php include_once('../layouts/footer.php'); ?>