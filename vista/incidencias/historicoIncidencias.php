<?php
	$page_title = 'Histórico de Incidencias';
	require_once ('../../modelo/load.php');

	// Checkin What level user has permission to view this page
	page_require_level(3);

	$usuarios = find_all_user();
	$meses = array("01"=>"Enero",
						"02"=>"Febrero",
						"03"=>"Marzo",
						"04"=>"Abril",
						"05"=>"Mayo",
						"06"=>"Junio",
						"07"=>"Julio",
						"08"=>"Agosto",
						"09"=>"Septiembre",
						"10"=>"Octubre",
						"11"=>"Noviembre",
						"12"=>"Diciembre"
	);

	$parametros = find_by_id('parametros','1');
	$BDCondor = $parametros['BDCondor'];

	$secDB = new MySqli_DB($BDCondor);

		$usuario =		current_user();
		$idSucursal =	$usuario['idSucursal'];

		$empresa =		buscaRegistroPorCampo('sucursal','idSucursal',$idSucursal);
		$idEmpresa  =	$empresa['idSucursal'];
		$nomEmpresa =	$empresa['nom_sucursal'];

		$usuarioReporta = (isset($_POST['usuario']) && $_POST['usuario']!='') ? $_POST['usuario']:'';
		$idEstatus = (isset($_POST['estatus']) && $_POST['estatus']) ? $_POST['estatus']:'';
		$anio =	(isset($_POST['anio']) && $_POST['anio'] != '') ? $_POST['anio']:date('Y') ;
		$mes =	(isset($_POST['mes']) && $_POST['mes'] != '') ? $_POST['mes']:date('m');

		$fechaIni = date('Y-m-d',strtotime($anio.'-'.$mes.'-'.'01'));
		$fechaFin = date('Y-m-t',strtotime($fechaIni));

		$historico = historicoIncidencias($idEmpresa,$nomEmpresa,$usuarioReporta,$fechaIni,$fechaFin,$idEstatus);
	
	$secDB->db_disconnect();
	$secDB =	null;
?>

<?php include_once ('../layouts/header.php'); ?>

<form name="form1" method="post" action="historicoIncidencias.php">
	<div class="row col-md-12">
		<?php echo display_msg($msg); ?>
		<?php echo "Histórico de incidencias de: ".date('d-m-Y',strtotime($fechaIni))." al ".date('d-m-Y',strtotime($fechaFin)); ?>
	</div>
	<div class="row col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<div class="form-group">
					<span class="glyphicon glyphicon-folder-open"></span>
					<strong> &nbsp;Incidencias Atendidas</strong>
				</div>
				<div class="form-group">
					<div class="col-md-3">
						<input type="text" class="form-control" placeholder="Usuario" name="usuario">
					</div>
					<div class="col-md-3">
						<select class="form-control" name="estatus">
							<option value="">  Estatus   </option>
							<option value="3"> Resuelto </option>
							<option value="4"> Cancelado</option>
						</select>
					</div>
					<div class="col-md-2">
						<select class="form-control" name="anio">
							<option value="">Año</option>
							<?php $i = (int)2020; while ( $i <= 2040):?>
								<option value="<?php echo $i; ?>"><?php echo $i ?></option>
							<?php $i++; endwhile; ?>
						</select>
					</div>
					<div class="col-md-2">
						<select class="form-control" name="mes">
							<option value="">Mes</option>
							<?php foreach ($meses as $mesNum => $mesVal):?>
								<option value="<?php echo $mesNum ?>"><?php echo $mesVal ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-2">
						<input type="submit" class="btn btn-info" value="Buscar">
					</div>
				</div>
			</div>
			<div class="panel-body clearfix">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th style="width: 40%;"> Usuario </th>
							<th class="text-center" style="width: 20%;"> Fecha   </th>
							<th class="text-center" style="width: 15%;"> Hora    </th>
							<th class="text-center" style="width: 15%;"> Status  </th>
							<th class="text-center" style="width: 10%;"> Detalles</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($historico as $incidencias): ?>	
						<tr>
							<td><?php echo $incidencias['usuario'] ?></td>
							<td class="text-center"><?php echo date('d-m-Y',strtotime($incidencias['fechaRes'])); ?></td>
							<td class="text-center"><?php echo $incidencias['horaRes'].' hrs'; ?></td>
							<td class="text-center"><?php echo $incidencias['estatus'] ?></td>
							<td class="text-center">
								<a href="detallesHistoricoIncidencias.php?idIncidencia=<?php echo $incidencias['id'] ?>" class="btn btn-success btn-xs" title="Detalles" data-toggle="tooltip">
									<span class="glyphicon glyphicon-list-alt"></span>
								</a>
							</td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</form>

<?php include_once ('../layouts/footer.php'); ?>