<?php
   $page_title = 'Lista de gastos';
   require_once('../../modelo/load.php');
   
   // Checkin What level user has permission to view this page
   page_require_level(2);
   
   $gasto = join_gastos_table2();
?>

<?php include_once('../layouts/header.php'); ?>
   <div class="row col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="pull-right">
               <a href="add_gastos.php" class="btn btn-primary">Agregar Gastos</a>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th style="width: 18%;"> Proveedor </th>
                     <th style="width: 18%;"> Descripción </th>
                     <th style="width: 15%;"> Categoría </th>
                     <th class="text-center" style="width: 8%;"> Subtotal </th>
                     <th class="text-center" style="width: 8%;"> IVA </th>
                     <th class="text-center" style="width: 8%;"> Total </th>
                     <th class="text-center" style="width: 10%;"> Forma de Pago </th>
                     <th class="text-center" style="width: 10%;"> Fecha </th>
                     <th class="text-center" style="width: 5%;"> Acciones </th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($gasto as $gasto):?>
                  <tr>
                     <td> <?php echo remove_junk($gasto['nom_proveedor']); ?></td>
                     <td> <?php echo remove_junk($gasto['descripcion']); ?></td>
                     <td> <?php echo remove_junk($gasto['name']); ?></td>
                     <td class="text-right"><?php echo '$'.$gasto['monto']; ?></td>
                     <td class="text-right"><?php echo '$'.$gasto['iva']; ?></td>
                     <td class="text-right"><?php echo '$'.$gasto['total']; ?></td>
                     <td class="text-center"><?php echo remove_junk($gasto['tipo_pago']); ?></td>
                     <td class="text-center"><?php echo date("d-m-Y", strtotime ($gasto['fecha'])); ?></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="edit_gasto.php?id=<?php echo (int)$gasto['id'];?>&idProveedor=<?php echo (int)$gasto['idProveedor'];?>&idCategoria=<?php echo (int)$gasto['categoria'];?>&id_pago=<?php echo (int)$gasto['id_pago'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                              <span class="glyphicon glyphicon-edit"></span>
                           </a>
                           <a href="delete_gasto.php?id=<?php echo (int)$gasto['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
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
<?php include_once('../layouts/footer.php'); ?>