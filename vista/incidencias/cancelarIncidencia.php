<?php 
	require_once("../../modelo/load.php");
	page_require_level(3);

	$parametros = find_by_id('parametros','1');
	$BDCondor = $parametros['BDCondor'];

	$secDB = new MySqli_DB($BDCondor);

		ini_set ('date.timezone','America/Mexico_City');
		$fechaRes =	date('Y/m/d',time());
		$horaRes =	date('H:i',time());

		$incidencia = buscaRegistroPorCampo2('incidencias','id',$_GET['idIncidencia'],$BDCondor);

		if ($incidencia['idEstatus'] == 1) {
			$resultado = cancelaIncidencia($incidencia['id'],$fechaRes,$horaRes);

			if ($resultado)
				$session->msg("s","Incidencia cancelada exitosamente.");
			else
				$session->msg("d","Error, no se pudo cancelar la incidencia.");
		} else
			$session->msg("d","Esta Incidencia no es posible cancelarla.");
	
	$secDB->db_disconnect();
	$secDB = null;

	redirect("incidencias.php");
?>