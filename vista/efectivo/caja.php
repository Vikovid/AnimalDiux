<?php
  $page_title = 'Agregar efectivo';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $user = current_user(); 
  $usuario = $user['id'];
  $idSucursal = $user['idSucursal'];
  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i:s',time());

  if(isset($_POST['monto'])){
     $req_fields = array('monto');
     validate_fields($req_fields);
     if(empty($errors)){
        $monto  = remove_junk($db->escape($_POST['monto']));

        if($monto > 0){
           $consMonto = buscaRegistroMaximo("caja","id");
           $montoActual=$consMonto['monto'];
           $idCaja = $consMonto['id'];

           $montoFinal = $montoActual + $monto;

           $resultado = actCaja($montoFinal,$fecha_actual,$idCaja);

           if($resultado){
              registrarEfectivo("4",$montoActual,$montoFinal,$idSucursal,$usuario,"",$fecha_actual,$hora_actual);

              $session->msg('s',"Monto agregado exitosamente.");
              redirect('caja.php', false);
           }else{
              $session->msg('d','Lo siento, falló el registro.');
              redirect('caja.php', false);
           }
        }else{
           $session->msg('d','El monto debe ser mayor a cero.');
           redirect('caja.php', false);
        }
     }else{
        $session->msg("d", $errors);
        redirect('caja.php',false);
     }
  }
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
  <div class="col-md-8">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-7">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar efectivo</span>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="60" alt="" align="center">
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-4">
               <form method="post" action="caja.php" class="clearfix">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="monto" placeholder="Monto">
                  </div>
               </div>
               <button type="submit" name="add_product" class="btn btn-danger">Agregar</button>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
