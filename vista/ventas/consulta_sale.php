<?php
  $page_title = 'Consulta de venta';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $idTicket = $_GET['idTicket'];
  $vendedor = $_GET['vendedor'];
  $fecha    = $_GET['fecha'];
  $cliente  = $_GET['cliente'];
  $abonoTotal = $_GET['total'];

  $fechaFin = strtotime ($fecha);
  $fechaFin = date ('d-m-Y',$fechaFin);
  $total = 0;
?>
<?php include_once('../layouts/header.php'); ?>

<body>
  <form name="form1" method="post" action="">
  <br>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span><?php if ($cliente != ""){ echo "Cliente: $cliente";?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?></span>
               <span><?php echo "Fecha: $fechaFin"; ?></span>   
               <span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo "Vendedor: $vendedor"; ?></span>   
            </strong>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Productos</span>
            </strong>
         </div>
         <div class="panel-body">
            <table class="table table-striped table-bordered table-condensed">
               <?php $ventas = ventas($idTicket); ?>              

               <?php 
                   foreach ($ventas as $venta):
                      $tipoPago = $venta['tipo_pago'];
                      break;
                   endforeach; 
                ?>
                <thead>
                   <tr>
                      <th>Producto</th>
                      <?php if ($tipoPago == "0"){ ?>
                         <th>Cantidad</th>
                      <?php } ?>   
                      <th>Total</th>
                   </tr>
                </thead>
                <tr>
                   <tbody>

                   <?php reset($ventas); ?>

                   <?php foreach ($ventas as $venta):?>
                      <td> <?php echo remove_junk($venta['name']); ?></td>
                   <?php if ($tipoPago == "0"){ ?>   
                      <td> <?php echo remove_junk($venta['qty']); ?></td>
                   <?php } ?>   
                      <td> <?php echo remove_junk($venta['price']); ?></td>
                </tr>
                   <?php 
                         endforeach; 
                   ?>
                   </tbody>
            </table>
         </div>
      </div>
      <?php if ($abonoTotal != ""){ ?>
         <div class="col-md-5">
            <div class="panel panel-default">
               <strong>
                  <span style="font-size: 30px;">Total: <?php echo $abonoTotal; ?></span>
               </strong>
            </div>
         </div>
         <a href="../pdf/ticketCredito.php?idTicket=<?php echo $idTicket;?>" class="btn btn-danger">Imprimir ticket</a>
      <?php } ?>   
   </div>

   <?php if ($tipoPago == "0"){ ?>
   <div class="col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Forma de Pago:</span>
            </strong>
         </div>
         <div class="panel-body">
            <table class="table table-striped table-bordered table-condensed">
               <thead>
                  <tr>
                     <th>Tipo de Pago</th>
                     <th>Total</th>
                  </tr>
               </thead>
                  <tr>
                     <tbody>
                        <?php $tiposPago = tipoPago($idTicket); ?>
                        <?php foreach ($tiposPago as $tipoPago):?>
                        <td> <?php echo remove_junk($tipoPago['tipo_pago']); ?></td>
                        <td> <?php echo remove_junk($tipoPago['cantidad']); ?></td>
                  </tr>
                  <?php 
                     $cantidad = $tipoPago['cantidad'];
                     $total = $total + $cantidad;
                     endforeach; 
                  ?>
                     </tbody>
            </table>
         </div>
      </div>
      <div class="col-md-6">
         <div class="panel panel-default">
            <strong>
               <span>&nbsp;&nbsp;</span>
               <span style="font-size: 30px;">Total: <?php echo $total; ?></span>
            </strong>
         </div>
      </div>
      <a href="../pdf/ticketVentas.php?idTicket=<?php echo $idTicket;?>" class="btn btn-danger">Imprimir ticket</a> 
   </div>
   <?php } ?>   
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>
