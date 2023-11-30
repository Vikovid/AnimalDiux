<?php
  $page_title = 'Consulta de ventas diarias';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $all_sucursal = find_all('sucursal');

  $vm_scu = "";
  $sucursal = "";

  if (isset($_POST['sucursal'])){  
     $vm_scu =  remove_junk($db->escape($_POST['sucursal']));//prueba
  }
 
  ini_set('date.timezone','America/Mexico_City');
  $fecha=date('Y/m/d',time());
  $sales = dailySales($fecha);  
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Venta del Día</title>
</head>

<body onload="focoSucursal();">
  <form name="form1" method="post" action="ventas-diarias.php">

<?php

   if($vm_scu!=""){
      $sales = dailySalesSuc($fecha,$vm_scu);
      $consVenta = ventaDiaSuc($fecha,$vm_scu);
      $consGanancia = gananciaDiaSuc($fecha,$vm_scu);
      $consSucursal= buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
      $sucursal=$consSucursal['nom_sucursal'];
   }else{
      $sales = dailySales($fecha);
      $consVenta= ventaDia($fecha);
      $consGanancia = ganaciaDia($fecha);
   }

   $totalVentaDia = $consVenta['venta'];
   $totalGananciaDia = $consGanancia['ganancia'];

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
                  <div class="col-md-4">
                     <select class="form-control" name="sucursal">
                        <option value="">Selecciona una sucursal</option>
                        <?php  foreach ($all_sucursal as $id): ?>
                        <option value="<?php echo (int)$id['idSucursal'] ?>">
                        <?php echo $id['nom_sucursal'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>  
                  <a href="#" onclick="ventasDelDia();" class="btn btn-primary">Buscar</a> 
			            <div class="pull-right">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>                    
                        <span>Venta del día es:</span>
                        <?php echo $totalVentaDia; ?>
                        <br>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Ganancia del día es:</span>
                        <?php echo $totalGananciaDia; ?>
                        <br>
                        <?php if ($vm_scu != ""){ ?>
                           <span class="glyphicon glyphicon-th"></span>
                           <span>Sucursal:</span>                  
                           <?php echo $sucursal;
                           }
                           ?>
                     </strong>
                  </div>
               </div>   
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 30%%;"> Descripción </th>
                     <th class="text-center" style="width: 10%%;"> Cantidad vendida </th>
                     <th class="text-center" style="width: 20%%;"> &nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp; </th>
                     <th class="text-center" style="width: 20%%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($sales as $sales):?>
                  <tr>
                     <td> <?php echo remove_junk($sales['name']); ?></td>
                     <td class="text-right"> <?php echo remove_junk($sales['total_ventas']); ?></td>
                     <td class="text-right"> <?php echo remove_junk($sales['precio_total']); ?></td>
                     <td class="text-right"><?php echo remove_junk($sales['ganancia']); ?></td>
                  </tr>
                  <?php endforeach; ?>
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
