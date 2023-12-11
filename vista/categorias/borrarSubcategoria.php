<?php
   require_once('../../modelo/load.php');

   // Checkin What level user has permission to view this page
   page_require_level(2);

   $subcategoria = find_by_id('subcategorias',(int)$_GET['id']);
   $idCategoria = $_GET['idCat'];

   if (!$subcategoria) {
      $session->msg("d","Falta el id de la subcategoria.");
      redirect('subcategorias.php?idCat='.$idCategoria);
   }

   $delete_id = delete_by_id('subcategorias',(int)$subcategoria['id']);

   if($delete_id)
      $session->msg("s","Subcategoría eliminada.");
   else
      $session->msg("d","falló la eliminación.");

   redirect('subcategorias.php?idCat='.$subcategoria['idCategoria']);
?>
