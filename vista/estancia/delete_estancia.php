<?php
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(3);

   if(isset($_GET['id'])) {
      $id = $_GET['id'];

      $resultado = borraRegistroPorCampo('estancia','idEstancia',$id);
      
      if(!$resultado) {
         die("Falló la eliminación.");
      }
      $session->msg("s","Estancia eliminada correctamente");
      redirect('estancia.php');
   } 
?>
