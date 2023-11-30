<?php 
	require_once('../../modelo/load.php');

	$meses = array('01'=>'Enero',
                  '02'=>'Febrero',
                  '03'=>'Marzo',
                  '04'=>'Abril',
                  '05'=>'Mayo',
                  '06'=>'Junio',
                  '07'=>'Julio',
                  '08'=>'Agosto',
                  '09'=>'Septiembre',
                  '10'=>'Octubre',
                  '11'=>'Noviembre',
                  '12'=>'Diciembre');

	if (isset($_GET['anio']) && isset($_GET['mes']) && !$cliente->IsAccessTokenExpired()) {
		$anio = $_GET['anio'];
		$mes  = $_GET['mes'];

		$fecha_actual = date('Y/m/d');

	   $fechaIni = date('Y/m/d', strtotime($anio . "-" . $mes . "-01"));
	   $numDias = 	date('t', strtotime($fechaIni));
	   $fechaFin = date("Y/m/d", strtotime($anio . "-" . $mes . "-" . $numDias));
	   $citas = 	citasFecha($fechaIni,$fechaFin);
	   $contador = 0;

	   foreach ($citas as $cita) {
	   	if (empty($cita['idEvent'])) {
	   		$responsable = $cita['responsable'];
	   		$nombre = 	$cita['nombre'];
	   		$nota = 		$cita['nota'];
	   		$horaCita = $cita['hora'];
	   		$idCita = 	$cita['id'];

		   	$fechaFin = date('Y-m-d', strtotime($cita['fecha_cita']));
	         $iniCita =  new DateTime("$fechaFin $horaCita");
	         $finCita =  clone $iniCita;
	         $finCita->modify('+30 minutes');

	         $servicioCalendario = new Google_Service_Calendar($cliente);
	         $evento = new Google_Service_Calendar_Event(array(
	            'summary' => 'Cita para: '.$nombre.". Agendada por: ".$responsable,
	            'description' => $nota,
	            'start' => array(
	               'dateTime' => $iniCita->format('Y-m-d\TH:i:s'),
	               'timeZone' => 'America/Mexico_City',
	            ),
	            'end' => array(
	               'dateTime' => $finCita->format('Y-m-d\TH:i:s'),
	               'timeZone' => 'America/Mexico_City',
	            ),
	         ));

	         $calendarioId = 'primary';
	         $eventoCreado = $servicioCalendario->events->insert($calendarioId, $evento);

	         if ($eventoCreado) {
	         	$idEvent = $eventoCreado->getId();
	         	actCita($responsable, $fechaFin, $nota, $horaCita, $fecha_actual, $idCita, $idEvent);
	            $contador++;
	         } else {
	         	$session->msg("d","Algo salió mal. Imposible Crear el Evento.");
	         	redirect("citas-mensuales.php",false);
	         }
	      }
	   }
	   if ($contador == 0)
	   	$session->msg("i", "No hay citas por agendar en el mes de " . $meses[$mes]);
	   else
	   	$session->msg("i","¡Éxito!, se añadieron " . $contador . " citas a la cuenta de Google Calendar de: \"".$_SESSION['mailUsuario']."\" en el mes de " . $meses[$mes]);
	} else
		$session->msg("d","Imposible acceder a esta página. Acceso restringido.");

	redirect("citas-mensuales.php",false);
?>