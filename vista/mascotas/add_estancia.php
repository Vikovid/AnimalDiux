<?php
   require_once ('../../modelo/load.php');
   $page_title = 'Estancia';
   
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $idmas =      isset($_GET['idMas']) ? $_GET['idMas']:'';
   $encargados = find_all('users');
 
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual = date('Y-m-d',time());

   if (isset($_POST['responsable'])) {
      $idMasc  =    remove_junk($db->escape($_POST['id']));
      $req_fields = array('hora_salida','fecha_salida','responsable');

      validate_fields($req_fields);
      if (empty($errors)) {
         $responsable = remove_junk($db->escape($_POST['responsable']));
         $horaSalida  = remove_junk($db->escape($_POST['hora_salida']));
         $fechaSalida = remove_junk($db->escape($_POST['fecha_salida']));
         $id =          remove_junk($db->escape($_POST['id']));
         $nota =        remove_junk($db->escape($_POST['nota']));

         $resultado = altaEstancia($id,'Estancia','0',$responsable,$horaSalida,$fechaSalida,$fecha_actual,$nota);
         altaHistEstancia($id,'Estancia',$responsable,$fecha_actual,$horaSalida,$fechaSalida);

         if ($resultado) {
            $session->msg('s', "Registro Exitoso.");
            redirect('history.php?idMascotas='.$idMasc, false);
         } else {
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('add_estancia.php?idMas='.$idMasc, false);
         }
      } else {
         $session->msg("d", $errors);
         redirect('add_estancia.php?idMas='.$idMasc,false);
      }
   }
   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre =  $mascota['nombre'];
?>

<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="add_estancia.php">
<div class="row col-md-9">
   <?php echo display_msg($msg); ?>
</div>
<div class="row col-md-9">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Estancia de: <?php echo $nombre ?></span>
         </strong>     
      </div>
      <div class="panel-body">
         <div class="form-group col-md-12">
            <div class="col-md-4">
               <label>Responsable:</label>
            </div>
            <div class="col-md-8">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona Responsable</option>
                  <?php  foreach ($encargados as $id): ?>
                     <option value="<?php echo $id['name'] ?>">
                        <?php echo $id['name'] ?>
                     </option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group col-md-12">
            <div class="col-md-4">
               <label>Hora de salida:</label>
            </div>
            <div class="col-md-8">
               <select class="form-control" name="hora_salida">
               <?php
                  $hora_inicio = strtotime('10:00');
                  $hora_fin =    strtotime('18:00');
                  for ($hora = $hora_inicio; $hora <= $hora_fin; $hora += 1800) { // 1800 segundos = 30 minutos
                     $hora_actual = date('H:i', $hora);
                     echo "<option>$hora_actual</option>";
                  }
               ?>
               </select>
            </div>
         </div>  
         <div class="form-group col-md-12">
            <div class="col-md-4">
               <label>Fecha de salida:</label>
            </div>
            <div class="col-md-8">
               <input type="date" name="fecha_salida">
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
            <input  type="hidden" value="<?php echo $idmas ?>" name="id">
            <input  type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
            <input  type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
            <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
         </div>
      </div>
   </div>
</div>
</form>
<?php include_once('../layouts/footer.php'); ?>