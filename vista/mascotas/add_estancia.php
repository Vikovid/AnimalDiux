<?php
   require_once('../../modelo/load.php');
   $page_title = 'Estancia';
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $encargados = find_all('users');
 
   $idmas= isset($_GET['idMas']) ? $_GET['idMas']:'';
 
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   if(isset($_POST['responsable'])){
      $idMasc  = remove_junk($db->escape($_POST['id']));
      $req_fields = array('hora_salida','fecha_salida','responsable');
      validate_fields($req_fields);
      if(empty($errors)){
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $horaSalida  = remove_junk($db->escape($_POST['hora_salida']));
         $fechaSalida  = remove_junk($db->escape($_POST['fecha_salida']));
         $id  = remove_junk($db->escape($_POST['id']));
         $nota  = remove_junk($db->escape($_POST['nota']));

         $resultado = altaEstancia($id,'Estancia','0',$responsable,$horaSalida,$fechaSalida,$fecha_actual,$nota);

         altaHistEstancia($id,'Estancia',$responsable,$fecha_actual,$horaSalida,$fechaSalida);

         if($resultado){
            $session->msg('s',"Registro Exitoso. ");
            redirect('clinica.php', false);
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('add_estancia.php?idMas='.$idMasc, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('add_estancia.php?idMas='.$idMasc,false);
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
               <span>Estancia de:</span>
               <span><?php echo $nombre ?></span>
            </strong>     
         </div>
         <br>
         <br>
      </div>
      <form name="form1" method="post" action="add_estancia.php">
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Responsable:</label>
            <div class="col-sm-10">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona Responsable</option>
                  <?php  foreach ($encargados as $id): ?>
                  <option value="<?php echo $id['name'] ?>">
                  <?php echo $id['name'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Hora de salida:</label>
            <div class="col-sm-10">
               <select class="form-control" name="hora_salida">
                  <option>10:00</option> 
                  <option>10:30</option>
                  <option>11:00</option>
                  <option>11:30</option>
                  <option>12:00</option>
                  <option>12:30</option>
                  <option>13:00</option>
                  <option>13:30</option>
                  <option>14:00</option>
                  <option>14:30</option>
                  <option>15:00</option> 
                  <option>15:30</option>
                  <option>16:00</option>
                  <option>16:30</option>
                  <option>17:00</option>
                  <option>17:30</option>
                  <option>18:00</option>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Fecha de salida:</label>
            <div class="col-sm-10">
               <input type="date" name="fecha_salida">
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
         <input type="hidden" value="<?php echo $idmas ?>" name="id">
         <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
         <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
         <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
      </form>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
