<?php
   require_once ('../../modelo/load.php');

   $vm_scu= isset($_POST['sucursal']) ? $_POST['sucursal']:'';
   $anio= isset($_POST['anio']) ? $_POST['anio']:'';
   $idSuc= isset($_POST['idSuc']) ? $_POST['idSuc']:'';

   $nomSucursal = "";

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

//   if ($vm_scu == "")
//   	  $vm_scu = $idSuc;

   if ($vm_scu!=""){

      $ventaAnual = ventasPeriodoSuc($vm_scu,$fechaIni,$fechaFin);

      $totalVentaAnual = $ventaAnual['totalVentas'];

      $gastoAnual = gastosPeriodoSuc($vm_scu,$fechaIni,$fechaFin);

      $totalGastoAnual = $gastoAnual['total'];

      $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);

      $nomSucursal = $sucursal['nom_sucursal'];

      $sales = ySalesSucFecha($vm_scu,$fechaIni,$fechaFin);

   }else{

      $ventaAnual = ventasPeriodo($fechaIni,$fechaFin);

      $totalVentaAnual = $ventaAnual['totalVentas'];

      $gastoAnual = gastosPeriodo($fechaIni,$fechaFin);

      $totalGastoAnual = $gastoAnual['total'];

      $sales = ySalesFecha($fechaIni,$fechaFin);
   }
        
   $totalAnual = $totalVentaAnual - $totalGastoAnual;            

   $meses = array();
   $colores = array(); 
   $totalesMes = array();
   $coloresAux = array();

   foreach ($sales as $sales):

      $fechaVentas = date("m", strtotime($sales['date']));
      $gastosMes = 0;

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

      $ventasMens = $sales['total_ventas'];

      $fechaInicio = $anio."/".date("m", strtotime($sales['date']))."/01";
      $numDias = date('t', strtotime($fechaInicio));
      $fechaIni = date('Y/m/d', strtotime($fechaInicio));
      $fechaFinal = $anio."/".date("m", strtotime($sales['date']))."/".$numDias;
      $fechaFin = date('Y/m/d', strtotime($fechaFinal));

      if ($vm_scu != "")
         $gastoMensual = gastosPeriodoSuc($vm_scu,$fechaIni,$fechaFin);
      else
         $gastoMensual = gastosPeriodo($fechaIni,$fechaFin);

      $gastosMes = $gastoMensual['total'];
   
      $totalMes = $ventasMens - $gastosMes;
      $meses[] = $mes;
      $totalesMes[] = $totalMes;
      $colores[] = 'rgba(51,82,255,1)';
      $coloresAux[] = 'rgba(137,19,19,1)';
   endforeach;
?>

<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../libs/js/chartJS/src/bootstrap.min.css">

    <title>Gráfica mensual</title>
  </head>
       
  <body>
    <div class="p-6 mb-4 text-center bg-secondary text-white">
       <h5>Total de ventas: <?php echo $totalVentaAnual;?> Total anual: <?php echo $totalAnual;?></h5>
    </div>
    <div class="p-6 mb-4 text-center bg-info text-white">
       <h3><?php echo $nomSucursal;?> GRÁFICA DEL AÑO: <?php echo $anio ?></h3>
    </div>
    <div class="col-lg-12">
       <canvas id="canvas" width="600" height="320"></canvas>
    </div>
  </body>
</html>

<script src="../../libs/js/chartJS/src/Chart.min.js"></script>
<script>

var ctx = document.getElementById('canvas').getContext('2d');
Chart.defaults.global.defaultFontColor = 'black';
Chart.defaults.global.defaultFontSize = 17;

var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($meses) ?>,
        datasets: [{
            label: 'Mes/Total',
            backgroundColor: <?php echo json_encode($colores) ?>,
            hoverBackgroundColor: <?php echo json_encode($coloresAux) ?>,
            data: <?php echo json_encode($totalesMes) ?>,
        }]
    },
});

</script>