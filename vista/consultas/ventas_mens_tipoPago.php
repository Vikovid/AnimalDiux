<?php
   $page_title = 'Venta mensual por tipo de pago';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $all_tiposPago = find_all('tipo_pago');

   $tipoPago = "";
   $mes = "";
   $anio = "";
 
   if(isset($_POST['tipoPago'])){  
      $tipoPago =  remove_junk($db->escape($_POST['tipoPago']));//prueba
   }

   if(isset($_POST['mes'])){  
      $mes =  remove_junk($db->escape($_POST['mes']));//prueba
   }

   if(isset($_POST['anio'])){  
      $anio =  remove_junk($db->escape($_POST['anio']));//prueba
   }

   if ($mes == "" && $anio == ""){                          
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
      $fechaInicial = $year."/".$month."/01";
      $fechaFinal = $year."/".$month."/".$day;
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

   if($tipoPago!=""){
      $ventasTipoPago = ventasTipoPagoPerTipo($tipoPago,$fechaIni,$fechaFin);
      $totalVenta = totalVentasTipoPagoPer($tipoPago,$fechaIni,$fechaFin);
      if ($tipoPago == "1")
         $efectivo = $totalVenta['total'];
      if ($tipoPago == "2")
         $transferencia = $totalVenta['total'];
      if ($tipoPago == "3")
         $deposito = $totalVenta['total'];
      if ($tipoPago == "4")
         $tarjeta = $totalVenta['total'];
   }else{
      $ventasTipoPago = ventasTipoPagoPer($fechaIni,$fechaFin);
      $totalVentaEfec = totalVentasTipoPagoPer("1",$fechaIni,$fechaFin);
      $totalVentaTrans = totalVentasTipoPagoPer("2",$fechaIni,$fechaFin);
      $totalVentaDep = totalVentasTipoPagoPer("3",$fechaIni,$fechaFin);
      $totalVentaTar = totalVentasTipoPagoPer("4",$fechaIni,$fechaFin);
      $efectivo = $totalVentaEfec['total'];
      $transferencia = $totalVentaTrans['total'];
      $deposito = $totalVentaDep['total'];
      $tarjeta = $totalVentaTar['total'];
   }
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function ventas(){
  document.form1.action = "ventas_mens_tipoPago.php";
  document.form1.submit();
}

function excel(){
  document.form1.action = "../excel/ventasTipoPago.php";
  document.form1.submit();
}

function foco(){
  document.form1.tipoPago.focus();
}
</script>


<!DOCTYPE html>
<html>
<head>
<title>Ventas mensual por tipo de pago</title>
</head>

<body onload="foco();">
<form name="form1" method="post" action="ventas_mens_tipoPago.php">
   <span>Período:</span>
   <?php echo "del $fechIni al $fechFin "; 
         if ($efectivo > 0)
            echo "   Efectivo: ".$efectivo."  ";
         if ($transferencia > 0)  
            echo "   Transferencia: ".$transferencia."  ";
         if ($deposito > 0)
            echo "  Depósito: ".$deposito."  ";
         if ($tarjeta > 0)
            echo "   Tarjeta: ".$tarjeta."  "; 
         ?>

   <div class="row">
      <div class="col-md-6">
         <?php echo display_msg($msg); ?>
      </div>
   </div>
   <div class="row">
      <div class="col-md-8">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div class="form-group">
                  <div class="col-md-3">
                     <select class="form-control" name="tipoPago">
                        <option value="">Forma de pago</option>
                        <?php  foreach ($all_tiposPago as $id): ?>
                        <option value="<?php echo (int)$id['id_pago'] ?>">
                        <?php echo $id['tipo_pago'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>  
                  <div class="col-md-3">
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
                  <a href="#" onclick="ventas();" class="btn btn-primary">Buscar</a>      
                  <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">   
               </div>
            </div>
            <div class="panel-body">
               <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 20%;"> Día </th>
                     <th class="text-center" style="width: 20%;"> Forma de pago</th>
                     <th class="text-center" style="width: 20%;"> Cantidad </th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($ventasTipoPago as $venta):?>
                  <tr>
                     <td class="text-center"><?php echo date("d-m-Y", strtotime ($venta['fecha'])); ?></td>
                     <td class="text-center"><?php echo $venta['tipo_pago']; ?></td>
                     <td class="text-right"><?php echo remove_junk($venta['cantidad']); ?></td>
                  </tr>
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
