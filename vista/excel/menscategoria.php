<?php
   require_once '../../libs/Classes/PHPExcel.php';
   require_once('../../modelo/load.php');

   $categoria = isset($_POST['categoria']) ? $_POST['categoria']:'';
   $anio = isset($_POST['anio']) ? $_POST['anio']:'';

   if ($anio == "")                          
      $anio = date('Y');

   $totCantidad = 0;
   $totAnualVenta = 0;
   $totAnualGasto = 0;
   $totGanancia = 0;

   $datosCat = buscaRegistroPorCampo('categories','id',$categoria);
   $nomCategoria = $datosCat['name'];     
   $longNomCat = strlen($nomCategoria);
   
   if ($longNomCat >= 31)
      $nomCategoria = substr($nomCategoria,0,31);

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
   $objPHPExcel->getActiveSheet()->setTitle("VentasMensCategoria");

   $objPHPExcel->getActiveSheet()->setCellValue('A1','MES');
   $objPHPExcel->getActiveSheet()->setCellValue('B1','CANTIDAD');
   $objPHPExcel->getActiveSheet()->setCellValue('C1','VENTA');
   $objPHPExcel->getActiveSheet()->setCellValue('D1','GASTO');
   $objPHPExcel->getActiveSheet()->setCellValue('E1','GANANCIA');
   
   $fila=2;

   if ($categoria != ""){
      for ($i = 1; $i <= 12; $i++) {
          if ($i < 10)
             $mes = "0".$i;
          else
             $mes = $i;
                                          
          if ($mes == "01")
             $nomMes = "Enero";
          if ($mes == "02")
             $nomMes = "Febrero";
          if ($mes == "03")
             $nomMes = "Marzo";
          if ($mes == "04")
             $nomMes = "Abril";
          if ($mes == "05")
             $nomMes = "Mayo";
          if ($mes == "06")
             $nomMes = "Junio";
          if ($mes == "07")
             $nomMes = "Julio";
          if ($mes == "08")
             $nomMes = "Agosto";
          if ($mes == "09")
             $nomMes = "Septiembre";
          if ($mes == "10")
             $nomMes = "Octubre";
          if ($mes == "11")
             $nomMes = "Noviembre";
          if ($mes == "12")
             $nomMes = "Diciembre";

          $fechaInicial = $anio."/".$mes."/01";
          $numDias = date('t', strtotime($fechaInicial));
          $fechaFinal = $anio."/".$mes."/".$numDias;

          $fechaIni = date('Y/m/d', strtotime($fechaInicial));
          $fechaFin = date("Y/m/d", strtotime($fechaFinal));

          $ventaCat = ventasCatTotal($categoria,$fechaIni,$fechaFin);
                   
          if ($ventaCat != null){
             $totalVenta = $ventaCat['total'];
             $cantidad = $ventaCat['cantidad'];
          }

          $gastoCat = gastosCatTotal($categoria,$fechaIni,$fechaFin);

          if ($gastoCat != null){
             $totalGasto = $gastoCat['total'];
          }
             
          $ganancia = $totalVenta - $totalGasto;

          if ($totalGasto == "")
             $totalGasto = "0";
          if ($totalVenta == "")
             $totalVenta = "0";
          if ($cantidad == "")
             $cantidad = "0";

          if ($totalVenta != 0 || $totalGasto != 0){

             $totCantidad = $totCantidad + $cantidad; 
             $totAnualVenta = $totAnualVenta + $totalVenta;
             $totAnualGasto = $totAnualGasto + $totalGasto;
             $totGanancia = $totGanancia + $ganancia;

             $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $nomMes);
             $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $cantidad);
             $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $totalVenta);
             $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $totalGasto);
             $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $ganancia);

             $objPHPExcel->getActiveSheet()->getStyle("C".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
             $objPHPExcel->getActiveSheet()->getStyle("D".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
             $objPHPExcel->getActiveSheet()->getStyle("E".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");

             $fila++;
          }
      }
      $fila++;
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, "TOTAL");
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $totCantidad);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $totAnualVenta);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $totAnualGasto);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $totGanancia);

      $objPHPExcel->getActiveSheet()->getStyle("C".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
      $objPHPExcel->getActiveSheet()->getStyle("D".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
      $objPHPExcel->getActiveSheet()->getStyle("E".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
   }

   /*Nombre de la página*/
   $objPHPExcel->getActiveSheet()->setTitle($nomCategoria);
   $objPHPExcel->setActiveSheetIndex(0);

   /*Crear Filtro Hoja*/

   /* Columnas AutoAjuste */
   $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
   
   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="Reporte_mens_categoria.xls"'); //nombre del documento
   header('Cache-Control: max-age=0');
	
   $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
   $objWriter->save('php://output');
   exit;
?>