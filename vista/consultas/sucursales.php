<?php
  $page_title = 'Lista de sucursales';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $sucursal = consultaCampos("nom_sucursal,direccion,telefono","sucursal");
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix"></div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
               <tr>
                  <th class="text-center" style="width: 45%;"> Sucursal </th>
                  <th class="text-center" style="width: 45%;"> Dirección </th>
                  <th class="text-center" style="width: 10%;"> Teléfono </th>
               </tr>
               </thead>
               <tbody>
               <?php foreach ($sucursal as $sucursal):?>
               <tr>
                  <td> <?php echo remove_junk($sucursal['nom_sucursal']); ?></td>
                  <td> <?php echo remove_junk($sucursal['direccion']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($sucursal['telefono']); ?></td>
               </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
