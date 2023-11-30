<?php
   require_once('../../modelo/load.php');
   $page_title = 'Estancia';
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $resultado = buscaEstancias();      
?>
<?php include_once('../layouts/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Estancia</title>
</head>

</script>
<body>
  <form name="form1" method="post" action="estancia.php">
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
                                <h3>Estancia</h3>
                             </span>
                          </div>
                       </div>
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
                    <th class="text-center" style="width: 25%;">Responsable</th>
                    <th class="text-center" style="width: 5%;">Estatus</th>
                    <th class="text-center" style="width: 8%;">Fecha de salida</th>
                    <th class="text-center" style="width: 7%;">Hora de salida</th>
                    <th class="text-center" style="width: 5%;">Id Cliente</th>
                    <th class="text-center" style="width: 25%;">Cliente</th>
                    <th class="text-center" style="width: 8%;">Teléfono</th>
                    <th class="text-center" style="width: 5%;">Acción</th>
                 </tr>
              </thead>
              <tbody>
                 <?php foreach ($resultado as $product):?>
                 <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td class="text-center"> <?php echo remove_junk($product['nombre']); ?></td>
                    <td class="text-center"> <?php echo remove_junk($product['Encargado']); ?></td>
                    <td class="text-center"> <?php echo remove_junk($product['estatus']); ?></td> 
                    <td class="text-center"> <?php echo date("d-m-Y", strtotime ($product['fecha_salida'])); ?></td>
                    <td class="text-center"> <?php echo remove_junk($product['Hora_salida']); ?></td>
                    <td class="text-center"> <?php echo remove_junk($product['idcredencial']); ?></td>
                    <td class="text-center"> <?php echo remove_junk($product['nom_cliente']); ?></td>
                    <td class="text-center"> <?php echo remove_junk($product['tel_cliente']); ?></td>
                    <td class="text-center">
                       <div class="btn-group">
                          <a href="delete_estancia.php?id=<?php echo (int)$product['idestancia'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                          <span class="glyphicon glyphicon-remove"></span>
                          </a>
                       </div>
                    </td>
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
