<?php
   $page_title = 'Venta mensual';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $all_categorias = find_all('categories');
 
   $regCat = "";
   $anio = "";
   $categoria = "";
   $nomCategoria = "";
   $totCantidad = 0;
   $totAnualVenta = 0;
   $totAnualGasto = 0;
   $totGanancia = 0;  

   if(isset($_POST['categoria'])){  
      $regCat = remove_junk($db->escape($_POST['categoria']));//prueba
   }

   if(isset($_POST['anio'])){  
      $anio =  remove_junk($db->escape($_POST['anio']));//prueba
   }

   if ($anio == ""){                          
      $anio = date('Y');
   }

   $fechaInicial = $anio."/01/01";
   $fechaFinal = $anio."/12/31";

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));
   $fechIni = date ('d-m-Y', strtotime($fechaInicial));
   $fechFin = date ('d-m-Y', strtotime($fechaFinal));

   if($regCat != ""){
     $categoria = buscaRegistroPorCampo('categories','id',$regCat);
     $nomCategoria = $categoria['name'];     
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script language="Javascript">

function foco(){
   document.form1.categoria.focus();
}

function ventasMens(){
  document.form1.action = "ventas_mens_categoria.php";
  document.form1.submit();
}

function excel(){
   if (document.form1.categoria.value != ""){
      document.form1.action = "../excel/menscategoria.php";
      document.form1.submit();
   }else{
      document.form1.categoria.focus();
      return -1;
   }
}

</script>  

<!DOCTYPE html>
<html>
<head>
<title>Ventas mensuales por categoría</title>
</head>

<body onload="foco();">
  <form name="form1" method="post" action="ventas_mens_categoria.php">

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
               <div class="pull-right">
               <?php if($nomCategoria != ""){ ?>
                  <strong>
                     <span class="glyphicon glyphicon-th"></span>
                     <?php echo $nomCategoria; ?>
                  </strong>
               <?php } ?>   
               </div>
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 14%;"> Mes </th>
                     <th class="text-center" style="width: 14%;"> Cantidad</th>
                     <th class="text-center" style="width: 14%;"> Venta </th>
                     <th class="text-center" style="width: 14%;"> Gasto </th>
                     <th class="text-center" style="width: 14%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
               <?php 
                  
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

                        $ventaCat = ventasCatTotal($regCat,$fechaIni,$fechaFin);
                   
                        if ($ventaCat != null){
                           $totalVenta = $ventaCat['total'];
                           $cantidad = $ventaCat['cantidad'];
                        }

                        $gastoCat = gastosCatTotal($regCat,$fechaIni,$fechaFin);

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
               <?php    if ($totalVenta != 0 || $totalGasto != 0){ ?>
                           <tr>
                              <td><?php echo remove_junk($nomMes); ?></td>
                              <td class="text-right"><?php echo $cantidad; ?></td>
                              <td class="text-right"><?php echo money_format('%.2n',$totalVenta); ?></td>
                              <td class="text-right"><?php echo money_format('%.2n',$totalGasto); ?></td>
                              <td class="text-right"><?php echo money_format('%.2n',$ganancia); ?></td>
                           </tr>
                           <?php 

                              $totCantidad = $totCantidad + $cantidad; 
                              $totAnualVenta = $totAnualVenta + $totalVenta;
                              $totAnualGasto = $totAnualGasto + $totalGasto;
                              $totGanancia = $totGanancia + $ganancia;

                           ?>
               <?php } ?>                      
               <?php } ?>
               <?php } ?>
               </tbody>
            </table>
         </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 14%;"> Total </th>
                     <th class="text-right" style="width: 14%;"><?php echo money_format('%.2n',$totCantidad); ?></th>
                     <th class="text-right" style="width: 14%;"><?php echo money_format('%.2n',$totAnualVenta); ?></th>
                     <th class="text-right" style="width: 14%;"><?php echo money_format('%.2n',$totAnualGasto); ?></th>
                     <th class="text-right" style="width: 14%;"><?php echo money_format('%.2n',$totGanancia); ?></th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
