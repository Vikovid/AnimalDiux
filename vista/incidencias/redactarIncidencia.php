<?php
	$page_title = 'Redatar Incidencia';
	require_once ('../../modelo/load.php');

	// Checkin What level user has permission to view this page
	page_require_level(3);

	$usuario = 	  current_user();
	$nombre = 	  $usuario['name'];
	$idSucursal = $usuario['idSucursal'];

	$parametros = find_by_id('parametros','1');
	$BDCondor = $parametros['BDCondor'];

	$sucursal = 	buscaRegistroPorCampo('sucursal','idSucursal',$idSucursal);
	$nomSucursal = $sucursal['nom_sucursal'];

	ini_set ('date.timezone','America/Mexico_City');
	$fecha = date('Y/m/d',time());
	$hora =  date('H:i',time());

	if (isset($_POST['banderaSubmit']) && $_POST['banderaSubmit'] === '1') {
		$detalles =  $_POST['mensaje'];
		$evidencia = "";

		if (is_uploaded_file($_FILES['archivo']['tmp_name'])) {
			$file_name = $_FILES['archivo']['name'];

			if ($file_name != '' || $file_name != null) {
				$file_type = $_FILES['archivo']['type'];
				list($type, $extension) = explode('/', $file_type);

				if ($extension == "pdf"  || $extension == "jpg" || 
					 $extension == "jpeg" || $extension == "png") {
					$file_tmp_name = $_FILES['archivo']['tmp_name'];
					$fp = fopen($file_tmp_name, 'r+b');
					$data = fread($fp, filesize($file_tmp_name));
					fclose($fp);

					$evidencia = $db->escape($data);

					if (empty($file_name) || empty($file_tmp_name)){
						$session->msg('d','La ubicación del archivo no se encuenta disponible.');
						redirect('add_product.php', false);
					}
				} else {
					$session->msg('d','Formato de archivo no válido.');
					redirect('add_product.php', false);
				}
			}
		}

		$secDB = new MySqli_DB($BDCondor);

			$resultado = redactarIncidencia($nombre,$fecha,$hora,$idSucursal,$nomSucursal,$detalles,1,$evidencia);

		$secDB->db_disconnect();
		$secDB = null;
		unset($secDB);

		if ($resultado) {
			$session->msg('s','Incidencia Enviada correctamente, en un momento atenderemos su problema.');
			redirect('incidencias.php',false);
		} else {
			$session->msg('d','Por el momento no es posible realizar esta operación.');
			redirect('redactarIncidencia.php',false);
		}
	}
?>

<?php include_once ('../layouts/header.php'); ?>

<script type="text/javascript">
	function redactarIncidencia(){
		let mensaje = document.form1.mensaje.value.trim();
		
		if (mensaje === '') {
			alert("Por favor redacte los detalles de la incidencia.");
			document.form1.mensaje.focus();
		} else {
			var confirmacion = window.confirm("¿Está segur@ de que desea reportar la incidencia?");

			if (confirmacion) {
				document.form1.banderaSubmit.value = "1";
				document.form1.action = "redactarIncidencia.php";
				document.form1.submit();
			} else
				return -1;
		}
	}
</script>
<style type="text/css">
	.custom-file-upload {
		background-color: #f9f9f9;
		border-radius: 	05px;
		padding-top:		40px;
		padding-bottom: 	40px;
		display: 			inline-block;
		width: 				100%;
		border: 				1px solid #ccc;
		cursor: 				pointer;
		color: 				rgb(37, 195, 143);
	}
	.custom-file-upload:hover { background-color: #e9e9e9;}
	input[type="file"] {
		display: none;
	}
</style>

<form name="form1" method="post" action="redactarIncidencia.php" enctype="multipart/form-data">
	<div class="row col-md-9">
		<?php echo display_msg($msg); ?>
	</div>
	<div class="row col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<span class="glyphicon glyphicon-pencil"></span>
				<strong> Redactar Incidencia</strong>
			</div>
			<div class="panel-body clearfix">
				<div class="form-group">
					<div class="form-control">
						<span class="glyphicon glyphicon-plus"></span>
						<strong>Por favor especifique el problema que está experimentando.</strong>
					</div>
					<textarea name="mensaje" class="form-control" maxlength="1000" rows="2" style="height: 200px; width: 100%; resize: none;"></textarea>
				</div>
				<div class="form-group">
					<div class="form-control">
						<span class="glyphicon glyphicon-file"></span>
						<label>Adjunte evidencia del problema (opcional)</label>
					</div>
					<div class="text-center">
						<input type="file" name="archivo" id="archivoInput" multiple="multiple">
						<label for="archivoInput" class="custom-file-upload">
							<i class="glyphicon glyphicon-open-file"></i> Subir Archivo
						</label>
						<p id="nombreArchivo"></p>
					</div>
				</div>

				<script type="text/javascript">
					document.getElementById("archivoInput").addEventListener("change", function () {
						var nombreArchivo = this.files[0] ? this.files[0].name : "";
						document.getElementById("nombreArchivo").textContent = nombreArchivo;
					});
				</script>

				<div class="form-group">
					<a href="#" class="btn btn-info" onclick="redactarIncidencia();">
						Enviar
						<i class="glyphicon glyphicon-send"></i>
					</a>
					<input type="hidden" name="banderaSubmit" value="0">
				</div>
			</div>
		</div>
	</div>
</form>

<?php include_once ('../layouts/footer.php'); ?>