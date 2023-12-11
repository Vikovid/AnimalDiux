<?php
   $page_title = 'Detalle crédito';
   require_once('../../modelo/load.php');

   // Checkin What level user has permission to view this page
   page_require_level(3);

   $detalle = histCredito((int)$_GET['idCredencial']);
   $clientez = buscaRegistroPorCampo('cliente','idcredencial',(int)$_GET['idCredencial']);
?>

<?php include_once('../layouts/header.php'); ?>

<form method="post" action="detalleCredito.php?idCliente=<?php echo (int)$_GET['idCredencial']; ?>">

<div class="row col-md-9">
   <?php echo display_msg($msg); ?>
</div>
<div class="row col-md-9">    
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Detalle de pagos del cliente: <?php echo remove_junk($clientez['nom_cliente']); ?></span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 5%;"> Pago </th>
                     <th class="text-center" style="width: 8%;"> Fecha Pago </th>
                     <th class="text-center" style="width: 8%;"> Hora Pago </th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($detalle as $det):?>
                  <tr>
                     <td class="text-center"><?php echo '$'.money_format('%.2n',$det['pago']); ?></td> 
                     <td class="text-center"><?php echo date("d-m-Y", strtotime ($det['fechaPago'])); ?></td>
                     <td class="text-center"><?php echo date("H:i:s", strtotime ($det['horaPago'])).'hrs.'; ?></td>
                  </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

</form>

<?php include_once('../layouts/footer.php'); ?>