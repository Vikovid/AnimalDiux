<?php 
	require_once("../../modelo/load.php");

	$url = $_GET['url'];

	unset($_SESSION['GoogleLoginToken']);
	unset($_SESSION['mailUsuario']);
	
	$session->msg("s","Sesion Google cerrada con exito.");
	redirect($url,false);
?>