<?php
  $page_title = 'Consulta de ventas diarias';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  //$products = join_product_table();
  $all_sucursal = find_all('sucursal');

  $mes = "";
  $anio = "";
  $vm_scu = "";

  if(isset($_POST['sucursal'])){  
    $vm_scu =  remove_junk($db->escape($_POST['sucursal']));//prueba
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

<!DOCTYPE html>
<html>
<head>
<title>Ventas Mensuales</title>
</head>

<body onload="focoSucursal();">
  <form name="form1" method="post" action="ventas-mensuales.php">

  <?php
                          
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

      if($vm_scu!=""){

         $ventaMensual = ventasPeriodoSuc($vm_scu,$fechaIni,$fechaFin);

         $totalVentaMensual = $ventaMensual['totalVentas'];

         $gastoMensual = gastosPeriodoSuc($vm_scu,$fechaIni,$fechaFin);

         $totalGastoMensual = $gastoMensual['total'];

         $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);

         $nomSucursal = $sucursal['nom_sucursal'];

      }else{

         $ventaMensual = ventasPeriodo($fechaIni,$fechaFin);

         $totalVentaMensual = $ventaMensual['totalVentas'];

         $gastoMensual = gastosPeriodo($fechaIni,$fechaFin);

         $totalGastoMensual = $gastoMensual['total'];
      }
              
      $total = $totalVentaMensual - $totalGastoMensual;
  ?>
  <span>Total de ventas:</span>
  <?php echo money_format('%.2n',$totalVentaMensual); ?>
  <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
  <span>Total:</span>
  <?php echo money_format('%.2n',$total); ?>
  <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
  <span>Período:</span>
  <?php echo "del $fechIni al $fechFin"; ?>
  <?php if($vm_scu!=""){ ?>
          <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
          <span>Sucursal:</span>
          <?php echo remove_junk($nomSucursal); ?>
  <?php } ?>

<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-10">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div>
               <div class="form-group">
               <div class="col-md-2">
                  <select class="form-control" name="sucursal">
                     <option value="">Sucursal</option>
                     <?php  foreach ($all_sucursal as $id): ?>
                     <option value="<?php echo (int)$id['idSucursal'] ?>">
                        <?php echo $id['nom_sucursal'] ?></option>
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
               <a href="#" onclick="ventasDiarias();" class="btn btn-primary">Buscar</a>
               <!--a href="#" onclick="barDiaria();" class="btn btn-info">Gráfica</a--> 
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th class="text-center" style="width: 10%;"> Dia</th>
                  <th class="text-center" style="width: 10%;"> Sucursal</th>
                  <th class="text-center" style="width: 10%;"> Venta </th>
                  <th class="text-center" style="width: 10%;"> Gasto </th>
                  <th class="text-center" style="width: 10%;"> Total </th>
               </tr>
            </thead>
            <tbody>
            <?php for ($i = 1; $i <= $numDias; $i++) {

                  $ventaDia = 0;
                  $gastoDia = 0;
                  $nomSucursal = "";

                  $fecha = date('Y/m/d', mktime(0,0,0, $mes, $i, $anio));
                  $fechaMov = date('d-m-Y', mktime(0,0,0, $mes, $i, $anio));    

                  if ($vm_scu != "")
                     $ventasDia = ventasPeriodoSuc($vm_scu,$fecha,$fecha);
                  else
                     $ventasDia = ventasPeriodo($fecha,$fecha);

                  $ventaDia = $ventasDia['totalVentas'];
                  $nomSucursal = $ventasDia['nom_sucursal'];

                  if ($vm_scu != "")
                     $gastosDia = gastosPeriodoSuc($vm_scu,$fecha,$fecha);
                  else
                     $gastosDia = gastosPeriodo($fecha,$fecha);

                  $gastoDia = $gastosDia['total'];

                  if ($nomSucursal == "")
                     $nomSucursal = $gastosDia['nom_sucursal'];

                  if ($ventaDia == 0)
                     $ventaDia = "0.00";
                  if ($gastoDia == 0)
                     $gastoDia = "0.00";
                  ?> 
                  <tr> 
                  <?php if ($ventaDia > 0 || $gastoDia > 0){ ?>
                           <td> <?php echo $fechaMov; ?></td>
                           <td class="text-center"> <?php echo remove_junk($nomSucursal); ?></td>
                           <td class="text-right"> <?php echo money_format('%.2n',$ventaDia); ?></td>
                           <td class="text-right"> <?php echo money_format('%.2n',$gastoDia); ?></td>
                           <?php $totalDia = $ventaDia - $gastoDia; ?>
                           <td class="text-right"> <?php echo money_format('%.2n',$totalDia); ?></td>
                  <?php } ?>                           
                  </tr>
            <?php }  ?>
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