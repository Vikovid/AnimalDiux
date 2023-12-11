<?php
   require_once('../../modelo/load.php');
   page_require_level(3);

   if(isset($_GET['id'])) {      

      $id =        $_GET['id'];
      $cita =      find_by_id('cita',(int)$_GET['id']);
      $idEvent =   $cita['idEvent'];

      $resultado = borraRegistroPorCampo('cita','id',$id);

      if ( !$cliente->isAccessTokenExpired() && $idEvent != '' ) {
         $cliente->setAccessToken($_SESSION['GoogleLoginToken']);
         $servicioCalendario = new Google_Service_Calendar($cliente);
         try {
            $servicioCalendario->events->delete('primary',$idEvent);
         } catch (Google_Service_Exception $e) {
            $session->msg("d","Error al eliminar el evento de Google Calendar: ".$e->getMessage());
         }
      }
      if (!$resultado)
         die("Falló la eliminación.");

      $session->msg("s","Cita eliminada correctamente");
      redirect('citas-mensuales.php');
   }
?>