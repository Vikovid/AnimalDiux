<?php
   $page_title = 'Venta mensual';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $all_categorias = find_all('categories');
 
   $regCat = "";
   $mes = "";
   $anio = "";
  
   if(isset($_POST['categoria'])){  
      $regCat = remove_junk($db->escape($_POST['categoria']));//prueba
   }

   if(isset($_POST['mes'])){  
      $mes =  remove_junk($db->escape($_POST['mes']));//prueba
   }

   if(isset($_POST['anio'])){  
      $anio =  remove_junk($db->escape($_POST['anio']));//prueba
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<script language="Javascript">
   function excel(){
      document.form1.action = "../excel/monthlysalescategoria.php";
      document.form1.submit();
   }
</script>

<!DOCTYPE html>
<html>
<head>
<title>Ventas Mensuales</title>
</head>

<body onload="focoCategoria();">
  <form name="form1" method="post" action="monthly_sales_categoria.php">

<?php

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
   $fechIni = date ('d-m-Y', strtotime($fechaInicial));
   $fechFin = date ('d-m-Y', strtotime($fechaFinal));

   if($regCat != ""){
     $categorias = buscaRegsPorCampo('categories','id',$regCat);
   }else{
     $categorias = monthlycat1($fechaIni,$fechaFin);
   }
?>

<span>Período:</span>
<?php echo "del $fechIni al $fechFin";?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-3">
                  <select class="form-control" name="categoria">
                     <option value="">Categoría</option>
                     <?php  foreach ($all_categorias as $id): ?>
                     <option value="<?php echo (int)$id['id'] ?>">
                     <?php echo $id['name'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="mes">
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
                  <select class="form-control" name="anio">
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
               <a href="#" onclick="ventasMens();" class="btn btn-primary">Buscar</a>
               <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>      
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">   
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th> Categoría </th>
                     <th class="text-center" style="width: 14%;"> Cantidad</th>
                     <th class="text-center" style="width: 14%;"> Venta </th>
                     <th class="text-center" style="width: 14%;"> Gasto </th>
                     <th class="text-center" style="width: 14%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
               <?php 

                  foreach ($categorias as $categoria):

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
               ?>
               <?php if ($totalVenta != 0 || $totalGasto != 0){ ?>
                     <tr>
                        <td><?php echo remove_junk($categoria['name']); ?></td>
                        <td class="text-right"><?php echo $cantidad; ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$totalVenta); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$totalGasto); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$ganancia); ?></td>
                     </tr>
               <?php } ?>                      
               <?php endforeach;?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
