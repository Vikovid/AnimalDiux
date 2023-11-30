<?php
require_once '../../libs/Classes/PHPExcel.php';
require_once('../../modelo/load.php');

$factura = 		isset($_POST['factura']) ? $_POST['factura']:'';
$regCat = 		isset($_POST['categoria']) ? $_POST['categoria']:'';
$regSubCat =	isset($_POST['subcategoria']) ? $_POST['subcategoria']:'';
$mes = 			isset($_POST['mes']) ? $_POST['mes']:'';
$anio = 			isset($_POST['anio']) ? $_POST['anio']:'';
$dia = 			isset($_POST['dia']) ? $_POST['dia']:'';

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

if ($factura != "" || $regCat != "" || $regSubCat != "")
   $gasto = gastosFactura($factura,
      						  $fechaIni,
      						  $fechaFin,
      						  $regCat,
      						  $regSubCat);
else
	$gasto = join_gastos_table2($fechaIni,$fechaFin);

$objPHPExcel = new PHPExcel();
$objPHPExcel->
    getProperties()
        ->setCreator("TEDnologia.com")
        ->setLastModifiedBy("TEDnologia.com")
        ->setTitle("Exportar Excel con PHP")
        ->setSubject("Documento de prueba")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("usuarios phpexcel")
        ->setCategory("reportes");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle("Gastos");

$objPHPExcel->getActiveSheet()->setCellValue('A1','FACTURA');
$objPHPExcel->getActiveSheet()->setCellValue('B1','PROVEEDOR');
$objPHPExcel->getActiveSheet()->setCellValue('C1','DESCRIPCIÓN');
$objPHPExcel->getActiveSheet()->setCellValue('D1','CATEGORÍA');
$objPHPExcel->getActiveSheet()->setCellValue('E1','SUBCATEGORÍA');
$objPHPExcel->getActiveSheet()->setCellValue('F1','SUBTOTAL');
$objPHPExcel->getActiveSheet()->setCellValue('G1','IVA');
$objPHPExcel->getActiveSheet()->setCellValue('H1','TOTAL');
$objPHPExcel->getActiveSheet()->setCellValue('I1','FORMA DE PAGO');
$objPHPExcel->getActiveSheet()->setCellValue('J1','FECHA');


$fila=2;
foreach ($gasto as $gasto){
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$gasto['factura']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$gasto['nom_proveedor']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$gasto['descripcion']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$gasto['name']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$gasto['nombre']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$gasto['monto']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$gasto['iva']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$gasto['total']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$gasto['tipo_pago']);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$gasto['fecha']);
	$fila++;
}

$objPHPExcel->getActiveSheet()->setTitle('Reporte de gastos');
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_gastos.xls"'); //nombre del documento
header('Cache-Control: max-age=0');
	
$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
$objWriter->save('php://output');
exit;
?>