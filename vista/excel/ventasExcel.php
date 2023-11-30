<?php
   require_once '../../libs/Classes/PHPExcel.php';
   require_once('../../modelo/load.php');

   $formaPago = isset($_POST['formaPago']) ? $_POST['formaPago']:'';
   $mes = isset($_POST['mes']) ? $_POST['mes']:'';
   $anio = isset($_POST['anio']) ? $_POST['anio']:'';

   $ticketAnt = 0;

   if ($mes == "" && $anio == ""){                          
      $mes = date('m');
      $anio = date('Y');
      $day = date("d", mktime(0,0,0, $mes+1, 0, $anio));
      $fechaInicial = $anio."/".$mes."/01";
      $fechaFinal = $anio."/".$mes."/".$day;
   }

   if ($mes != "" && $anio == ""){
      $anio = date('Y');
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   if ($mes == "" && $anio != ""){
      $mes = date('m');
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   if ($mes != "" && $anio != ""){
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));

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

   $nomAbrv = substr($nomMes,0,3);

   $nomDocumento = "Ventas_".$nomAbrv."_".$anio;
   $periodo = $nomMes." ".$anio;

   $sales = venta($fechaIni,$fechaFin);

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
   $objPHPExcel->getActiveSheet()->setTitle($periodo);

   $objPHPExcel->getActiveSheet()->setCellValue('A1','VENDEDOR');
   $objPHPExcel->getActiveSheet()->setCellValue('B1','NOMBRE DEL PRODUCTO');
   $objPHPExcel->getActiveSheet()->setCellValue('C1','CANTIDAD');
   $objPHPExcel->getActiveSheet()->setCellValue('D1','TOTAL');
   $objPHPExcel->getActiveSheet()->setCellValue('E1','TIPO PAGO');
   $objPHPExcel->getActiveSheet()->setCellValue('F1','FECHA');

   $fila=2;
   foreach ($sales as $sale){

      $consNumPagos = numTiposPagos($sale['id_ticket']);
      $numPagos = $consNumPagos['numPagos'];
      $abonoTotal = $consNumPagos['cantidad'];

      if ($numPagos == "1"){

         $consTipoPago = buscaRegistroPorCampo('pagos','id_ticket',$sale['id_ticket']);
         $idTipoPago = $consTipoPago['id_tipo'];

         if ($idTipoPago == "1")
            $tipoPago = "Efectivo";
         if ($idTipoPago == "2")
            $tipoPago = "Transferencia";
         if ($idTipoPago == "3")
            $tipoPago = "Deposito";
         if ($idTipoPago == "4")
            $tipoPago = "Tarjeta";
      }else{
         $tipoPago = "Mixto";
         $idTipoPago = "5";
      }

      $vendedor = $sale['vendedor'];
                 
      $cantidad = $sale['qty'];
      $fecha = date("d-m-Y", strtotime ($sale['date']));

      if ($sale['tipo_pago'] == "0"){
         $producto = $sale['name'];
         $precio = $sale['price'];
      }

      if ($sale['tipo_pago'] != "0" && $ticketAnt != $sale['id_ticket']){                 

         $cliente = buscaRegistroPorCampo('cliente','idcredencial',$sale['idCliente']);

         if ($cliente != null)
            $nomCliente = $cliente['nom_cliente'];

         $producto = "Abono crédito: ".$nomCliente;
         $precio = $abonoTotal;
      }
          
      if ($formaPago == ""){          
         if($sale['tipo_pago'] == "0"){              
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $vendedor);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $producto);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $cantidad);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $precio);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $tipoPago);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $fecha);
            $fila++;
         }                     
            
         if($sale['tipo_pago'] != "0" && $ticketAnt != $sale['id_ticket']){                          
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $vendedor);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $producto);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $cantidad);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $precio);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $tipoPago);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $fecha);
            $ticketAnt = $sale['id_ticket'];
            $fila++;
         }
      }else{
         if($sale['tipo_pago'] == "0" && $formaPago == $idTipoPago){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $vendedor);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $producto);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $cantidad);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $precio);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $tipoPago);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $fecha);
            $fila++;
         }
         if($sale['tipo_pago'] != "0" && $ticketAnt != $sale['id_ticket'] && $formaPago == $idTipoPago){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $vendedor);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $producto);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $cantidad);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $precio);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, $tipoPago);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $fecha);
            $ticketAnt = $sale['id_ticket'];
            $fila++;
         }
      }
   }

   /*Nombre de la página*/
   $objPHPExcel->getActiveSheet()->setTitle($periodo);
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
   header('Content-Disposition: attachment;filename='.$nomDocumento.'.xls'); //nombre del documento
   header('Cache-Control: max-age=0');
	
   $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
   $objWriter->save('php://output');
   exit;
?>