<?php
  $page_title = 'Histórico estancia';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  $mascota= isset($_POST['mascota']) ? $_POST['mascota']:'';
 
  if ($mascota!="")
     $historico = histEstanciaMasc($mascota);
  else
     $historico = histEstancia();
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Histórico estancia</title>
</head>

<body onload="focoMascota();">
  <form name="form1" method="post" action="histEstancia.php">
  <br>

  <div class="row">
     <div class="col-md-12">
        <?php echo display_msg($msg); ?>
     </div>
     <div class="col-md-12">
        <div class="panel panel-default">
           <div class="panel-heading clearfix">
              <div class="pull-right">
                 <div class="form-group">
                    <div class="col-md-4">
                       <div class="input-group">
                          <span class="input-group-addon">
                             <i class="glyphicon glyphicon-list-alt"></i>
                          </span>
                          <input type="text" class="form-control" name="mascota" long="21">
                       </div>
                    </div>
                    <a href="#" onclick="histEstancia();" class="btn btn-primary">Buscar</a>
                    <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
                 </div>   
              </div>   
           </div>
        </div>
        <div class="panel-body">
           <table class="table table-bordered">
           <thead>
              <tr>
                 <th class="text-center" style="width: 5%;">#</th>
                 <th class="text-center" style="width: 10%;">Nombre de la mascota</th>
                 <th class="text-center" style="width: 20%;">Responsable</th>
                 <th class="text-center" style="width: 5%;">Estatus</th>
                 <th class="text-center" style="width: 7%;">Fecha entrada</th>
                 <th class="text-center" style="width: 7%;">Fecha salida</th>
                 <th class="text-center" style="width: 5%;">Hora</th>
                 <th class="text-center" style="width: 20%;">Cliente</th>
                 <th class="text-center" style="width: 10%;">Teléfono</th>
              </tr>
           </thead>
           <tbody>
              <?php foreach ($historico as $product):?>
              <tr>
                 <td class="text-center"><?php echo count_id();?></td>
                 <td class="text-center"> <?php echo remove_junk($product['nombre']); ?></td>
                 <td class="text-center"> <?php echo remove_junk($product['responsable']); ?></td>
                 <td class="text-center"> <?php echo remove_junk($product['estatus']); ?></td> 
                 <td class="text-center"> <?php echo remove_junk($product['fecha']); ?></td>
                 <td class="text-center"> <?php echo remove_junk($product['fechaSalida']); ?></td>
                 <td class="text-center"> <?php echo remove_junk($product['hora']); ?></td>
                 <td class="text-center"> <?php echo remove_junk($product['nom_cliente']); ?></td>
                 <td class="text-center"> <?php echo remove_junk($product['tel_cliente']); ?></td>
              </tr>
             <?php endforeach; ?>
           </tbody>
           </table>
        </div>
     </div>
  </div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
