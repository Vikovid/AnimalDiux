<?php
	// LOAD y PHPExcel
	require_once('../../libs/Classes/PHPExcel.php');
   require_once('../../modelo/load.php');

   // Categorias
   $all_categorias = find_all('categories');
 	
 	// Filtros
   $regCat = "";
   $mes = "";
   $anio = "";
  	
  	// Esctableciendo los valores de los filtros
   if(isset($_POST['categoria']))
      $regCat = 	remove_junk($db->escape($_POST['categoria']));
   if(isset($_POST['mes']))
      $mes =  		remove_junk($db->escape($_POST['mes']));
   if(isset($_POST['anio']))  
      $anio =  	remove_junk($db->escape($_POST['anio']));

   // Establece los rangos de las fechas con base a los valores de los filtros
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
	if ($regCat != '') {
		$categorie = find_by_id('categories',$regCat);
		$titulo = $categorie['name'];
	}

   // Longitud del titulo
   $longNomCat = strlen($titulo);
   // Reduciendo la longitud del título
   if ($longNomCat >= 31)
      $titulo = substr($titulo,0,31);

   // Retorna una matriz
   if($regCat != ""){
     $categorias = buscaRegsPorCampo('categories','id',$regCat);
   }else{
     $categorias = monthlycat1($fechaIni,$fechaFin);
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
   $objPHPExcel->getActiveSheet()->setCellValue('B1','CANTIDAD');
   $objPHPExcel->getActiveSheet()->setCellValue('C1','VENTA');
   $objPHPExcel->getActiveSheet()->setCellValue('D1','GASTO');
   $objPHPExcel->getActiveSheet()->setCellValue('E1','GANANCIA');
	
   // Comienza en la celda A2
   $fila=2;

   foreach ($categorias as $categoria){
		$ventaCat = ventasCatTotal($categoria['id'],$fechaIni,$fechaFin);
                   
      if ($ventaCat != null){
         $totalVenta = $ventaCat['total'];
         $cantidad = $ventaCat['cantidad'];
      }

      $gastoCat = gastosCatTotal($categoria['id'],$fechaIni,$fechaFin);

      if ($gastoCat != null){
      	$totalGasto = $gastoCat['total'];
      }
             
      $ganancia = $totalVenta - $totalGasto;

      if ($totalGasto == "")
      	$totalGasto = 0;
      if ($totalVenta == "")
         $totalVenta = 0;
      if ($cantidad == "")
         $cantidad = "0";

      if ($totalVenta != 0 || $totalGasto != 0){
 			$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $categoria['name']);
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
   
   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="Monthly_sales_categoria.xls"'); //nombre del documento
   header('Cache-Control: max-age=0');
	
   $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
   $objWriter->save('php://output');
   exit;

?>