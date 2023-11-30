<?php
  $page_title = 'Corte de la quincena';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $encargados = find_all('users');
  
  ini_set('date.timezone','America/Mexico_City');
  $fechaInicio = date("Y/m/01");

  $c_idEncargado = "";

  if (isset($_POST['encargado'])){  
     $c_idEncargado =  remove_junk($db->escape($_POST['encargado']));//prueba
  }  

  $parametros = find_by_id("parametros","1");
  $porcComision = $parametros['comision'];

  if (date("j") < 16){
     $fechaFinPrimerQuin = strtotime('+14 day',strtotime($fechaInicio)); 
     $fechaFinal = date ('Y/m/d',$fechaFinPrimerQuin);
     $fechaIni = strtotime ( $fechaInicio );
     $fechaIni = date ('d-m-Y',$fechaIni);
     $fechaFin = date ('d-m-Y',$fechaFinPrimerQuin);
  }else{
     $fechaIniSegundaQuin = strtotime('+15 day',strtotime($fechaInicio)); 
     $fechaInicio = date ('Y/m/d',$fechaIniSegundaQuin);
     $fechaIni = date ('d-m-Y',$fechaIniSegundaQuin);
     $fechaFinal = date("Y/m/t");
     $fechaFin = strtotime ($fechaFinal);
     $fechaFin = date ('d-m-Y',$fechaFin);
  }

  if ($c_idEncargado!=""){
     $result = find_by_id("users",$c_idEncargado);
     $cortes = cortePeriodoVen($result['username'],$fechaInicio,$fechaFinal);
  }else{
     $cortes = cortePeriodo($fechaInicio,$fechaFinal);
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Corte de la quincena</title>
</head>

<body onload="focoEncargado();">
  <form name="form1" method="post" action="cortes_semana.php">

  <div class="row">
     <div class="col-md-10">
        <?php echo display_msg($msg); ?>
     </div>
     <div class="col-md-9">
        <div class="panel panel-default">
           <div class="panel-heading clearfix">
              <div class="form-group">
                 <div class="col-md-3">
                    <select class="form-control" name="encargado">
                       <option value="">Selecciona vendedor</option>
                       <?php  foreach ($encargados as $id): ?>
                       <option value="<?php echo (int)$id['id'] ?>">
                       <?php echo $id['name'] ?></option>
                       <?php endforeach; ?>
                    </select>
                 </div>                 
                 <a href="#" onclick="corteEncargado();" class="btn btn-primary">Buscar</a>
              </div>   
           </div>
           <div class="panel-body">
              <span><strong><?php echo "Quincena del: $fechaIni al: $fechaFin"; ?></strong></span>
           </div>
           <div class="panel-body">
              <table class="table table-bordered">
              <thead>
                 <tr>
                    <th class="text-center" style="width: #%;">#</th>
                    <th>Vendedor</th>
                    <th class="text-center" style="width: 19%;">Sucursal</th>
                    <th class="text-center" style="width: 17%;">Venta</th>
                    <th class="text-center" style="width: 17%;">Comisión <?php echo $porcComision; ?> %</th>
                 </tr>
              </thead>
              <tbody>
              <?php foreach ($cortes as $ventas): ?>
                 <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td> <?php echo remove_junk($ventas['vendedor']); ?></td>
                    <td class="text-center"> <?php echo remove_junk($ventas['nom_sucursal']); ?></td>
                    <td class="text-right"> <?php echo "$".money_format("%.2n",$ventas['venta']); ?></td>

                    <?php $comision = $ventas['venta'] * ($porcComision/100); ?>

                    <td class="text-right"> <?php echo "$".money_format("%.2n",$comision); ?></td>
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
<?php
/*if (date('y/m/d')<='y/m/15'){
   $week_start = date("Y/m/01");
   $fechaInicio = strtotime ( $week_start ); 
$fechaAuxIni = strtotime ( $week_start ); 
$fechaInicio = date ( 'Y/m/d' , $fechaInicio );
$fechaIni = date('d/m/Y',$fechaAuxIni); 
$fechaFinal = date ( 'Y/m/15');
$fechaFin = date('15/m/Y');
} else {
   $week_start = strtotime('y/m/16');
   $week_start = date('Y/m/d',$week_start);
   $fechaInicio = strtotime ( $week_start ); 
$fechaAuxIni = strtotime ( $week_start ); 
$fechaInicio = date ( 'Y/m/d' , $fechaInicio );
$fechaIni = date('d/m/Y',$fechaAuxIni); 
$fechaFinal = date ( 'Y/m/t');
$fechaFin = date('t/m/Y');
}*/
?>
