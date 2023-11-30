<?php
   $page_title = 'Ventas y gastos por categoría';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $all_categorias = find_all('categories');

   $mes = "";
   $anio = "";
   $categ = "";
   $categoria = "";
   $ventaMen = "0";
   $gastoMen = "0";
   $totalMen = "0";
   $ventaAnual = "0";
   $gastoAnual = "0";
   $totalAnual = "0";

   if(isset($_POST['mes'])){  
      $mes =  remove_junk($db->escape($_POST['mes']));//prueba
   }

   if(isset($_POST['anio'])){  
      $anio =  remove_junk($db->escape($_POST['anio']));//prueba
   }

   if(isset(($_POST['categoria']))){
      $categ = remove_junk($db->escape($_POST['categoria']));
   }

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
   $fechIni = date ('d-m-Y', strtotime($fechaInicial));
   $fechFin = date ('d-m-Y', strtotime($fechaFinal));
   $numDias = date('t', strtotime($fechaIni));

   if ($anio != ""){
      $fechaIniAnio = $anio."/01/01";
      $fechaFinAnio = $anio."/12/31";
   }else{
      $anio = date('Y');
      $fechaIniAnio = $anio."/01/01";
      $fechaFinAnio = $anio."/12/31";
   }

   $fechaIniAnio = date ('Y/m/d', strtotime($fechaIniAnio));
   $fechaFinAnio = date ('Y/m/d', strtotime($fechaFinAnio));

   if ($categ != ""){
      $gastoCatMen = gastosMACTotal($categ,$fechaIni,$fechaFin);
      $gastoCatAnual = gastosMACTotal($categ,$fechaIniAnio,$fechaFinAnio);
      $ventasCatMen = ventasCatTotal($categ,$fechaIni,$fechaFin);
      $ventasCatAnual = ventasCatTotal($categ,$fechaIniAnio,$fechaFinAnio);

      $consCat = find_by_id("categories",$categ);
      $categoria = $consCat['name'];
   }else{
      $gastoCatMen = gastosMACPerTotal($fechaIni,$fechaFin);
      $gastoCatAnual = gastosMACPerTotal($fechaIniAnio,$fechaFinAnio);
      $ventasCatMen = ventasCatPerTotal($fechaIni,$fechaFin);
      $ventasCatAnual = ventasCatPerTotal($fechaIniAnio,$fechaFinAnio);
   }

   $gastoMen = $gastoCatMen['total'];
   $gastoAnual = $gastoCatAnual['total'];
   $ventaMen = $ventasCatMen['total'];
   $ventaAnual = $ventasCatAnual['total'];
   $totalMen = $ventaMen - $gastoMen;
   $totalAnual = $ventaAnual - $gastoAnual;
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function dato(){
  document.form1.action = "ventas_gastos_mens_categoria.php";
  document.form1.submit();
}

</script>
<form name="form1" method="post" action="ventas_gastos_mens_categoria.php">
   <span>Período:</span>
   <?php echo "del $fechIni al $fechFin";?>
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>

   <div class="row">
      <div class="col-md-12">
         <?php echo display_msg($msg); ?>
      </div>
      <div class="col-md-9">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div>
                  <div class="form-group">
                     <div class="col-md-2">
                        <select class="form-control" name="categoria">
                           <option value="">Seleccione una categoría</option>
                           <?php foreach ($all_categorias as $id): ?>
                           <option value="<?php echo $id['id'] ?>">
                           <?php echo $id['name'] ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-2">
                        <select class="form-control" name="mes" >
                           <option value="">Mes</option>
                           <option value="01">Enero</option>
                           <option value="02">Febrero</option>
                           <option value="03">Marzo</option>
                           <option value="04">Abril</option>
                           <option value="05">Mayo</option>
                           <option value="06">Junio</option>
                           <option value="07">Julio</option>
                           <option value="08">Agosto</option>
                           <option value="09">Septiembre</option>
                           <option value="10">Octubre</option>
                           <option value="11">Noviembre</option>
                           <option value="12">Diciembre</option>
                        </select>
                     </div>  
                     <div class="col-md-2">
                        <select class="form-control" name="anio" >
                           <option value="">Año</option>
                           <option value="2020">2020</option>
                           <option value="2021">2021</option>
                           <option value="2022">2022</option>
                           <option value="2023">2023</option>
                           <option value="2024">2024</option>
                           <option value="2025">2025</option>
                           <option value="2026">2026</option>
                           <option value="2027">2027</option>
                           <option value="2028">2028</option>
                           <option value="2029">2029</option>
                           <option value="2030">2030</option>
                           <option value="2031">2031</option>
                           <option value="2032">2032</option>
                           <option value="2033">2033</option>
                           <option value="2034">2034</option>
                           <option value="2035">2035</option>
                           <option value="2036">2036</option>
                           <option value="2037">2037</option>
                           <option value="2038">2038</option>
                           <option value="2039">2039</option>
                           <option value="2040">2040</option>
                        </select>
                     </div>  
                     <a href="#" onclick="dato();" class="btn btn-primary">Buscar</a>
                     <?php if ($categ != ""){ ?>
                        <strong>
                           <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
                           <span class="glyphicon glyphicon-th"></span>
                           <span><?php echo $categoria ?></span>
                        </strong>
                     <?php } ?>   
                  </div>   
               </div>
            </div>
            <table width="100%">
               <tr>
                  <td width="25%" style="font-size:17px">Venta mensual:</td> 
                  <td width="20%" align="right" style="font-size:17px"><?php echo money_format('%.2n',$ventaMen); ?></td>
                  <td width="5%">&nbsp;</td> 
                  <td width="25%" style="font-size:17px">Venta anual:</td> 
                  <td width="20%" align="right" style="font-size:17px"><?php echo money_format('%.2n',$ventaAnual); ?></td>
                  <td width="5%">&nbsp;</td> 
               </tr>
               <tr>
                  <td width="25%" style="font-size:17px">Gasto mensual:</td> 
                  <td width="20%" align="right" style="font-size:17px"><?php echo money_format('%.2n',$gastoMen); ?></td>
                  <td width="5%">&nbsp;</td> 
                  <td width="25%" style="font-size:17px">Gasto anual:</td> 
                  <td width="20%" align="right" style="font-size:17px"><?php echo money_format('%.2n',$gastoAnual); ?></td>
                  <td width="5%">&nbsp;</td> 
               </tr>
               <tr>
                  <td width="25%" style="font-size:17px"><b>Total mensual:</td> 
                  <td width="20%" align="right" style="font-size:17px"><b><?php echo money_format('%.2n',$totalMen); ?></td>
                  <td width="5%">&nbsp;</td> 
                  <td width="25%" style="font-size:17px"><b>Total anual:</td> 
                  <td width="20%" align="right" style="font-size:17px"><b><?php echo money_format('%.2n',$totalAnual); ?></td>
                  <td width="5%">&nbsp;</td> 
               </tr>
            </table>
         </div>
      </div>
   </div>
</form>
<?php include_once('../layouts/footer.php'); ?>
