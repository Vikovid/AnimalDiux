<?php
	// LOAD y PHPExcel
	require_once('../../libs/Classes/PHPExcel.php');
   require_once('../../modelo/load.php');

   $all_categorias = find_all('categories');
   $regCat =      "";
   $regSubCat =   "";
   $mes =         "";
   $anio =        "";
  	
   $subcategorias = '';
   if(isset($_POST['categoria'])){  
      $regCat = remove_junk($db->escape($_POST['categoria']));
      if ($regCat != '') {
         $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$regCat);
      }
   }
   if(isset($_POST['subcategoria']))
      $regSubCat = remove_junk($db->escape($_POST['subcategoria']));
   if(isset($_POST['mes']))
      $mes = remove_junk($db->escape($_POST['mes']));
   if(isset($_POST['anio']))
      $anio = remove_junk($db->escape($_POST['anio']));

   // Configuración de fechas
   if ($mes == "" && $anio == ""){                          
      $year = date('Y');
      $fechaInicial = $year."/01/01";
      $fechaFinal = $year."/12/31";
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

   // El rango de fechas
   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));
	
	// Titulo
   $titulo ='categorias';				
   // Longitud del titulo
   $longNomCat = strlen($titulo);
   // Reduciendo la longitud del título
   if ($longNomCat >= 31)
      $titulo = substr($titulo,0,31);

	if($regCat != '' && $regSubCat == ''){
      $categorias = monthlycateg1($fechaIni, $fechaFin, $regCat);
   }elseif($regCat != '' && $regSubCat != ''){
      $categorias = monthlySubcateg1($fechaIni, $fechaFin, $regCat, $regSubCat);
   }
   else{
      $categorias = monthlycat1($fechaIni, $fechaFin);
   }

	// Creación del objeto PHPExcel
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

   // Coloca los títulos de las columnas de la tabla
   $objPHPExcel->getActiveSheet()->setCellValue('A1','CATEGORIA');
   $objPHPExcel->getActiveSheet()->setCellValue('B1','SUBCATEGORIA');
   $objPHPExcel->getActiveSheet()->setCellValue('C1','CANTIDAD');
   $objPHPExcel->getActiveSheet()->setCellValue('D1','VENTA');
   $objPHPExcel->getActiveSheet()->setCellValue('E1','GASTO');
   $objPHPExcel->getActiveSheet()->setCellValue('F1','GANANCIA');
	
   // Comienza en la celda A2
   $fila=2;

   foreach ($categorias as $categ){
 		$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, utf8_encode($categ['categoria']));
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $categ['subCat']);
 		$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $categ['cantVentas']);
 		$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $categ['ventas']);
 		$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $categ['gasto']);
 		$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $categ['ganancia']);

 		$objPHPExcel->getActiveSheet()->getStyle("D".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
      $objPHPExcel->getActiveSheet()->getStyle("E".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");
      $objPHPExcel->getActiveSheet()->getStyle("F".$fila)->getNumberFormat()->setFormatCode("_(\"$\"* #,##0.00_);_(\"$\"* \(#,##0.00\);_(\"$\"* \"-\"??_);_(@_)");

 		$fila++;      
   }

   /*Nombre de la página*/
   $objPHPExcel->getActiveSheet()->setTitle($titulo);
   $objPHPExcel->setActiveSheetIndex(0);

   /*Crear Filtro Hoja*/

   /* Columnas AutoAjuste */
   $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
   $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
   
   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="Monthly_sales_categoria.xls"'); //nombre del documento
   header('Cache-Control: max-age=0');
	
   $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
   $objWriter->save('php://output');
   exit;
?>