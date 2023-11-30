<?php
  $page_title = 'Administración crédito';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  $apartados = detApartadoXCliente((int)$_GET['idCredencial']);
  $cliente = buscaRegistroPorCampo('cuenta','idCredencial',(int)$_GET['idCredencial']);
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Detalle de crédito del cliente: <?php echo $cliente['cliente']; ?></span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-13">
            <form method="post" action="admin_apartado.php?idCredencial=<?php echo (int)$_GET['idCredencial']; ?>">

            <div class="panel-body">
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 80%;"> Nombre </th>
                        <th class="text-center" style="width: 5%;"> Cantidad </th>
                        <th class="text-center" style="width: 10%;"> Total </th>
                        <th class="text-center" style="width: 5%;"> Acción </th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($apartados as $apartado):?>
                     <tr>
                        <td> <?php echo remove_junk($apartado['name']); ?></td>
                        <td class="text-center"> <?php echo remove_junk($apartado['cantidad']); ?></td>
                        <td class="text-right"> <?php echo remove_junk($apartado['total']); ?></td> 
                        <td class="text-center">
                           <div class="btn-group">
                              <?php if ($apartado['total'] == $apartado['totalVenta']){ ?>
                                 <a href="delete_apartado.php?id=<?php echo (int)$apartado['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-trash"></span>
                                 </a>
                              <?php } ?>
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
