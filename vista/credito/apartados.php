<?php
  $page_title = 'Monto créditos';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  $apartados = apartadosCliente();
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Monto créditos</span>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 65%;">Cliente </th>
                     <th class="text-center" style="width: 15%;">Monto del crédito</th>
                     <th class="text-center" style="width: 10%;">Fecha </th>
                     <th class="text-center" style="width: 15%;">Acciones</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($apartados as $apartado):?>
                  <tr>
                     <td><?php echo remove_junk($apartado['cliente']); ?></td>
                     <td class="text-right"><?php echo "$".$apartado['monto']; ?></td>
                     <td class="text-center"><?php echo date("d-m-Y", strtotime ($apartado['fecha'])); ?></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="edit_apartado.php?idCredencial=<?php echo (int)$apartado['idCredencial'];?>" class="btn btn-info btn-xs" title="Abonar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-usd"></span>
                           </a>
                           <a href="admin_apartado.php?idCredencial=<?php echo (int)$apartado['idCredencial'];?>" class="btn btn-danger btn-xs"  title="Administrar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-inbox"></span>
                           </a>
                           <a href="detalleCredito.php?idCredencial=<?php echo (int)$apartado['idCredencial'];?>" class="btn btn-primary btn-xs"  title="Detalle" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-list-alt"></span>
                           </a>
                        </div>
                     </td>
                  </tr>
                  <?php endforeach;?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
