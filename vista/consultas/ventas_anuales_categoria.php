<?php
   $page_title = 'Venta anual por categoría';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $all_sucursal = find_all('sucursal');

   $anio = "";
   $vm_scu = "";

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
<title>Ventas Anuales</title>
</head>

<body onload="focoSucursal();">
  <form name="form1" method="post" action="ventas_anuales_categoria.php">

<?php

   if ($vm_scu != ""){
      $consulta = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
      $sucursal = $consulta['nom_sucursal'];
   }

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
     $ventasCat = monthlycatsuc($vm_scu,$fechaIni,$fechaFin);
   }else{
     $ventasCat = monthlycat1($fechaIni,$fechaFin);
   }
?>

<span>Año:</span>
<?php echo $anio; ?>
<?php if($vm_scu!=""){ ?>
        <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span>Sucursal:</span>
        <?php echo $sucursal; ?>
<?php } ?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-10">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
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
               <a href="#" onclick="ventasAnual();" class="btn btn-primary">Buscar</a>      
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">   
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th> Categoría </th>
                     <th class="text-center" style="width: 20%;"> Cantidad</th>
                     <th class="text-center" style="width: 20%;"> Venta </th>
                     <th class="text-center" style="width: 20%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($ventasCat as $sale):?>
                  <tr>
                     <td><?php echo remove_junk($sale['name']); ?></td>
                     <td class="text-right"><?php echo (int)$sale['cantidad']; ?></td>
                     <td class="text-right"><?php echo remove_junk($sale['precio_total']); ?></td>
                     <td class="text-right"><?php echo remove_junk($sale['ganancia']); ?></td>
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
