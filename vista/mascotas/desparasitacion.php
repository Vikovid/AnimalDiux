<?php
   $page_title = 'Desparasitacion';
   require_once('../../modelo/load.php');

   // Checkin What level user has permission to view this page
   page_require_level(3);

   $encargados =      find_all('users');
   $desparasitantes = find_all('desparasitantes');

   $idmas = isset($_GET['idMas']) ? $_GET['idMas']:'';

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual = date('Y-m-d',time());

   if (isset($_POST['responsable'])) {
      $idMasc  =    remove_junk($db->escape($_POST['id']));
      $req_fields = array('responsable','desparasitante');
      validate_fields($req_fields);

      if (empty($errors)) {
         $desparasitante = remove_junk($db->escape($_POST['desparasitante']));
         $fechaSigDesp =   remove_junk($db->escape($_POST['fechaSigDesp']));
         $responsable =    remove_junk($db->escape($_POST['responsable']));
         $nota =           remove_junk($db->escape($_POST['nota']));
         $id =             remove_junk($db->escape($_POST['id']));

         $resultado = altaDesparacitacion($responsable,$desparasitante,'0',$fecha_actual,$id,'',$fechaSigDesp,$nota);

         if ($resultado) {
            $session->msg('s', "Registro Exitoso.");
            redirect('history.php?idMascotas='.$idMasc, false);
         } else {
            $session->msg('d',' Lo siento, registro falló.');
            redirect('desparasitacion.php?idMas='.$idMasc, false);
         }
      } else {
         $session->msg("d", $errors);
         redirect('desparasitacion.php?idMas='.$idMasc,false);
      }
   }

   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre =  $mascota['nombre'];
?>

<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="desparasitacion.php">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Desparasitación de:</span>
               <span><?php echo remove_junk($nombre); ?></span>
            </strong>     
         </div>
         <div class="panel-body">
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Responsable:</label>
               </div>
               <div class="col-md-8">
                  <select class="form-control" name="responsable">
                     <option value="">Selecciona responsable</option>
                     <?php  foreach ($encargados as $id): ?>
                        <option value="<?php echo $id['username'] ?>">
                           <?php echo remove_junk($id['name']); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>  
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Desparasitante:</label>
               </div>
               <div class="col-md-8">
                  <select class="form-control" name="desparasitante">
                     <option value="">Selecciona desparasitante</option>
                     <?php  foreach ($desparasitantes as $id2): ?>
                        <option value="<?php echo $id2['nombre'] ?>">
                           <?php echo remove_junk($id2['nombre']); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>  
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Fecha sig. Desparasitación:</label>
               </div>
               <div class="col-md-8">
                  <input type="date" name="fechaSigDesp">
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