<?php
  $page_title = 'Corte del día';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $encargados = find_all('users');
  $parametros = find_by_id("parametros","1");

  $porcComision = $parametros['comision'];

  $c_idEncargado = "";

  if (isset($_POST['encargado'])){  
     $c_idEncargado =  remove_junk($db->escape($_POST['encargado']));//prueba
  }  
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Cortes del día</title>
</head>

<body onload="focoEncargado();">
  <form name="form1" method="post" action="cortes.php">

  <?php
     ini_set('date.timezone','America/Mexico_City');
     $fecha=date('Y/m/d',time());

     if ($c_idEncargado!=""){
        $result = find_by_id("users",$c_idEncargado);
        $cortes = corteDiaVendedor($result['username'],$fecha);
     }else{
        $cortes = corteDia($fecha);
     }
  ?>
  <div class="row">
     <div class="col-md-12">
        <?php echo display_msg($msg); ?>
     </div>
     <div class="col-md-10">
        <div class="panel panel-default">
           <div class="panel-heading clearfix">
              <div class="form-group">
                 <div class="col-md-4">
                    <select class="form-control" name="encargado">
                       <option value="">Selecciona vendedor</option>
                       <?php  foreach ($encargados as $id): ?>
                       <option value="<?php echo (int)$id['id'] ?>">
                       <?php echo $id['name'] ?></option>
                       <?php endforeach; ?>
                    </select>
                 </div>                 
                 <a href="#" onclick="corteDia();" class="btn btn-primary">Buscar</a>
              </div>   
           </div>
           <div class="panel-body">
              <table class="table table-bordered">
                 <thead>
                    <tr>
                       <th class="text-center" style="width: 30px;">#</th>
                       <th> Vendedor </th>
                       <th class="text-center" style="width: 25%;"> Sucursal </th>
                       <th class="text-center" style="width: 15%;"> Venta </th>
                       <th class="text-center" style="width: 15%;"> Comisión </th>
                       <th class="text-center" style="width: 25%;"> Fecha </th>
                    </tr>
                 </thead>
                 <tbody>
                 <?php foreach ($cortes as $ventas):?>
                    <tr>
                       <td class="text-center"><?php echo count_id();?></td>
                       <td> <?php echo remove_junk($ventas['vendedor']); ?></td>
                       <td class="text-center"> <?php echo remove_junk($ventas['nom_sucursal']); ?></td>
                       <td class="text-center"> <?php echo "$".money_format("%.2n",$ventas['venta']); ?></td>
                 
                       <?php $comision = $ventas['venta'] * ($porcComision/100); ?>
       
                       <td class="text-center"> <?php echo "$".money_format("%.2n",$comision); ?></td>
                       <td class="text-center"> <?php echo date("d-m-Y", strtotime ($ventas['date'])); ?></td>
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
