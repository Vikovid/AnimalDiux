<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  if(isset($_GET['id'])) {
     $id = $_GET['id'];
     $resultado = delete_by_id('products',$id);
     if(!$resultado) {
        die("Query Failed.");
     }

     $session->msg("s","Producto eliminado correctamente");
     redirect('product.php');
  } 
?>
