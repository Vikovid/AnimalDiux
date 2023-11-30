<?php
   require_once ('../../modelo/load.php');

   $vm_scu = isset($_POST['sucursal']) ? $_POST['sucursal']:'';
   $mes = isset($_POST['mes']) ? $_POST['mes']:'';
   $anio = isset($_POST['anio']) ? $_POST['anio']:'';

   $nomSucursal = "";

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

   $newdate = date("m", strtotime($fechaInicial));

   if ($newdate == "01")
      $nomMes = "Enero";
   if ($newdate == "02")
      $nomMes = "Febrero";
   if ($newdate == "03")
      $nomMes = "Marzo";
   if ($newdate == "04")
      $nomMes = "Abril";
   if ($newdate == "05")
      $nomMes = "Mayo";
   if ($newdate == "06")
      $nomMes = "Junio";
   if ($newdate == "07")
      $nomMes = "Julio";
   if ($newdate == "08")
      $nomMes = "Agosto";
   if ($newdate == "09")
      $nomMes = "Septiembre";
   if ($newdate == "10")
      $nomMes = "Octubre";
   if ($newdate == "11")
      $nomMes = "Noviembre";
   if ($newdate == "12") 
      $nomMes = "Diciembre";

   if ($vm_scu!=""){

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

   $fechasMov = array();
   $colores = array(); 
   $totalesDia = array();
   $coloresAux = array();

   for ($i = 1; $i <= $numDias; $i++) {
      $contVenta = 0;                     
      $contGasto = 0;
      $fecha = date('Y/m/d', mktime(0,0,0, $mes, $i, $anio));
      $fechaMov = date('d-m-Y', mktime(0,0,0, $mes, $i, $anio));    

      if ($vm_scu != "")
         $ventasDia = ventasPeriodoSuc($vm_scu,$fecha,$fecha);
      else
         $ventasDia = ventasPeriodo($fecha,$fecha);

      $ventaDia = $ventasDia['totalVentas'];

      if ($vm_scu != "")
         $gastosDia = gastosPeriodoSuc($vm_scu,$fecha,$fecha);
      else
         $gastosDia = gastosPeriodo($fecha,$fecha);

      $gastoDia = $gastosDia['total'];

      if ($ventaDia > 0 || $gastoDia > 0){ 
         $colores[] = 'rgba(51,82,255,1)';
         $coloresAux[] = 'rgba(137,19,19,1)';
         $totalDia = $ventaDia - $gastoDia; 
         $totalesDia[] = $totalDia;         
         $fechasMov[] = $fechaMov; 
      }                           
   } 
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
       <h5>Total de ventas: <?php echo $totalVentaMensual; ?>Total : <?php echo $total; ?></h5>
    </div>
    <div class="p-6 mb-4 text-center bg-info text-white">
       <h3><?php echo $nomSucursal  ?> GRÁFICA DEL MES: <?php echo $nomMes ?></h3>
    </div>
    <div class="col-lg-12">
      <canvas id="myChart" width="600" height="320"></canvas>
    </div>
  </body>
</html>

<script src="../../libs/js/chartJS/src/Chart.min.js"></script>
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script-->
<script>

var ctx = document.getElementById('myChart').getContext('2d');
Chart.defaults.global.defaultFontColor = 'black';
Chart.defaults.global.defaultFontSize = 17;

var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($fechasMov) ?>,
        datasets: [{
            label: 'Día/Total',
            backgroundColor: <?php echo json_encode($colores) ?>,
            hoverBackgroundColor: <?php echo json_encode($coloresAux) ?>,
            data: <?php echo json_encode($totalesDia) ?>,
        }]
    },
});

</script>