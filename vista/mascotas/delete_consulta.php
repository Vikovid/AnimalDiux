<?php
	require_once('../../modelo/load.php');

	// Checkin What level user has permission to view this page
	page_require_level(1);

	$id = 				$_GET['idconsulta'];
	$datosConsulta =	buscaRegistroPorCampo('Consulta', 'idconsulta', $id);
	$idMascotas = 		$datosConsulta['idMascota'];

	$consMasc =			buscaClienteMascota($idMascotas);
	$idcredencial =	$consMasc['idcredencial'];

	$user =			current_user();
	$idUsuario =	$user['id'];
	$idSucursal =	$user['idSucursal'];

	ini_set('date.timezone','America/Mexico_City');
	$fecha_actual =	date('Y-m-d',time());
	$hora_actual =		date('H:i:s',time());

	$resultado =	borraRegistroPorCampo('Consulta', 'idconsulta', $id);
	$resultado2 =	altaHistConsulta($idUsuario, $idcredencial, $idMascotas, $idSucursal, "Consulta Borrada", $fecha_actual,$hora_actual);

	if ($resultado)
		$session->msg("s", "La consulta se borró exitosamente.");
	else
		$session->msg("d", "La consulta no pudo ser borrada.");

	redirect('history.php?idMascotas='.$idMascotas);
?>