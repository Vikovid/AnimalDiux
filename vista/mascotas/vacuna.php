<?php
   require_once('../../modelo/load.php');
   $page_title = 'Vacuna';
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $encargados = find_all('users');
   $vacunas = find_all('vacunas');
   $idmas= isset($_GET['idMas']) ? $_GET['idMas']:'';

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   if(isset($_POST['responsable'])){
      $idMasc  = remove_junk($db->escape($_POST['id'])); 
      $req_fields = array('responsable','vacuna','fechaCaducidad');
      validate_fields($req_fields);
      if(empty($errors)){
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $observaciones  = remove_junk($db->escape($_POST['vacuna']));
         $fechaCaducidad  = remove_junk($db->escape($_POST['fechaCaducidad']));
         $fechaVacuna = remove_junk($db->escape($_POST['fechaVacuna']));
         $nota = remove_junk($db->escape($_POST['nota']));
         $id  = remove_junk($db->escape($_POST['id']));

         $resultado = altaVacuna($responsable,$observaciones,'0',$fecha_actual,$id,$fechaCaducidad,$fechaVacuna,$nota);

         if($resultado){
            $session->msg('s',"Registro Exitoso.");
            redirect('clinica.php', false);
         }else{
            $session->msg('d',' Lo siento, registro falló.');
            redirect('vacuna.php?idMas='.$idMasc, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('vacuna.php?idMas='.$idMasc,false);
      }
   }
   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre = $mascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

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
               <span>Vacuna de:</span>
               <span><?php echo $nombre ?></span>
            </strong>     
         </div>
         <br>
         <br>
      </div>
      <form name="form1" method="post" action="vacuna.php">
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Responsable:</label>
            <div class="col-sm-10">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona responsable</option>
                  <?php  foreach ($encargados as $id): ?>
                  <option value="<?php echo $id['username'] ?>">
                  <?php echo $id['name'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Vacuna:</label>
            <div class="col-sm-10">
               <select class="form-control" name="vacuna">
                  <option value="">Selecciona vacuna</option>
                  <?php  foreach ($vacunas as $id2): ?>
                  <option value="<?php echo $id2['nombre'] ?>">
                  <?php echo $id2['nombre'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Fecha de caducidad:</label>
            <div class="col-sm-9">
               <input type="date" name="fechaCaducidad">
            </div>
         </div>
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Fecha sig. Vacuna:</label>
            <div class="col-sm-9">
               <input type="date" name="fechaVacuna">
            </div>
         </div>
         <div class="form-group row">
            <div class="input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <p><textarea name="nota" class="form-control" placeholder="Nota" maxlength="500" rows="5" style="resize: none"></textarea></p>
            </div>
         </div>
         <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Día:</label>
            <div class="col-sm-10">
               <strong><?php echo $time2."  ".$time1;?></strong>
            </div>
         </div>
         <input type="hidden" value="<?php echo $idmas ?>" name="id">
         <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
         <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
         <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
      </form>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
