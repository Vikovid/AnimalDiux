<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $proveedor = buscaRegistroPorCampo("proveedor","idProveedor",$_GET['idProveedor']);
  if(!$proveedor){
    $session->msg("d","idProveedor vacío");
    redirect('proveedores.php');
  }

  $resultado = borraRegistroPorCampo("proveedor","idProveedor",$proveedor['idProveedor']);

  if($resultado){
     $session->msg("s","Proveedor eliminado");
     redirect('proveedores.php');
  }else{
     $session->msg("d","Falló la eliminación");
     redirect('proveedores.php');
  }
?>
