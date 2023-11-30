<?php
require_once '../../libs/Classes/PHPExcel.php';
require_once('../../modelo/load.php');

$p_scu = isset($_POST['categoria']) ? $_POST['categoria']:'';
$codigo = isset($_POST['Codigo']) ? $_POST['Codigo']:'';

$objPHPExcel = new PHPExcel();

if ($codigo!="" & $p_scu!="") {
   $resultado = productosCodCatExcel($codigo,$p_scu);
}
elseif ($p_scu!="") {
   $resultado = productosCatExcel($p_scu);
}
elseif ($codigo!="") {
   $resultado = productosCodExcel($codigo);
}
else{
   $resultado = productosExcel();
}
   
/*Info General Excel*/
$objPHPExcel->
    getProperties()
        ->setCreator("TEDnologia.com")
        ->setLastModifiedBy("TEDnologia.com")
        ->setTitle("Exportar Excel con PHP")
        ->setSubject("Documento de prueba")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("usuarios phpexcel")
        ->setCategory("reportes");
    

    /* Datos Hojas */
  
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle("Productos");

$objPHPExcel->getActiveSheet()->setCellValue('A1','DESCRIPCION');
$objPHPExcel->getActiveSheet()->setCellValue('B1','STOCK');
$objPHPExcel->getActiveSheet()->setCellValue('C1','PRECIO');
$objPHPExcel->getActiveSheet()->setCellValue('D1','CATEGORIA');

$fila=2;
foreach ($resultado as $rows){

$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $rows['name']);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $rows['quantity']);
$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $rows['sale_price']);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $rows['categories']);

$fila++;
}

/*Nombre de la página*/
$objPHPExcel->getActiveSheet()->setTitle('Reporte de productos');
$objPHPExcel->setActiveSheetIndex(0);

/*Crear Filtro Hoja*/

/* Columnas AutoAjuste */
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_productos.xls"'); //nombre del documento
header('Cache-Control: max-age=0');
	
$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
$objWriter->save('php://output');
exit;

?>