<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $categorie = find_by_id('categories',(int)$_POST['idCategoria']);
  if(!$categorie){
     $session->msg("d","Falta el ID de la categoría.");
     redirect('categorias.php');
  }

  $borraSubcategorias = borraRegistrosPorCampo('subcategorias','idCategoria',$categorie['id']);
  $delete_id = delete_by_id('categories',(int)$categorie['id']);

  if($delete_id){
     $session->msg("s","Categoría eliminada.");
     redirect('categorias.php');
  }else{
     $session->msg("d","falló la eliminación.");
     redirect('categorias.php');
  }
?>
