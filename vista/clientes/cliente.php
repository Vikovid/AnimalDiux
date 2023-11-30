<?php
  $page_title = 'Lista de Clientes';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  $codigo= isset($_POST['Codigo']) ? $_POST['Codigo']:'';

  if ($codigo!="") {
     if (is_numeric($codigo)){
        $cliente = join_cliente_table1a($codigo);
     }else{
        $cliente = join_cliente_table2a($codigo);
     }
  }else{
     $cliente = join_cliente_table();
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Lista de Clientes</title>
</head>
<body onload="foco();">
   <form name="form1" method="post" action="cliente.php">
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
                           <i class="glyphicon glyphicon-barcode"></i>
                        </span>
                        <input type="text" class="form-control" name="Codigo" long="21">
                     </div>
                  </div>  
                  <a href="#" onclick="cliente();" class="btn btn-primary">Buscar</a> 
                  <a href="add_cliente.php" class="btn btn-primary">Agregar Cliente</a>            
               </div>   
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th class="text-center" style="width: 3%;">#</th>
                  <th class="text-center" style="width: 20%;"> Nombre </th>
                  <th class="text-center" style="width: 50%;"> Dirección </th>
                  <th class="text-center" style="width: 10%;"> Número Telefónico </th>
                  <th class="text-center" style="width: 7%;"> Id Cliente </th>
                  <th class="text-center" style="width: 5%;"> Puntos </th>
                  <th class="text-center" style="width: 5%;"> Acciones </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($cliente as $cliente):?>
               <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td> <?php echo remove_junk($cliente['nom_cliente']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($cliente['dir_cliente']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($cliente['tel_cliente']); ?></td> 
                  <td class="text-center"> <?php echo remove_junk($cliente['IdCredencial']); ?></td>
                  <td class="text-center"><?php echo remove_junk(first_character(floor($cliente['venta']))); ?></td>
                  <td class="text-center">
                     <div class="btn-group">
                        <a href="edit_client.php?IdCredencial=<?php echo (int)$cliente['IdCredencial'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_cliente.php?IdCredencial=<?php echo (int)$cliente['IdCredencial'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-trash"></span>
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
