<?php
   require_once('../../modelo/load.php');
   $page_title = 'Agregar Medicamento Inyectable';

   // Checkin What level user has permission to view this page
   page_require_level(3);

   if (isset($_POST['add_solucion'])) {
      $req_fields = array('nombre','cantidad','fechaCaducidad');
      validate_fields($req_fields);

      if(empty($errors)){
         $s_fechaCaducidad = remove_junk($db->escape($_POST['fechaCaducidad']));
         $s_cantidad =     remove_junk($db->escape($_POST['cantidad']));
         $s_nombre =       remove_junk($db->escape($_POST['nombre']));

         $resultado = altaSolucion($s_nombre, $s_cantidad,$s_fechaCaducidad);

         if($resultado)
            $session->msg('s', "Medicamento Inyectable agregado exitosamente.");
         else
            $session->msg('d', ' Lo siento, falló el registro.');

         redirect('solucion.php', false);
      } else {
         $session->msg("d", $errors);
         redirect('add_solucion.php', false);
      }
   }
?>

<?php include_once('../layouts/header.php'); ?>

<form method="post" action="add_solucion.php" class="clearfix">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-plus"></span>
               <span>Agregar Medicamento Inyectable</span>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <div class="form-group row col-md-9">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                  </div>
               </div>
               <div class="form-group row col-md-9">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-shopping-cart"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="cantidad" placeholder="Cantidad">
                  </div>
               </div>
               <div class="form-group row col-md-9">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                     </span>
                     <label class="col-md-6">Fecha de caducidad:</label>
                     <div class="col-md-3">
                        <input type="date" name="fechaCaducidad">
                     </div>
                  </div>
               </div>
               <div class="form-group row col-md-9">
                  <button type="submit" name="add_solucion" class="btn btn-danger">Agregar</button>
                  <a href="solucion.php" class="btn btn-primary"> Regresar </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>

<?php include_once('../layouts/footer.php'); ?>