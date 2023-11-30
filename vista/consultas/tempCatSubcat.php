<?php
   require_once('../../modelo/load.php');

   ini_set('date.timezone','America/Mexico_City');

   $categoria = isset($_POST['categoria']) ? $_POST['categoria']:'';
   $subcategoria = isset($_POST['subcategoria']) ? $_POST['subcategoria']:'';
   $mes = isset($_POST['mes']) ? $_POST['mes']:'';
   $anio = isset($_POST['anio']) ? $_POST['anio']:'';

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

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));

   if ($categoria != '' && $subcategoria == '')
      $categorias = monthlycateg1($fechaIni, $fechaFin, $categoria);
   if ($categoria != '' && $subcategoria != '')
      $categorias = monthlySubcateg1($fechaIni, $fechaFin, $categoria, $subcategoria);
   if ($categoria == '' && $subcategoria == '')
      $categorias = monthlycat1($fechaIni, $fechaFin);

   borraTabla('tempcatsubcat');

   foreach($categorias as $cat):
      altaTempCatSubCat($cat['categoria'],$cat['subCat'],$cat['cantVentas'],$cat['ventas'],$cat['gasto'],$cat['ganancia'],$fechaInicial,$fechaFinal);
   endforeach;

   echo '<script> window.location="monthly_sales_categoria.php";</script>';	
?>