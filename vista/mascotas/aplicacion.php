<?php
   require_once('../../modelo/load.php');
   $page_title = 'Aplicación';
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $encargados = find_all('users');
   $soluciones = find_all('soluciones');
  
   $idmas= isset($_GET['idMas']) ? $_GET['idMas']:'';
   
   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   if(isset($_POST['aplicacion'])){
      $idMasc  = remove_junk($db->escape($_POST['idMas']));
      $req_fields = array('responsable','solucion','cantidad','fechaCaducidad');
      validate_fields($req_fields);
      if(empty($errors)){
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $solucion  = remove_junk($db->escape($_POST['solucion']));
         $cantidad  = remove_junk($db->escape($_POST['cantidad']));
         $idMas  = remove_junk($db->escape($_POST['idMas']));
         $fechaCaducidad  = remove_junk($db->escape($_POST['fechaCaducidad']));
         $fechaAplicacion  = remove_junk($db->escape($_POST['fechaAplicacion']));
         $nota  = remove_junk($db->escape($_POST['nota']));

         $respCant = buscaRegistroPorCampo('soluciones','id',$solucion);

         $stock = $respCant['cantidad'];
         $nomProd = $respCant['nombre'];

         $stockTotal = $stock - $cantidad;

         if ($stockTotal < 0){
             echo "<script> alert('Está solicitando más de lo disponible');</script>";
         }else{
            $resultado = altaAplicacion($responsable,$solucion,$cantidad,$fecha_actual,$idMas,$fechaCaducidad,$fechaAplicacion,$nota);

            actRegistroPorCampo('soluciones','cantidad',$stockTotal,'id',$solucion);

            if($resultado){
               if ($stockTotal <= 100){
                  $session->msg('s',"Solo queda ".$stockTotal." disponible de ".$nomProd);
                  redirect('aplicacion.php?idMas='.$idMasc, false);
               }else{
                  $session->msg('s',"Registro exitoso.");
                  redirect('clinica.php', false);                
               }
            }else{
               $session->msg('d',' Lo siento, falló el registro.');
               redirect('aplicacion.php?idMas='.$idMasc, false);
            }
         }
      }else{
         $session->msg("d", $errors);
         redirect('aplicacion.php?idMas='.$idMasc,false);
      }
   }
   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre = $mascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

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
               <span>Aplicación de:</span>
               <span><?php echo $nombre ?></span>
            </strong>     
         </div>
         <br>
         <br>
      </div>
      <form name="form1" method="post" action="aplicacion.php">
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Responsable:</label>
            <div class="col-sm-10">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona responsable</option>
                  <?php  foreach ($encargados as $encargados): ?>
                  <option value="<?php echo $encargados['username'] ?>">
                  <?php echo $encargados['name'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Soluciones:</label>
            <div class="col-sm-10">
               <select class="form-control" name="solucion">
                  <option value="">Selecciona solución</option>
                  <?php  foreach ($soluciones as $id): ?>
                  <option value="<?php echo $id['id'] ?>">
                  <?php echo $id['nombre'] ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Cantidad:</label>
            <div class="col-sm-10">
               <input type="number" class="form-control" name="cantidad">
            </div>
         </div>
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Fecha de caducidad:</label>
            <div class="col-sm-9">
               <input type="date" name="fechaCaducidad">
            </div>
         </div>
         <div class="form-group row">
            <label class="col-sm-3 col-form-label">Fecha Sig. Aplicación:</label>
            <div class="col-sm-9">
               <input type="date" name="fechaAplicacion">
            </div>
         </div>
         <div class="form-group row">
            <div class="input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <p><textarea name="nota" class="form-control" placeholder="Nota" maxlength="500" rows="5" style="resize: none"></textarea></p>
            </div>
         </div>
         <input type="hidden" value="<?php echo $idmas ?>" name="idMas">
         <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
         <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
         <button type="submit" name="aplicacion" class="btn btn-danger">Guardar</button>
      </form>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
