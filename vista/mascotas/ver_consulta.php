<?php
   require_once('../../modelo/load.php');
   $page_title = 'Vista de consulta';
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $consulta = buscaRegistroPorCampo('Consulta','idconsulta',(int)$_GET['idconsulta']);
   $idMas = $consulta['idMascota'];
 
   $mascota= buscaRegistroPorCampo('Mascotas','idMascotas',$idMas);
   $nombre=$mascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>

<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Vista de consulta de:</span>
               <span><?php echo $nombre ?></span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form name="form1" method="post" action="history.php" class="clearfix">
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-scale"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="peso" placeholder="Peso" value="<?php echo remove_junk($consulta['peso']); ?>" readonly>Kg
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-info-sign"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="temp" placeholder="Temperatura" value="<?php echo remove_junk($consulta['temperatura']); ?>" readonly>C
                  </div>
               </div>
               <br>
               <br>
               <br>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="problema" class="form-control" placeholder="Historial clínico" maxlength="300" rows="3" style="resize: none" readonly><?php echo remove_junk($consulta['problema']); ?></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="diagnostico" class="form-control" placeholder="Diagnóstico" maxlength="100" rows="1" style="resize: none" readonly><?php echo remove_junk($consulta['diagnostico']); ?></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="receta" class="form-control" placeholder="Receta" maxlength="1204" rows="12" style="resize: none" readonly><?php echo remove_junk($consulta['consulta']); ?></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="receta" class="form-control" placeholder="Nota" maxlength="100" rows="1" style="resize: none" readonly><?php echo remove_junk($consulta['nota']); ?></textarea></p>
                  </div>
               </div>
               <input type="hidden"  class="form-control" value="<?php echo $idMas ?>" name="idMascotas">
               <button type="submit" name="consulta" class="btn center-block btn-danger">Regresar</button>
            </form>
         </div>   
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
