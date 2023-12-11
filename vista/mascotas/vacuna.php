<?php
   require_once('../../modelo/load.php');
   $page_title = 'Vacuna';

   // Checkin What level user has permission to view this page
   page_require_level(3);

   $encargados = find_all('users');
   $vacunas =    find_all('vacunas');
   $idmas =      isset($_GET['idMas']) ? $_GET['idMas']:'';

   ini_set ('date.timezone','America/Mexico_City');
   $fecha_actual = date('Y-m-d',time());

   if (isset($_POST['responsable'])) {

      $idMasc  =    remove_junk($db->escape($_POST['id'])); 
      $req_fields = array('responsable','vacuna','fechaCaducidad');
      validate_fields($req_fields);

      if (empty($errors)) {

         $fechaCaducidad = remove_junk($db->escape($_POST['fechaCaducidad']));
         $observaciones =  remove_junk($db->escape($_POST['vacuna']));
         $responsable =    remove_junk($db->escape($_POST['responsable']));
         $fechaVacuna =    remove_junk($db->escape($_POST['fechaVacuna']));
         $nota =           remove_junk($db->escape($_POST['nota']));
         $id =             remove_junk($db->escape($_POST['id']));

         $resultado = altaVacuna($responsable,$observaciones,'0',$fecha_actual,$id,$fechaCaducidad,$fechaVacuna,$nota);

         if ($resultado) {
            $session->msg('s', "Registro Exitoso.");
            redirect('history.php?idMascotas='.$idMasc, false);
            // redirect('history.php?idMascotas='.$id, false);
         } else {
            $session->msg('d',' Lo siento, registro falló.');
            redirect('vacuna.php?idMas='.$idMasc, false);
         }
      } else {
         $session->msg("d", $errors);
         redirect('vacuna.php?idMas='.$idMasc,false);
      }
   }

   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre =  $mascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="vacuna.php">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Vacuna de: <?php echo $nombre ?></span>
            </strong>     
         </div>
         <div class="panel-body">
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Responsable:</label>
               </div>
               <div class="col-md-8">
                  <select class="form-control" name="responsable">
                     <option value="">Seleccione responsable</option>
                     <?php  foreach ($encargados as $id): ?>
                     <option value="<?php echo $id['username'] ?>">
                     <?php echo remove_junk($id['name']); ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>  
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Vacuna:</label>
               </div>
               <div class="col-md-8">
                  <select class="form-control" name="vacuna">
                     <option value="">Seleccione vacuna</option>
                     <?php  foreach ($vacunas as $id2): ?>
                     <option value="<?php echo $id2['nombre'] ?>">
                     <?php echo remove_junk($id2['nombre']); ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Fecha de caducidad:</label>
               </div>
               <div class="col-md-8">
                  <input type="date" name="fechaCaducidad">
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Fecha sig. Vacuna:</label>
               </div>
               <div class="col-md-8">
                  <input type="date" name="fechaVacuna">
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <p><textarea name="nota" class="form-control" placeholder="Nota" maxlength="500" rows="5" style="resize: none"></textarea></p>
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label for="inputPassword">Día:</label>
               </div>
               <div class="col-md-8">
                  <strong><?php echo $time2."  ".$time1;?></strong>
               </div>
            </div>
            <div class="form-group col-md-12">
               <input type="hidden" value="<?php echo $idmas ?>" name="id">
               <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
               <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
               <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
            </div>
         </div>
      </div>
   </div>
</form>
<?php include_once('../layouts/footer.php'); ?>