<?php 
   require_once('../../modelo/load.php'); 
   $page_title = 'Agendar Cita';
   page_require_level(3);
   ini_set('date.timezone','America/Mexico_City');

   $encargados =   find_all('users');
   $idmas =        isset($_GET['idMas']) ? $_GET['idMas']:'';
   $fecha_actual = date('Y-m-d',time());
   $user =         current_user();
   $idSucursal =   $user['idSucursal'];

   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre =  $mascota['nombre'];

   if(isset($_POST['responsable'])) {
      $idMasc  =    remove_junk($db->escape($_POST['id']));
      $req_fields = array('fecha','responsable','hora');

      validate_fields($req_fields);
      
      if (empty($errors)) {
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $fecha  =       remove_junk($db->escape($_POST['fecha']));
         $nota  =        remove_junk($db->escape($_POST['nota']));
         $hora =         remove_junk($db->escape($_POST['hora']));
         $id  =          remove_junk($db->escape($_POST['id']));
         $idCita =       '';

         $horaCita = date("H:i:s", strtotime ($hora));
         $consCita = buscaCita($responsable,$fecha,$horaCita);
         $idCita =   $consCita != null ? $consCita['id'] : "";
       
         if ($idCita == "") {
            if (isset($_POST['sincronizar']) && !$cliente->isAccessTokenExpired()) {
               $fechaFin = date('Y-m-d', strtotime($fecha));
               $iniCita =  new DateTime("$fechaFin $horaCita");
               $finCita =  clone $iniCita;
               $finCita->modify('+30 minutes');

               $servicioCalendario = new Google_Service_Calendar($cliente);
               $evento = new Google_Service_Calendar_Event(array(
                  'summary' => 'Cita para: '.$nombre.". Agendada por: ".$responsable,
                  'description' => $nota,
                  'start' => array(
                     'dateTime' => $iniCita->format('Y-m-d\TH:i:s'),
                     'timeZone' => 'America/Mexico_City',
                  ),
                  'end' => array(
                     'dateTime' => $finCita->format('Y-m-d\TH:i:s'),
                     'timeZone' => 'America/Mexico_City',
                  ),
               ));

               $calendarioId = 'primary';
               $eventoCreado = $servicioCalendario->events->insert($calendarioId, $evento);

               if ($eventoCreado) {
                  $idEvent = $eventoCreado->getId();
                  $resultado = altaCitaEvent($id,$responsable,$fecha,$hora,$nota,$fecha_actual,$idSucursal,$idEvent);
               }
            } else
               $resultado = altaCita($id,$responsable,$fecha,$hora,$nota,$fecha_actual,$idSucursal);

            if ($resultado) {
               $session->msg('s',"Registro Exitoso.");
               redirect('clinica.php', false);
            } else {
               $session->msg('d','Lo siento, falló el registro.');
               redirect('cita.php?idMas='.$idMasc, false);
            }
         } else {
            $session->msg('d','Lo siento, Día y Hora ya agendada.');
            redirect('cita.php?idMas='.$idMasc, false);
         }
      } else {
         $session->msg("d", $errors);
         redirect('cita.php?idMas='.$idMasc,false);
      }
   }
?>
<?php include_once('../layouts/header.php') ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
   <title> Registro de Cita </title>
</head>
<body onload="horaInicialCita();">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agendar cita de :</span>
               <span><?php echo remove_junk($nombre); ?></span>
            </strong>     
         </div>
         <div class="panel-body">
            <form name="form1" method="post" action="cita.php?idMas=<?php echo $idmas ?>">
               <div class="form-group row">
                  <?php if ($cliente->isAccessTokenExpired()) { ?>
                     <p class="col-md-12">Recuerde que si desea agendar sus citas <b>también</b> con Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo"> es necesario que inicie sesión con Google<img src="../../libs/imagenes/Google.png" class="google-logo">.</p>
                  <?php } ?>
               </div>
               <div class="form-group row">
                  <label class="col-md-3 col-form-label">Responsable:</label>
                  <div class="col-md-5">
                     <select class="form-control" name="responsable">
                        <option value="">Selecciona responsable</option>
                        <?php  foreach ($encargados as $id): ?>
                           <option value="<?php echo $id['username'] ?>"><?php echo remove_junk($id['name']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>  
               <div class="form-group row">
                  <label class="col-md-3 col-form-label">Fecha de cita:</label>
                  <div class="col-md-5">
                     <input type="date" name="fecha" min="<?php echo $fecha_actual; ?>" onchange="horasCita();">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-md-3 col-form-label">Hora de la cita:</label>
                  <div class="col-md-5">
                     <select class="form-control" id="horasLista" name="hora">
                        <option value="">Selecciona una hora</option>
                     </select>                
                  </div>
               </div>
               <div class="form-group row">
                  <div class="col-md-8">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-th-large"></i>
                        </span>
                        <textarea name="nota" class="form-control" placeholder="Nota" maxlength="200" rows="2" style="resize: none"></textarea>
                     </div>
                  </div>
               </div>
               <div class="form-group row">
                  <label for="inputPassword" class="col-md-2 col-form-label">Día:</label>
                  <div class="col-md-10">
                     <strong><?php echo $time2."  ".$time1;?></strong>
                  </div>
               </div>
               <?php if (!$cliente->isAccessTokenExpired()): ?>
                  <label>Sincronizar con Google Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo"> </label>
                  <input type="checkbox" name="sincronizar">
               <?php endif ?>
               <a href="clinica.php" class="btn btn-primary">Regresar</a>
               <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
               
               <input type="hidden" value="<?php echo $idmas ?>" name="id">
               <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
            </form>
         </div>
      </div>
   </div>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>