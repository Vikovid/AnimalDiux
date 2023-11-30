<?php
   require_once('../../modelo/load.php');
   $page_title = 'Lista de Medicamentos Inyectables';
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $solucion = find_all("soluciones");

   $descripcion = isset($_POST['nombre']) ? $_POST['nombre']:'';
 
   if ($descripcion!="")
      $solucion = buscaSoluciones($descripcion);
   else
      $solucion = find_all("soluciones");
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Lista de Medicamentos Inyectables</title>
</head>

<body onload="focoNombre();">
   <form name="form1" method="post" action="solucion.php">
      <br>
      <div class="row">
         <div class="col-md-9">
            <?php echo display_msg($msg); ?>
         </div>
         <div class="col-md-9">
            <div class="panel panel-default">
               <div class="panel-heading clearfix">
                  <div class="pull-right">
                     <div class="form-group">
                        <div class="col-md-6">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-barcode"></i>
                              </span>
                              <input type="text" class="form-control" name="nombre" long="50">
                           </div>
                        </div>  
                        <a href="#" onclick="soluciones();" class="btn btn-primary">Buscar</a> 
                        <a href="add_solucion.php" class="btn btn-primary">Agregar</a>            
                     </div>   
                  </div>   
               </div>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 3%;">#</th>
                        <th class="text-center" style="width: 72%;">Nombre</th>
                        <th class="text-center" style="width: 20%;">Cantidad</th>
                        <th class="text-center" style="width: 5%;">Acciones</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($solucion as $solucion):?>
                     <tr>
                        <td class="text-center"><?php echo count_id();?></td>
                        <td> <?php echo utf8_decode($solucion['nombre']); ?></td>
                        <td class="text-center"> <?php echo remove_junk($solucion['cantidad']); ?></td>
                        <td class="text-center">
                           <div class="btn-group">
                              <a href="edit_solucion.php?id=<?php echo (int)$solucion['id'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-edit"></span>
                              </a>
                              <a href="delete_solucion.php?id=<?php echo (int)$solucion['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
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