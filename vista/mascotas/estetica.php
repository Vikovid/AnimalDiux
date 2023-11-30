<?php
   require_once('../../modelo/load.php');
   $page_title = 'Estetica';  
   // Checkin What level user has permission to view this page
   page_require_level(2);
   $encargados = find_all('users');

   $idmas= isset($_GET['idMas']) ? $_GET['idMas']:'';

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   if(isset($_POST['responsable'])){
      $idMasc  = remove_junk($db->escape($_POST['id']));
      $req_fields = array('responsable','observaciones');
      validate_fields($req_fields);
      if(empty($errors)){
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $observaciones  = remove_junk($db->escape($_POST['observaciones']));
         $hora_ent  = remove_junk($db->escape($_POST['hora_ent']));
         $id  = remove_junk($db->escape($_POST['id']));

         $resultado = altaEstetica($responsable,$observaciones,'0',$hora_ent,$fecha_actual,$id,'');

         altaEstancia($id,'Estetica','0',$responsable,$hora_ent,$fecha_actual,$fecha_actual,'');
     
         altaHistEstancia($id,'Estetica',$responsable,$fecha_actual,$hora_ent,$fecha_actual);

         if($resultado){
            $session->msg('s',"Registro Exitoso. ");
            redirect('clinica.php', false);
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('estetica.php?idMas='.$idMasc, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('estetica.php?idMas='.$idMasc,false);
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
               <span>Estética de:</span>
               <span><?php echo $nombre ?></span>
            </strong>     
         </div>
         <br>
         <br>
      </div>
      <form name="form1" method="post" action="estetica.php">
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
            <div class="input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <p><textarea name="observaciones" class="form-control" placeholder="Escribe las observaciones que te piden los clientes" maxlength="500" rows="5" style="resize: none"></textarea></p>
            </div>
         </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Hora de entrega:</label>
            <div class="col-sm-10">
               <select class="form-control" name="hora_ent">
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
