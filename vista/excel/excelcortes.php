<?php
   require_once '../../libs/Classes/PHPExcel.php';
   require_once('../../modelo/load.php');

   $c_idEncargado = isset($_POST['encargado']) ? $_POST['encargado']:'';

   $parametros = find_by_id("parametros","1");

   $porcComision = $parametros['comision'];

   $objPHPExcel = new PHPExcel();

   if ($c_idEncargado!=""){
      $result = find_by_id("users",$c_idEncargado);          
      $cortes = corteVendedor($result['username']);
   }else{
      $cortes = corte();
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
   $objPHPExcel->getActiveSheet()->setTitle("Cortes");

   $objPHPExcel->getActiveSheet()->setCellValue('A1','NOMBRE');
   $objPHPExcel->getActiveSheet()->setCellValue('B1','SUCURSAL');
   $objPHPExcel->getActiveSheet()->setCellValue('C1','VENTA');
   $objPHPExcel->getActiveSheet()->setCellValue('D1','COMISION');
   $objPHPExcel->getActiveSheet()->setCellValue('E1','FECHA');

   $fila=2;

   foreach ($cortes as $corte) {

      $comision = $corte['venta'] * ($porcComision/100);

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $corte['vendedor']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $corte['nom_sucursal']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $corte['venta']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $comision);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, date("d-m-Y", strtotime ($corte['date'])));

      $objPHPExcel->getActiveSheet()->getStyle("C".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
      $objPHPExcel->getActiveSheet()->getStyle("D".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");

      $fila++;
   }

   /*Nombre de la página*/
   $objPHPExcel->getActiveSheet()->setTitle('Reporte de cortes');
   $objPHPExcel->setActiveSheetIndex(0);

   /*Crear Filtro Hoja*/

   /* Columnas AutoAjuste */
   $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="Reporte_cortes.xls"'); //nombre del documento
   header('Cache-Control: max-age=0');
  
   $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
   $objWriter->save('php://output');
   exit;
?>