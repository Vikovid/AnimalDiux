<?php
  $page_title = 'Consulta de ventas mensuales';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $user = current_user(); 

  $all_sucursal = find_all('sucursal');

  $anio = "";
  $vm_scu = "";
  $gastoAnual = 0;

  if(isset($_POST['sucursal'])){  
    $vm_scu =  remove_junk($db->escape($_POST['sucursal']));//prueba
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
  <form name="form1" method="post" action="ventas_anuales.php">

    <?php
       if ($anio == ""){                          
          $month = date('m');
          $year = date('Y');
          $day = date("d", mktime(0,0,0, $month+1, 0, $year));
          $fechaInicial = $year."/01/01";
          $fechaIni = date('Y/m/d', strtotime($fechaInicial));
          $fechaFin = date("Y/m/d", strtotime($year.$month.$day));
       }else{
          $fechaInicial = $anio."/01/01";
          $fechaFinal = $anio."/12/31";
          $fechaIni = date('Y/m/d', strtotime($fechaInicial));
          $fechaFin = date("Y/m/d", strtotime($fechaFinal));
       }

       $anio = date('Y', strtotime($fechaInicial));

       if($vm_scu!=""){

          $ventaAnual = ventasPeriodoSuc($vm_scu,$fechaIni,$fechaFin);

          $totalVentaAnual = $ventaAnual['totalVentas'];

          $gastoAnual = gastosPeriodoSuc($vm_scu,$fechaIni,$fechaFin);

          $totalGastoAnual = $gastoAnual['total'];

          $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);

          $nomSucursal = $sucursal['nom_sucursal'];
       }else{

          $ventaAnual = ventasPeriodo($fechaIni,$fechaFin);

          $totalVentaAnual = $ventaAnual['totalVentas'];

          $gastoAnual = gastosPeriodo($fechaIni,$fechaFin);

          $totalGastoAnual = $gastoAnual['total'];
       }
            
       $totalAnual = $totalVentaAnual - $totalGastoAnual;
     ?>
     <span>Total de ventas:</span>
     <?php echo money_format('%.2n',$totalVentaAnual); ?>
     <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
     <span>Total Anual:</span>
     <?php echo money_format('%.2n',$totalAnual); ?>
     <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
     <span>Año:</span>
     <?php echo $anio; ?>
     <?php if($vm_scu!=""){ ?>
          <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
          <span>Sucursal:</span>
          <?php echo remove_junk($nomSucursal); ?>
     <?php } ?>
<?php

if($vm_scu!=""){
   $sales = ySalesSucFecha($vm_scu,$fechaIni,$fechaFin);
}else{
   $sales = ySalesFecha($fechaIni,$fechaFin);
}

?>

<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-10">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div>
               <div class="form-group">
               <div class="col-md-3">
                  <select class="form-control" name="sucursal">
                     <option value="">Selecciona una sucursal</option>
                     <?php  foreach ($all_sucursal as $id): ?>
                     <option value="<?php echo (int)$id['idSucursal'] ?>">
                        <?php echo $id['nom_sucursal'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-3">
                  <select class="form-control" name="anio">
                     <option value="">Selecciona un año</option>
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
               <a href="#" onclick="ventasMensuales();" class="btn btn-primary">Buscar</a>
               <!--a href="#" onclick="barMensual();" class="btn btn-info">Gráfica</a--> 
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </div>   
         </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
             <tr>
                <th class="text-center" style="width: 10%;"> Mes</th>
                <th class="text-center" style="width: 10%;"> Sucursal</th>
                <th class="text-center" style="width: 10%;"> Venta </th>
                <th class="text-center" style="width: 10%;"> Gastos </th>
                <th class="text-center" style="width: 10%;"> Total </th>
             </tr>
          </thead>
          <tbody>
            <?php foreach ($sales as $sales):?>
               <tr>
                  <?php $fechaVentas = date("m", strtotime($sales['date']));
                        $gastosMes = "0.00";
                        if ($fechaVentas == "01")
                            $mes = "Enero";
                        if ($fechaVentas == "02")
                            $mes = "Febrero";
                        if ($fechaVentas == "03")
                            $mes = "Marzo";
                        if ($fechaVentas == "04")
                            $mes = "Abril";
                        if ($fechaVentas == "05")
                            $mes = "Mayo";
                        if ($fechaVentas == "06")
                            $mes = "Junio";
                        if ($fechaVentas == "07")
                            $mes = "Julio";
                        if ($fechaVentas == "08")
                            $mes = "Agosto";
                        if ($fechaVentas == "09")
                            $mes = "Septiembre";
                        if ($fechaVentas == "10")
                            $mes = "Octubre";
                        if ($fechaVentas == "11")
                            $mes = "Noviembre";
                        if ($fechaVentas == "12")
                            $mes = "Diciembre";

                        $ventasMens = $sales['total_ventas'];?>

                  <td> <?php echo remove_junk($mes); ?></td>
                  <td class="text-center"> <?php echo remove_junk($sales['nom_sucursal']); ?></td>
                  <td class="text-right"> <?php echo money_format('%.2n',$sales['total_ventas']); ?></td>

                  <?php 
                      $fechaCons = $anio."/".$fechaVentas."/01";
                      $numDias = date('t', strtotime($fechaCons));
                      $fechaCons = date('Y/m/d', strtotime($fechaCons));
                      $fechaFin = $anio."/".$fechaVentas."/".$numDias;
                      $fechaFin = date('Y/m/d', strtotime($fechaFin));
                      
                      if ($vm_scu != "")
                         $gastosMens = gastosPeriodoSuc($vm_scu,$fechaCons,$fechaFin);
                      else
                         $gastosMens = gastosPeriodo($fechaCons,$fechaFin);

                      $gastosMes = $gastosMens['total'];
                      
                      $totalMes = $ventasMens - $gastosMes; 
                  ?>
                  <td class="text-right"> <?php echo money_format('%.2n',$gastosMes); ?></td>
                  <td class="text-right"> <?php echo money_format('%.2n',$totalMes); ?></td>
               </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
     </div>         
  </div>
</div>
<input type="hidden" name="idSuc" value="<?php echo ucfirst($user['idSucursal']) ?>">
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>


