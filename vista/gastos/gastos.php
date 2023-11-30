<?php
  	$page_title = 'Lista de gastos';
  	require_once('../../modelo/load.php');
  	page_require_level(1);

  	$categorias = categorias();
	$subcategorias = array();

   $meses = array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');

  	$factura = 	"";
  	$regCat = 	"";
  	$regSubCat ="";
  	$mes = 		"";
  	$anio = 		"";
  	$dia = 		"";

  	if(isset($_POST['factura']))
    	$factura =  remove_junk($db->escape($_POST['factura']));
   if(isset($_POST['categoria'])){
      $regCat = 	remove_junk($db->escape($_POST['categoria']));
      if ($regCat != '')
         $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$regCat);
   }
   if(isset($_POST['subcategoria']))
      $regSubCat = remove_junk($db->escape($_POST['subcategoria']));
  	if(isset($_POST['anio']))
    	$anio =  	remove_junk($db->escape($_POST['anio']));
  	if(isset($_POST['mes']))
    	$mes =  		remove_junk($db->escape($_POST['mes']));
   if(isset($_POST['dia']))
    	$dia =  		remove_junk($db->escape($_POST['dia']));

   ini_set('date.timezone','America/Mexico_City');
   $year =  date('Y');
   $month = date('m');
   $day =   date('d');

   if ($mes == "" && $anio == "" && $dia == "") {
      $fechaInicial = $year."/01/01";
      $fechaFinal =   $year."/12/31";
   }
   if ($mes == "" && $anio == "" && $dia != "") {
      $fechaInicial = $year."/".$month."/".$dia;
      $fechaFinal =   $year."/".$month."/".$dia;
   }
   if ($mes == "" && $anio != "" && $dia == "") {
      $fechaInicial = $anio."/01/01";
      $fechaFinal =   $anio."/12/31";
   }
   if ($mes == "" && $anio != "" && $dia != "") {
      $fechaInicial = $anio."/".$month."/".$dia;
      $fechaFinal =   $anio."/".$month."/".$dia;
   }
   if ($mes != "" && $anio == "" && $dia == "") {
      $fechaInicial = $year."/".$mes."/01/";
      $numDias =      date('t', strtotime($fechaInicial));
      $fechaFinal =   $year."/".$mes."/".$numDias;
   }
   if ($mes != "" && $anio == "" && $dia != "") {
      $fechaInicial = $year."/".$mes."/".$dia;
      $fechaFinal =   $year."/".$mes."/".$dia;
   }
   if ($mes != "" && $anio != "" && $dia == "") {
      $fechaInicial = $anio."/".$mes."/01";
      $numDias =      date('t', strtotime($fechaInicial));
      $fechaFinal =   $anio."/".$mes."/".$numDias;
   }
   if ($mes != "" && $anio != "" && $dia != "") {
      $fechaInicial = $anio."/".$mes."/".$dia;
      $fechaFinal =   $anio."/".$mes."/".$dia;
   }
   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));

	if (isset($_POST['buscar']) && $_POST['buscar'] == "1") {
   	$_SESSION['factura'] = 		$factura;
   	$_SESSION['categoria'] = 	$regCat;
   	$_SESSION['subcategoria'] =$regSubCat;
   	$_SESSION['fechaIni'] = 	$fechaIni;
   	$_SESSION['fechaFin'] = 	$fechaFin;

   	if ($factura != "" || $regCat != "" || $regSubCat != "")
      	$gasto = gastosFactura($factura,
      								  $fechaIni,
      								  $fechaFin,
      								  $regCat,
      								  $regSubCat);
      else
	   	$gasto = join_gastos_table2($fechaIni,$fechaFin);

   } elseif ((isset($_POST['buscar']) && $_POST['buscar'] == "0") && 
   			 isset($_SESSION['factura']) && 
   			 isset($_SESSION['fechaIni']) && 
   			 isset($_SESSION['fechaFin']) && 
   			 isset($_SESSION['categoria']) && 
   			 isset($_SESSION['subcategoria'])) {

	   if ($_SESSION['factura'] != "" || $_SESSION['categoria'] != "" || $_SESSION['subcategoria'] != "") 
	      $gasto = gastosFactura($_SESSION['factura'],
	      							  $_SESSION['fechaIni'],
	      							  $_SESSION['fechaFin'],
	      							  $_SESSION['categoria'],
	      							  $_SESSION['subcategoria']);
	   else
		   $gasto = join_gastos_table2($_SESSION['fechaIni'],$_SESSION['fechaFin']);

   } else {
   	$_SESSION['factura'] = 		$factura;
   	$_SESSION['categoria'] = 	$regCat;
   	$_SESSION['subcategoria'] =$regSubCat;
   	$_SESSION['fechaIni'] = 	$fechaIni;
   	$_SESSION['fechaFin'] = 	$fechaFin;
   	
   	$gasto = join_gastos_table2($fechaIni,$fechaFin);
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script language="Javascript">
	function recarga(){
		document.form1.action = "gastos.php";
		document.getElementsByName("buscar")[0].value = "0";
		document.form1.submit();
	}
	function gastos(){
		document.form1.action = "gastos.php";
		document.getElementsByName("buscar")[0].value = "1";
		document.form1.submit();
	}
	function excel(){
		document.form1.action = "../excel/excelGastos.php";
		document.form1.submit();
	}
	function foco(){
	  	document.form1.factura.focus();
	}
	function diasMes() {
	  	var anio = "";
	  	var mes = "";
	  	var hoy = new Date();
	  	var dia = "";
	  	var array = [];

	  	anio = document.form1.anio.value;
	  	mes = document.form1.mes.value;

	  	if (anio == "")
	     	anio = hoy.getFullYear();
	  	if (mes == ""){
	     	mes = hoy.getMonth() + 1;
	     	if (mes < 10)
	        	mes = "0" + mes;
	  	}

	  	var numDias = new Date(anio, mes, 0).getDate();

	  	for (var d = 1;d <= numDias; d++){
	     	if (d < 10)
	       	dia = "0" + d;
	     	else
	       	dia = d;
	     	array.push(dia);
	  	}
	  	addOptions("dia", array);
	}
	function addOptions(domElement, array) {
	  	var select = document.getElementsByName(domElement)[0];
	  	var option;
	  	for (value in array) {
	     	option = document.createElement("option");
	     	option.text = array[value];
	     	select.add(option);
	  	}
	}
</script>
<body onload="foco();diasMes();">
<form name="form1" method="post" action="gastos.php">
  	<div class="row">
     	<div class="col-md-12">
       	<?php echo display_msg($msg);?>
      </div>
	   <div class="col-md-12">
	      <div class="panel panel-default">
	         <div class="panel-heading clearfix">
	            <div class="form-group">
		            <div class="col-md-2">
		               <div class="input-group">
		                  <span class="input-group-addon">
		                     <i class="glyphicon glyphicon-list-alt"></i>
		                  </span>
		                  <?php if ((isset($_POST['factura']) && $_POST['buscar'] == 0)) { ?>
									<input type="text" class="form-control" name="factura" placeholder="Factura" value="<?php echo $_POST['factura']?>" onkeydown="if (event.keyCode === 13) gastos();">
		                  <?php } else {?>
		                  	<input type="text" class="form-control" name="factura" placeholder="Factura" value="" onkeydown="if (event.keyCode === 13) gastos();">
		                  <?php } ?>
		               </div>
		            </div>
		            <div class="col-md-2">
		            	<select class="form-control" name="categoria" onchange="recarga();">
		            		<option value=""> Categoría </option>
		            		<?php foreach ($categorias as $categ): ?>
		            		<?php if ((isset($_POST['categoria']) && $_POST['categoria'] == $categ['id']) && $_POST['buscar'] == 0) { ?>
		            			<option value="<?php echo $categ['id'] ?>" selected> <?php echo utf8_encode($categ['name']) ?> </option>
		            		<?php } else {?>
		            			<option value="<?php echo $categ['id'] ?>"> <?php echo utf8_encode($categ['name']) ?> </option>
		            		<?php } ?>
		            		<?php endforeach ?>
		            	</select>
		            </div>
		            <div class="col-md-2">
		            	<select class="form-control" name="subcategoria">
		            		<option value=""> Subcategoría </option>
		            		<?php foreach ($subcategorias as $subcat): ?>
		            			<option value="<?php echo $subcat['idSubCategoria'] ?>"> <?php echo $subcat['nombre'] ?> </option>
		            		<?php endforeach ?>
		            	</select>
		            </div>
		            <div class="col-md-1">
		               <select class="form-control" name="anio" onchange="diasMes();">
		                  <option value="">Año</option>
		                  <?php $i = (int)2020; while($i <= 2040): ?>
		                   	<option value="<?php echo $i ?>"> <?php echo $i; ?> </option>
		                  <?php $i++; endwhile; ?>
		               </select>
		            </div>
		            <div class="col-md-1">
		               <select class="form-control" name="mes" onchange="diasMes();">
		                  <option value=""  >Mes</option>
		                  <?php while(key($meses)): ?>
		                    	<option value="<?php echo key($meses); ?>"> <?php echo remove_junk($meses[key($meses)]); ?> </option>
		                  <?php next($meses); endwhile;?>
		               </select>
		            </div>
		            <div class="col-md-1">
		               <select class="form-control" name="dia">
		                  <option value="">Día</option>
		               </select>                
		            </div>
		            <a href="#" onclick="gastos();" class="btn btn-primary">Buscar</a>
		            <input type="hidden" name="buscar" value="0">
		            <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>
		            <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
		         </div>
	         </div>
	        	<div class="panel-body">
	          	<table class="table table-bordered">
	            	<thead>
	              		<tr>
	                		<th class="text-center" style="width: 10%;"> Factura </th>                
	                		<th class="text-center" style="width: 9%;" > Proveedor </th>
	                		<th class="text-center" style="width: 17%;"> Descripción </th>
	                		<th class="text-center" style="width: 14%;"> Categoría </th>
	                		<th class="text-center" style="width: 11%;"> Subcategoría</th>
	                		<th class="text-center" style="width: 6%;" > Subtotal </th>
	                		<th class="text-center" style="width: 6%;" > IVA </th>
	                		<th class="text-center" style="width: 6%;" > Total </th>
	                		<th class="text-center" style="width: 8%;" > Forma de Pago </th>
	                		<th class="text-center" style="width: 8%;" > Fecha </th>
	                		<th class="text-center" style="width: 5%;" > Acciones </th>
	              		</tr>
	            	</thead>
	            	<tbody>
		              	<?php foreach ($gasto as $gasto): ?>
		              		<tr>
		                		<td> <?php echo remove_junk($gasto['factura']); ?></td>
		                		<td> <?php echo remove_junk($gasto['nom_proveedor']); ?></td>
		                		<td> <?php echo remove_junk($gasto['descripcion']); ?></td>
		                		<td> <?php echo remove_junk($gasto['name']); ?></td>
		                		<td> <?php echo remove_junk($gasto['nombre']); ?></td>
		                		<td class="text-right"><?php echo remove_junk($gasto['monto']); ?></td>
		                		<td class="text-right"><?php echo remove_junk($gasto['iva']); ?></td>
		                		<td class="text-right"><?php echo remove_junk($gasto['total']); ?></td>
		                		<td class="text-center"><?php echo remove_junk($gasto['tipo_pago']); ?></td>
		                		<td class="text-center"><?php echo date("d-m-Y", strtotime ($gasto['fecha'])); ?></td>
		                		<td class="text-center">
			                  	<div class="btn-group">
			                    		<a href="edit_gasto.php?id=<?php echo (int)$gasto['id'];?>&idProveedor=<?php echo (int)$gasto['idProveedor'];?>&idCategoria=<?php echo (int)$gasto['categoria'];?>&id_pago=<?php echo (int)$gasto['id_pago'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
			                      		<span class="glyphicon glyphicon-edit"></span>
			                    		</a>
			                     	<a href="delete_gasto.php?id=<?php echo (int)$gasto['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
			                      		<span class="glyphicon glyphicon-trash"></span>
			                    		</a>
			                  	</div>
		                		</td>
		              		</tr>
		             	<?php endforeach; ?>
	            	</tbody>
	          	</table>
	        	</div>
	      </div>
	   </div>
  	</div>
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>