<?php
	$page_title = 'Incidencias por atender';
	require_once ('../../modelo/load.php');

	// Checkin What level user has permission to view this page
	page_require_level(3);

	$usuario =		current_user();
	$nombre =		$usuario['name'];
	$idSucursal =	$usuario['idSucursal'];

	$empresa =		buscaRegistroPorCampo('sucursal','idSucursal',$idSucursal);
	$idEmpresa  =	$empresa['idSucursal'];
	$nomEmpresa =	$empresa['nom_sucursal'];

	$parametros = find_by_id('parametros','1');
	$BDCondor = $parametros['BDCondor'];

	$secDB = new MySqli_DB($BDCondor);

		$sinAtender = incidenciaSinAtender($idEmpresa,$nomEmpresa);

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

<form name="form1" method="post" action="incidencias.php">
	<div class="row col-md-12">
		<?php echo display_msg($msg); ?>
	</div>
	<div class="row col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<span class="glyphicon glyphicon-bell"></span>
				<strong> Incidencias sin atender</strong>
			</div>
			<div class="panel-body clearfix">
				<?php if (!empty($sinAtender)): ?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th style="width: 30%;"> Usuario </th>
							<th class="text-center" style="width: 20%;"> Fecha   </th>
							<th class="text-center" style="width: 20%;"> Hora    </th>
							<th class="text-center" style="width: 10%;"> Estatus  </th>
							<th class="text-center" style="width: 10%;"> Detalles</th>
							<th class="text-center" style="width: 10%;"> Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($sinAtender as $incidencia): ?>
							<tr>
								<td> <?php echo $incidencia['usuario'] ?> </td>
								<td class="text-center"><?php echo date('d-m-Y',strtotime($incidencia['fecha'])) ?></td>
								<td class="text-center"><?php echo $incidencia['hora'].' hrs' ?></td>
								<td class="text-center"><?php echo $incidencia['estatus'] ?></td>
								<td class="text-center">
									<a href="detallesIncidencias.php?idIncidencia=<?php echo $incidencia['id'] ?>" class="btn btn-success btn-xs" title="Detalles de la Incidencia" data-toggle="tooltip">
										<span class="glyphicon glyphicon-list-alt"></span>
									</a>
								</td>
								<td class="text-center">
									<?php if ($incidencia['idEstatus'] == 1): ?>
										<a href="#" onclick="cancelarIncidencia(<?php echo $incidencia['id'] ?>);" class="btn btn-danger btn-xs" title="Cancelar Incidencia" data-toggle="tooltip">
											<span class="glyphicon glyphicon-remove"></span>
										</a>
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<?php else: ?>
					<table class="table table-bordered">
						<tr><td class="text-center">
							<label>No hay incidencias por atender.</label>
						</td></tr>
					</table>
				<?php endif; ?>
			</div>
		</div>
	</div>
</form>

<?php include_once ('../layouts/footer.php'); ?>