<?php
   require_once('../../modelo/load.php');
   $page_title = 'Aplicación';

   // Checkin What level user has permission to view this page
   page_require_level(3);

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual = date('Y-m-d',time());

   $encargados = find_all('users');
   $soluciones = find_all('soluciones');
  
   $idmas = (isset($_GET['idMas']) && $_GET['idMas']) ? $_GET['idMas']:'';   

   if(isset($_POST['aplicacion'])){

      $idMasc  =    remove_junk($db->escape($_POST['idMas']));
      $req_fields = array('responsable','solucion','cantidad');
      validate_fields($req_fields);

      if (empty($errors)) {
         $fechaAplicacion = remove_junk($db->escape($_POST['fechaAplicacion']));
         $responsable =     remove_junk($db->escape($_POST['responsable']));
         $solucion =        remove_junk($db->escape($_POST['solucion']));
         $cantidad =        remove_junk($db->escape($_POST['cantidad']));
         $idMas =           remove_junk($db->escape($_POST['idMas']));
         $nota =            remove_junk($db->escape($_POST['nota']));

         $respCant = buscaRegistroPorCampo('soluciones','id',$solucion);

         $fechaCaducidad = $respCant['fechaCaducidad'];
         $nomProd =        $respCant['nombre'];
         $stock =          $respCant['cantidad'];

         $stockTotal = $stock - $cantidad;

         if($fechaCaducidad <= date('Y-m-d')){
            $session->msg('d',"Solución ya caducada");
            redirect('aplicacion.php?idMas='.$idMasc, false);
         }
         if ($stockTotal < 0) {
            $session->msg('d','No hay soluciones suficientes.');
            redirect('aplicacion.php?idMas='.$idMasc, false);
         }
         
         $resultado = altaAplicacion($responsable,$solucion,$cantidad,$fecha_actual,$idMas,$fechaAplicacion,$nota);
         actRegistroPorCampo('soluciones','cantidad',$stockTotal,'id',$solucion);

         if($resultado){
            $session->msg('s', "Registro Exitoso.");
            redirect('history.php?idMascotas='.$idMasc, false);
         } else {
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('aplicacion.php?idMas='.$idMasc, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('aplicacion.php?idMas='.$idMasc,false);
      }
   }

   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre =  $mascota['nombre'];
?>

<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="aplicacion.php">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Aplicación de: <?php echo $nombre ?></span>
            </strong>     
         </div>
         <div class="panel-body">
            <div class="form-group row col-md-12">
               <label class="col-md-4">Responsable:</label>
               <div class="col-md-8">
                  <select class="form-control" name="responsable">
                     <option value="">Selecciona responsable</option>
                     <?php  foreach ($encargados as $encargados): ?>
                        <option value="<?php echo $encargados['username'] ?>">
                           <?php echo remove_junk($encargados['name']); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>  
            <div class="form-group row col-md-12">
               <label class="col-md-4">Soluciones:</label>
               <div class="col-md-8">
                  <select class="form-control" name="solucion">
                     <option value="">Selecciona solución</option>
                     <?php  foreach ($soluciones as $id): ?>
                        <option value="<?php echo $id['id'] ?>">
                           <?php echo remove_junk($id['nombre']); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>  
            <div class="form-group row col-md-12">
               <label class="col-md-4">Cantidad:</label>
               <div class="col-md-8">
                  <input type="number" class="form-control" step="0.01" min="0" name="cantidad">
               </div>
            </div>
            <div class="form-group row col-md-12">
               <label class="col-md-4" >Siguiente Aplicación:</label>
               <div class="col-md-8">
                  <input type="date" name="fechaAplicacion">
               </div>
            </div>
            <div class="form-group row col-md-12">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <p><textarea name="nota" class="form-control" placeholder="Nota" maxlength="500" rows="5" style="resize: none"></textarea></p>
               </div>
            </div>
            <input type="hidden" value="<?php echo $idmas ?>" name="idMas">
            <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
            
            <button type="submit" name="aplicacion" class="btn btn-danger">Agregar</button>
            <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
         </div>
      </div>
   </div>
</form>
<?php include_once('../layouts/footer.php'); ?>