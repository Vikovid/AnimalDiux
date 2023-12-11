<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  if(isset($_GET['id'])) {
     $id = $_GET['id'];

     $resultado = borraRegistroPorCampo('Mascotas','idMascotas',$id);

     if(!$resultado) {
        die("Query Failed.");
     }

     $session->msg("s","Mascota eliminada correctamente");
     redirect('clinica.php');
  } 
?>
