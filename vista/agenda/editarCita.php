<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar cita';
   page_require_level(3);
   ini_set ('date.timezone','America/Mexico_City');
   
   $fecha_actual = date('Y-m-d',time());
   $encargados =   find_all('users');
   $cita =         find_by_id('cita',(int)$_GET['id']);

   $idCitaOrig =   $cita['id'];
   $idMas =        $cita['idMascota'];
   $horaCitaOrig = $cita['hora'];
   $notaOrig =     $cita['nota'];
   $idEvent =      $cita['idEvent'] ?: '';

   $mascota =      buscaRegistroPorCampo('Mascotas','idMascotas',$idMas);
   $nombre =       $mascota['nombre'];

   $responsable  = (isset($_POST['responsable'])) ? remove_junk($db->escape($_POST['responsable'])):'';
   $fecha  =       (isset($_POST['fecha']))       ? remove_junk($db->escape($_POST['fecha'])):'';
   $nota  =        (isset($_POST['nota']))        ? remove_junk($db->escape($_POST['nota'])):'';
   $hora =         (isset($_POST['hora']))        ? remove_junk($db->escape($_POST['hora'])):'';
   $horaCita =     date("H:i:s", strtotime ($hora));
?>
<!-- Agenda la Cita SOLO en Google Calendar -->
<?php
   if (isset($_POST['agendarCalendar']) && $_POST['agendarCalendar'] === "1") {
      $req_fields = array('fecha','responsable','hora');
      validate_fields($req_fields);

      if (empty($errors) && !$cliente->isAccessTokenExpired()) {
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

         if($eventoCreado) {
            $idEvent = $eventoCreado->getId();
            $resultado = actCita($responsable, $fecha, $nota, $horaCita, $fecha_actual, $idCitaOrig, $idEvent);

            $session->msg("i","La cita se agregó correctamente a su cuenta de Google Calendar \"".$_SESSION['mailUsuario']."\".");
            echo '<script> window.location="citas-mensuales.php";</script>';
         }else {
            $session->msg("i","¡ERROR! No se pudo agendar esta cita en Google Calendar");
            echo '<script> window.location="citas-mensuales.php";</script>';
         }
      } elseif (!empty($errors)) {
         $session->msg("d",$errors);
         //redirect("editarCita?id=".$cita['id'],false);
         echo "<script> window.location='editarCita?id=".$cita['id']."';</script>";
      }
   }
?>
<!-- Elimina la cita SOLO de Google Calendar -->
<?php
   if (isset($_POST['eliminarCalendar']) && $_POST['eliminarCalendar'] === "1") {

      $resultado = actRegistroPorCampo('cita','idEvent',NULL,'id',$idCitaOrig);
      
      if ( !$cliente->isAccessTokenExpired() && $resultado) {
         $cliente->setAccessToken($_SESSION['GoogleLoginToken']);
         $servicioCalendario = new Google_Service_Calendar($cliente);
         try {
            $servicioCalendario->events->delete('primary',$idEvent);
         } catch (Google_Service_Exception $e) {
            $session->msg("i","¡ERROR! al eliminar el evento de Google Calendar: ".$e->getMessage());
            echo '<script> window.location="citas-mensuales.php";</script>';
         }
      }
      $session->msg("i","Cita eliminada correctamente de su cuenta de Google Calendar \"".$_SESSION['mailUsuario']."\"");
      echo '<script> window.location="citas-mensuales.php";</script>';
   }
