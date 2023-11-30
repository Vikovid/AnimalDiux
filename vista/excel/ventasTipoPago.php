<?php
   require_once '../../libs/Classes/PHPExcel.php';
   require_once('../../modelo/load.php');

   $tipoPago = isset($_POST['tipoPago']) ? $_POST['tipoPago']:'';
   $mes = isset($_POST['mes']) ? $_POST['mes']:'';
   $anio = isset($_POST['anio']) ? $_POST['anio']:'';

   if ($mes == "" && $anio == ""){                          
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
      $fechaInicial = $year."/".$month."/01";
      $fechaFinal = $year."/".$month."/".$day;
   }

   if ($mes != "" && $anio == ""){
      $year = date('Y');
      $fechaInicial = $year."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $year."/".$mes."/".$numDias; 
   }

   if ($mes == "" && $anio != ""){
      $fechaInicial = $anio."/01/01";
      $fechaFinal = $anio."/12/31";
   }

   if ($mes != "" && $anio != ""){
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));

   if($tipoPago!="")
      $ventasTipoPago = ventasTipoPagoPerTipo($tipoPago,$fechaIni,$fechaFin);
   else
      $ventasTipoPago = ventasTipoPagoPer($fechaIni,$fechaFin);

   $objPHPExcel = new PHPExcel();

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
   $objPHPExcel->getActiveSheet()->setTitle("VentasMensFormaPago");

   $objPHPExcel->getActiveSheet()->setCellValue('A1','DIA');
   $objPHPExcel->getActiveSheet()->setCellValue('B1','FORMA DE PAGO');
   $objPHPExcel->getActiveSheet()->setCellValue('C1','CANTIDAD');

   $fila=2;

   foreach ($ventasTipoPago as $venta) {

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, date("d-m-Y", strtotime ($venta['fecha'])));
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $venta['tipo_pago']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $venta['cantidad']);

      $objPHPExcel->getActiveSheet()->getStyle("C".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");

      $fila++;
   }

   /*Nombre de la página*/
   $objPHPExcel->getActiveSheet()->setTitle('VentasMensFormaPago');
   $objPHPExcel->setActiveSheetIndex(0);

   /*Crear Filtro Hoja*/

   /* Columnas AutoAjuste */
   $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="VentasMensFormaPago.xls"'); //nombre del documento
   header('Cache-Control: max-age=0');
  
   $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
   $objWriter->save('php://output');
   exit;
?>