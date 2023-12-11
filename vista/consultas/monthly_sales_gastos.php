<?php
   $page_title = 'Gastos mensuales';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(2);
   $all_proveedor = find_all('proveedor');

   $mes = "";
   $anio = "";
   $prove = "";
  
   if(isset($_POST['mes'])){  
      $mes =  remove_junk($db->escape($_POST['mes']));//prueba
   }

   if(isset($_POST['anio'])){  
      $anio =  remove_junk($db->escape($_POST['anio']));//prueba
   }

   if(isset(($_POST['proveedor']))){
      $prove = remove_junk($db->escape($_POST['proveedor']));
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
   $numDias = date('t', strtotime($fechaIni));

   if ($prove != ""){
      $gastosDia = gastosMAP($prove,$fechaIni,$fechaFin);
      $totalGasto = totalGastosMAP($prove,$fechaIni,$fechaFin);
   }else{
      $gastosDia = gastosMesAnio($fechaIni,$fechaFin);
      $totalGasto = totalGastosMesAnio($fechaIni,$fechaFin);
   }
   $total = $totalGasto['total'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="monthly_sales_gastos.php">
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
   <span>Período:</span>
      <?php echo "del $fechIni al $fechFin "; if ($total > 0) echo " Total: ".$total; ?>
   <div class="row">
      <div class="col-md-12">
         <?php echo display_msg($msg); ?>
      </div>
      <div class="col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div>
                  <div class="form-group">
                     <div class="col-md-2">
                        <select class="form-control" name="proveedor">
                           <option value="">Proveedor</option>
                           <?php  foreach ($all_proveedor as $prov): ?>
                           <option value="<?php echo (int)$prov['idProveedor'] ?>">
                           <?php echo $prov['nom_proveedor'] ?></option>
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
                     <a href="#" onclick="ventasGastosMens();" class="btn btn-primary">Buscar</a>
                     <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
                  </div>   
               </div>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  <thead>
                  <tr>
                     <th class="text-center" style="width: 1%;"> #</th>
                     <th class="text-center" style="width: 10%;"> Descripción</th>
                     <th class="text-center" style="width: 10%;"> Monto de gasto </th>
                     <th class="text-center" style="width: 10%;"> Proveedor </th>
                     <th class="text-center" style="width: 10%;"> Metodo de pago </th>
                     <th class="text-center" style="width: 10%;"> Fecha </th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($gastosDia as $sale):?>
                  <tr>
                     <td class="text-center"><?php echo count_id();?></td>
                     <td class="text-left"><?php echo remove_junk($sale['descripcion']); ?></td>
                     <td class="text-right"><?php echo $sale['total']; ?></td>
                     <td class="text-center"><?php echo remove_junk($sale['nom_proveedor']); ?></td>
                     <td class="text-center"><?php echo remove_junk($sale['tipo_pago']); ?></td>
                     <td class="text-center"><?php echo date("d-m-Y", strtotime ($sale['fecha'])); ?></td>
                  </tr>
                  <?php endforeach;?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</form>
<?php include_once('../layouts/footer.php'); ?>