?>
<!-- Actualiza la cita en sistema y la agenda en Google Calendar -->
<?php
   if (isset($_POST['agendaEdita']) && $_POST['agendaEdita'] === "1") {
      $req_fields = array('fecha', 'responsable', 'hora');
      validate_fields($req_fields);

      if (empty($errors)) {
         $fechaFin = date('Y-m-d', strtotime($fecha));
         $iniCita =  new DateTime("$fechaFin $horaCita");
         $finCita =  clone $iniCita;
         $finCita->modify('+30 minutes');
         $servicioCalendario = new Google_Service_Calendar($cliente);

         $consCita = buscaCita($responsable,$fecha,$horaCita);
         $idCita =   "";

         if ($consCita != null)
            $idCita = $consCita['id'];//La fecha y hora está disponible
         if ($idCita == "") {
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
            $idEvent =      $eventoCreado->getId();

            $resultado = actCita($responsable, $fecha, $nota, $horaCita, $fecha_actual, $idCitaOrig,$idEvent);

            if($resultado && $idEvent){
               $session->msg('i',"¡EXITO!, La cita fue actualizada en sistema y agendada en Google Calendar. \"".$_SESSION['mailUsuario']."\"");
               echo '<script> window.location="citas-mensuales.php";</script>';
            } else {
               $session->msg('i','Lo siento. Algo salió mal.');
               //redirect('editarCita.php?id='.$idCitaOrig, false);
               echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
            }
         } else {
            if ($notaOrig != $nota) {
               $evento = new Google_Service_Calendar_Event(array(
                  'summary' => 'Cita',
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
               $idEvent =      $eventoCreado->getId();

               if ($eventoCreado) {
                  actRegistroPorCampo('cita','nota',$nota,'id',$idCitaOrig);
                  actRegistroPorCampo('cita','idEvent',$idEvent,'id',$idCitaOrig);

                  $session->msg('i',"¡EXITO!, La cita fue actualizada en sistema y agendada en Google Calendar. \"".$_SESSION['mailUsuario']."\"");
                  echo '<script> window.location="citas-mensuales.php";</script>';
               } else {
                  $session->msg('i','Lo siento. Algo salió mal.');
                  //redirect('editarCita.php?id='.$idCitaOrig, false);
                  echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
               }
            } else {
               $session->msg('d','Lo siento, día y Hora ya agendada.');
               //redirect('editarCita.php?id='.$idCitaOrig, false);
               echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
            }
         }
      } else {
         $session->msg("d", $errors);
         //redirect('editarCita.php?id='.$idCitaOrig,false);
         echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
      }
   }
?>
<!-- Edita la cita tanto del sistema como de Google Calendar -->
<?php
   if (isset($_POST['editarCalendarSis']) && $_POST['editarCalendarSis'] === "1") {
      $req_fields = array('fecha', 'responsable', 'hora');
      validate_fields($req_fields);

      if (empty($errors)) {
         $idCita =   "";
         $consCita = buscaCita($responsable,$fecha,$horaCita);

         $servicioCalendario = new Google_Service_Calendar($cliente);
         $evento = $servicioCalendario->events->get('primary', $idEvent);

         if ($consCita != null)
            $idCita= $consCita['id'];
         if ($idCita == "") {
            if (isset($_SESSION['GoogleLoginToken']) && $idEvent != '') {
               try {
                  $nuevaFecha =   date('Y-m-d', strtotime($fecha));
                  $nuevoIniCita = new DateTime("$nuevaFecha $horaCita");
                  $nuevoFinCita = clone $nuevoIniCita;
                  $nuevoFinCita->modify('+30 minutes');
                  
                  $evento->setDescription($nota);
                  $evento->getStart()->setDateTime($nuevoIniCita->format('Y-m-d\TH:i:s'));
                  $evento->getEnd()->setDateTime($nuevoFinCita->format('Y-m-d\TH:i:s'));
                  $evento->setSummary("Cita para: ".$nombre.". Agendada por: ".$responsable);
                  
                  $eventoActualizado = $servicioCalendario->events->update('primary', $evento->getId(), $evento);
                  if ($eventoActualizado) {
                     actCita($responsable, $fecha, $nota, $horaCita, $fecha_actual, $idCitaOrig,'');
                     $session->msg('i', "Registro Exitoso Las citas se actualizaron en el sistema y en su cuenta de Google Calendar \"".$_SESSION['mailUsuario']."\".");
                     echo '<script> window.location="citas-mensuales.php";</script>';  
                  }
               } catch (Google_Service_Exception $e) {
                  $session->msg("i","Error al actualizar el evento en Google Calendar: ".$e->getMessage());
                  //redirect ('editarCita.php?id='.$idCitaOrig, false);  
                  echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
               }
            }
         } else {
            if ($notaOrig != $nota) {
               $actCita = actRegistroPorCampo('cita', 'nota', $nota, 'id', $idCitaOrig);

               if ($actCita) {
                  if (isset($_SESSION['GoogleLoginToken']) && $idEvent != '') {
                     try {
                        $servicioCalendario = new Google_Service_Calendar($cliente);
                        $evento =             $servicioCalendario->events->get('primary', $idEvent);
                        $evento->setDescription($nota);
                        $eventoActualizado = $servicioCalendario->events->update('primary', $evento->getId(), $evento);
                     } catch (Google_Service_Exception $e) {
                        echo "Error al actualizar el evento en Google Calendar: ".$e->getMessage();
                     }
                  }
                  $session->msg('i', "Se actualizó la nota de la cita en sistema y en Google Calendar. \"".$_SESSION['mailUsuario']."\".");
                  echo '<script> window.location="citas-mensuales.php";</script>';
               } else {
                  $session->msg('d', 'Lo siento, falló el registro.');
                  //redirect ('editarCita.php?id=' . $idCitaOrig, false);
                  echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
               }
            } else {
               $session->msg('d', 'Lo siento, día y Hora ya agendada.');
               //redirect('editarCita.php?id=' . $idCitaOrig, false);
               echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
            }
         }
      } else {
         $session->msg("d", $errors);
         //redirect('editarCita.php?id=' . $idCitaOrig, false);
         echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
      }
   }
?>
<!-- Actualiza la cita en sistema (Cuando la sesión de Google esta abierta o cerrada) -->
<?php
   if(isset($_POST['consulta'])) {
      $req_fields = array('fecha','responsable','hora');
      validate_fields($req_fields);

      if (empty($errors)) {
         $idCita =       "";
         $consCita = buscaCita($responsable,$fecha,$horaCita);

         if ($consCita != null)
            $idCita= $consCita['id'];
         if ($idCita == ""){
            $resultado = actCita($responsable, $fecha, $nota, $horaCita, $fecha_actual, $cita['id'], '');

            if($resultado){
               $session->msg('s',"La cita se cambió en el sistema con éxito.");
               echo '<script> window.location="citas-mensuales.php";</script>';
            } else {
               $session->msg('d','Lo siento, falló el registro.');
               //redirect('editarCita.php?id='.$idCitaOrig, false);
               echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
            }
         } else {
            if ($notaOrig != $nota) {
               $actCita = actRegistroPorCampo('cita','nota',$nota,'id',$cita['id']);

               if ($actCita) {
                  $session->msg('s',"La cita se cambió en el sistema con éxito.");
                  echo '<script> window.location="citas-mensuales.php";</script>';
               } else {
                  $session->msg('d','Lo siento, falló el registro.');
                  //redirect('editarCita.php?id='.$idCitaOrig, false);
                  echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
               }
            } else {
               $session->msg('d','Lo siento, día y Hora ya agendada.');
               //redirect('editarCita.php?id='.$idCitaOrig, false);
               echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
            }
         }
      } else {
         $session->msg("d", $errors);
         //redirect('editarCita.php?id='.$idCitaOrig,false);
         echo "<script> window.location='editarCita?id='.$idCitaOrig';</script>";
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>
<script type="text/javascript">
   function agendarCalendar(){
      document.form1.action = "editarCita.php?id=<?php echo $idCitaOrig ?>";
      document.form1.agendarCalendar.value = "1";
      document.form1.submit();
   }
   function eliminarDeCalendar(){
      document.form1.action = "editarCita.php?id=<?php echo $idCitaOrig ?>";
      document.form1.eliminarCalendar.value = "1";
      document.form1.submit();
   }
   function editarCalendarSis(){
      document.form1.action = "editarCita.php?id=<?php echo $idCitaOrig ?>";
      document.form1.editarCalendarSis.value = "1";
      document.form1.submit();
   }
   function agendarActualizar(){
      document.form1.action = "editarCita.php?id=<?php echo $idCitaOrig ?>";
      document.form1.agendaEdita.value = "1";
      document.form1.submit();
   }
</script>

<!DOCTYPE html>
<html>
<head>
   <title>Edición de Cita</title>
</head>
<body onload="horasEdicionCita();">

<form name="form1" method="post" action="editarCita.php?id=<?php echo (int)$cita['id'] ?>">
<!-- Mensaje -->
<div class="row col-md-8">
   <?php echo display_msg($msg); ?>
</div>
<!-- Panel para editar la cita en el sistema -->
<div class="row col-md-8">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span> Editar cita de : <?php echo $nombre ?> </span>
         </strong>     
      </div>
      <div class="panel-body">
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Responsable:</label>
            <div class="col-sm-5">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona responsable</option>
                  <?php  foreach ($encargados as $resp): ?>
                     <option value="<?php echo $resp['username']; ?>" <?php if($cita['responsable'] === $resp['username']): echo "selected"; endif; ?> >
                           <?php echo remove_junk($resp['name']); ?>
                     </option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Fecha de cita:</label>
            <div class="col-sm-6">
               <input type="date" name="fecha" min="<?php echo $fecha_actual ?>" onchange="horasEdicionCita();" value="<?php echo remove_junk($cita['fecha_cita']); ?>">
            </div>
         </div>
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Hora de cita:</label>
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
                  <textarea name="nota" class="form-control" placeholder="Nota" maxlength="200" rows="2" style="resize: none"><?php echo remove_junk($cita['nota']); ?></textarea>
               </div>
            </div>
         </div>
         <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Día:</label>
            <div class="col-sm-10">
               <strong><?php echo $time2."  ".$time1;?></strong>
            </div>
         </div>
         <?php if ($cliente->isAccessTokenExpired()): ?>
            <button type="submit" name="consulta" class="btn btn-danger">Actualizar</button>
         <?php endif; ?>
         <input type="hidden" value="<?php echo $idCitaOrig ?>" name="idCita">
         <input type="hidden" name="horaAux" value="<?php echo $horaCitaOrig ?>">
         <input type="hidden" name="notaOrig" value="<?php echo $notaOrig ?>">   
      </div>
   </div>
</div>
<!-- Menú de opciones para Google Calendar -->
<?php if (!$cliente->isAccessTokenExpired()):?>
   <div class="row col-md-4">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-plus"></span>
               <span> opciones </span>
            </strong>   
         </div>
         <div class="panel-body">
            <?php if (!$cliente->isAccessTokenExpired() && ($cita['idEvent'] == null )): ?>
               <p>¡Vaya! Esta cita no está agendada en su cuenta Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo">.</p>
            <?php endif ?>
            <div class="form-group row">
               <div class="col-md-12">
                  <label>¿Qué le gustaría hacer?</label>
               </div>
            </div>
            <?php if (!$cliente->isAccessTokenExpired() && $cita['idEvent'] == null) { ?>
               <div class="form-group row">
                  <div class="col-md-12">
                     <a href="#" class="google-sync-button" onclick="agendarCalendar();">
                        Agendar esta cita a Google Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo">
                     </a>
                  </div>
               </div>
               <div class="form-group row">
                  <div class="col-md-12">
                     <a href="#" class="google-sync-button" onclick="agendarActualizar();">
                        Actualizar cita en sistema y Agendar en Google Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo">
                     </a>
                  </div>
               </div>
            <?php } elseif (!$cliente->isAccessTokenExpired() && $cita['idEvent'] != null) { ?>
               <div class="form-group row">
                  <div class="col-md-12">
                     <a href="#" class="google-sync-button" onclick="editarCalendarSis();">
                        Actualizar en el sistema y en Google Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo">
                     </a>
                  </div>
               </div>
               <div class="form-group row">
                  <div class="col-md-12">
                     <a href="#" class="google-sync-button" onclick="eliminarDeCalendar();">
                        Eliminar de Google Calendar <img src="../../libs/imagenes/Calendar.png" class="google-logo">
                     </a>
                  </div>
               </div>
            <?php } ?>
            <div class="form-group row">
               <div class="col-md-12">
                  <button type="submit" name="consulta" class="btn btn-danger">Actualizar Cita en Sistema</button>
               </div>
            </div>
         </div>
         <input type="hidden" name="agendarCalendar"   value="0">
         <input type="hidden" name="editarCalendarSis" value="0">
         <input type="hidden" name="eliminarCalendar"  value="0">
         <input type="hidden" name="agendaEdita"       value="0">
      </div>
   </div>
<?php endif; ?>

</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>