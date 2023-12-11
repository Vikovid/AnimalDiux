<?php
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(3);

   if(isset($_GET['id'])) {
      $id = $_GET['id'];
      $resultado = borraRegistroPorCampo('soluciones','id',$id);

      if(!$resultado) {
         die("falló la eliminación.");
      }

      $session->msg("s","Medicamento Inyectable eliminado correctamente");
      redirect('solucion.php');
   } 
?>
