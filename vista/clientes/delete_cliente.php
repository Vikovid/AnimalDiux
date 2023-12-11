<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  if(isset($_GET['IdCredencial'])) {
     $id = $_GET['IdCredencial'];

     $consMascota = buscaRegistroPorCampo('mascotas','idCliente',$id);
     $idMascota = $consMascota['idMascotas'];

     if ($consMascota > 0){
        $session->msg("d","El Cliente tiene mascotas registradas.");
        redirect('cliente.php');
     }else{
        $resultado = borraRegistroPorCampo("cliente","idcredencial",$id);
  
        if(!$resultado) {
           die("falló la eliminación.");
        }
        $session->msg("s","Cliente eliminado correctamente.");
        redirect('cliente.php');
     }
  } 
?>
