<?php
	$page_title = 'Detalles de la incidencia';
	require_once ('../../modelo/load.php');

	// Checkin What level user has permission to view this page
	page_require_level(3);

	$parametros = find_by_id('parametros','1');
	$BDCondor = $parametros['BDCondor'];

	$secDB = new MySqli_DB($BDCondor);

		$idIncidencia = $_GET['idIncidencia'];
		$incidencia = detallesIncidencia($idIncidencia);

	$secDB->db_disconnect();
	$secDB = null;
?>

<?php include_once ('../layouts/header.php'); ?>

<script type="text/javascript">
	function cancelarIncidencia(idIncidencia){
		var confirmacion = window.confirm("¿Está segur@ que desea cancelar esta incidencia?");

		if (confirmacion)
			window.location.href = "cancelarIncidencia.php?idIncidencia="+idIncidencia;
		else
			return -1;
	}
</script>
<style type="text/css">
	img {
		height: 100%;
		width: 100%;
	}
</style>

<div class="row col-md-9">
	<?php echo display_msg($msg); ?>
</div>
<div class="row col-md-9">
	<div class="panel panel-default">
		<div class="panel-heading clearfix">
			<span class="glyphicon glyphicon-plus"></span>
			<strong> Detalles de la incidencia</strong>
		</div>
		<div class="panel-body clearfix">
			<!-- Quien reportó la incidencia -->
			<div class="form-group">
				<div class="form-control">
					<span class="glyphicon glyphicon-user"></span>
					<label>Incidencia reportada por:</label>
				</div>
				<table class="table">
					<tr>
						<th style="width: 30%;">Usuario:</th>
						<td><?php echo $incidencia['usuario'] ?></td>
					</tr>
					<tr>
						<th style="width: 30%;">Fecha:</th>
						<td><?php echo date('d-m-Y',strtotime($incidencia['fecha'])) ?></td>
					</tr>
					<tr>
						<th style="width: 30%;">Hora:</th>
						<td><?php echo $incidencia['hora'].' hrs.' ?></td>
					</tr>
				</table>
			</div>
			<!-- Detalles de la incidencia -->
			<div class="form-group">
				<div class="form-control">
					<span class="glyphicon glyphicon-file"></span>
					<label>Detalles de la Incidencia:</label>
				</div>
				<textarea name="detalles" class="form-control" maxlength="1000" rows="2" style="height: 200px; width: 100%; resize: none;" readonly><?php echo $incidencia['detalles'] ?></textarea>
			</div>
			<!-- Evidencia -->
			<?php if (!empty($incidencia['evidencias'])): ?>
			<div class="form-group">
				<div class="form-control">
					<span class="glyphicon glyphicon-paperclip"></span>
					<label>Evidencia enviada</label>
				</div>
				<table class="table table-bordered">
					<thead><tr><td>
						<?php if (esPDF($incidencia['evidencias'])): ?>
							<embed src="data:application/pdf;base64,<?php echo base64_encode($incidencia['evidencias']); ?>" width="100%" height="500px" type="application/pdf">
						<?php elseif (esImagen($incidencia['evidencias'])): ?>
							<img src="data:image/jpeg;base64,<?php echo base64_encode($incidencia['evidencias']) ?>">
						<?php endif; ?>
					</td></tr></thead>
				</table>
			</div>
			<?php endif; ?>
			<!-- Respuesta de la empresa. -->
			<?php if ($incidencia['idEstatus'] != 1): ?>
				<div class="form-group">
					<div class="form-control">
						<span class="glyphicon glyphicon-envelope"></span>
						<label>Respuesta:</label>
					</div>
					<textarea name="mensaje" class="form-control" maxlength="1000" rows="2" style="height: 200px; width: 100%; resize: none;" readonly><?php echo $incidencia['respuesta'] ?></textarea>
				</div>
				<div class="form-group">
					<div class="form-control">
						<span class="glyphicon glyphicon-user"></span>
						<label>Incidencia atendida por:</label>
					</div>
					<table class="table table-bordered">
						<tr>
							<th style="width: 25%;">Usuario:</th>
							<td><?php echo $incidencia['representante'] ?></td>
						</tr>
						<tr>
							<th style="width: 25%;">Fecha:</th>
							<td><?php echo date('d-m-Y',strtotime($incidencia['fechaRes'])) ?></td>
						</tr>
						<tr>
							<th style="width: 25%;">Hora:</th>
							<td><?php echo $incidencia['horaRes'].' hrs' ?></td>
						</tr>
					</table>
				</div>
			<?php else: ?>
				<div class="form-group">
					<div class="form-control text-center">
						<span class="glyphicon glyphicon-info-sign"></span>
						<label>Esta incidencia aún no ha sido atendida ¿Desea cancelarla?</label>
					</div>
					<div class="form-control text-center">
						<a href="#" onclick="cancelarIncidencia(<?php echo $idIncidencia ?>);" class="btn btn-danger btn-xs" title="Cancelar Incidencia" data-toggle="tooltip">
							<span class="glyphicon glyphicon-remove"></span>
						</a>
					</div>
				</div>
			<?php endif; ?>
			<!-- Estatus -->
			<div class="form-group">
				<div class="form-control">
					<span class="glyphicon glyphicon-file"></span>
					<label>Status:</label>
				</div>
				<table class="table table-bordered">
					<tr>
						<th style="width: 50%">
							Status:
						</th>
						<td style="width: 50%">
							<?php echo $incidencia['estatus'] ?>
						</td>
					</tr>
				</table>
			</div>
			<!-- Regresar -->
			<div class="form-group">
				<a href="incidencias.php" class="btn btn-danger">Regresar</a>
			</div>
		</div>
	</div>
</div>

<?php include_once ('../layouts/footer.php'); ?>