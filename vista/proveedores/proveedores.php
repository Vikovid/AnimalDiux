<?php
  $page_title = 'Lista de proveedores';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $proveedor = find_all("proveedor");
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="pull-right">
               <a href="add_proveedores.php" class="btn btn-primary">Agregar Proveedores</a>
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th> Proveedor </th>
                     <th class="text-center" style="width: 40%;"> Dirección </th>
                     <th class="text-center" style="width: 10%;"> Teléfono </th>
                     <th class="text-center" style="width: 30%;"> Contacto </th>
                     <th class="text-center" style="width: 100px;"> Acciones </th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($proveedor as $proveedor):?>
                  <tr>
                     <td> <?php echo remove_junk($proveedor['nom_proveedor']); ?></td>
                     <td class="text-center"> <?php echo remove_junk($proveedor['direccion']); ?></td>
                     <td class="text-center"> <?php echo remove_junk($proveedor['telefono']); ?></td>
                     <td class="text-center"> <?php echo remove_junk($proveedor['contacto']); ?></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="edit_proveedor.php?idProveedor=<?php echo (int)$proveedor['idProveedor'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-edit"></span>
                           </a>
                           <a href="delete_proveedor.php?idProveedor=<?php echo (int)$proveedor['idProveedor'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
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
</div>
<?php include_once('../layouts/footer.php'); ?>
