<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar estética';
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $idmas= $_GET['idMas'];
  
   $estetica = buscaRegistroPorCampo('estetica','idestetica',(int)$_GET['idestetica']);
   $hora = $estetica['hora'];

   $encargados = find_all('users');

   if(!$estetica){
      $session->msg("d","Missing estetica id.");
      redirect('history.php');
   }

   if(isset($_POST['responsable'])){
      $req_fields = array('responsable','nota');
      validate_fields($req_fields);

      if(empty($errors)){
         $e_responsable = remove_junk($db->escape($_POST['responsable']));
         $e_nota = remove_junk($db->escape($_POST['nota']));
         $e_hora_ent = remove_junk($db->escape($_POST['hora_ent']));

         $resultado = actEstetica($e_responsable,$e_nota,$e_hora_ent,$estetica['idestetica']);

         if($resultado){
            $session->msg('s',"Estética ha sido actualizada. ");
            redirect('edit_estetica.php?idestetica='.$estetica['idestetica'].'&idMas='.$estetica['idMascota'], false);
         }else{
            $session->msg('d',' Lo siento, falló la actualización.');
            redirect('edit_estetica.php?idestetica='.$estetica['idestetica'].'&idMas='.$estetica['idMascota'], false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('edit_estetica.php?idestetica='.$estetica['idestetica'].'&idMas='.$estetica['idMascota'], false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Edición de estética</title>
</head>

<body onload="valorAntEstetica();">
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
            <span>Editar estética</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
         <form method="post" name="form1" action="edit_estetica.php?idestetica=<?php echo (int)$estetica['idestetica'] ?>&idMas=<?php echo $estetica['idMascota']?>">
            <div class="form-group">
               <div class="row">
                  <label class="col-sm-2 col-form-label">Responsable:</label>
                  <div class="col-md-5">
                     <select class="form-control" name="responsable">
                        <option value="">Selecciona responsable</option>
                        <?php  foreach ($encargados as $resp): ?>
                        <option value="<?php echo $resp['name']; ?>" <?php if($estetica['responsable'] === $resp['name']): echo "selected"; endif; ?> >
                        <?php echo remove_junk($resp['name']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group row">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <p><textarea name="nota" class="form-control" placeholder="observaciones" maxlength="500" rows="5" style="resize: none"><?php echo remove_junk($estetica['observaciones']);?></textarea></p>
               </div>
            </div>
            <div class="form-group row">
               <label class="col-sm-3 col-form-label">Hora de entrega:</label>
               <div class="col-sm-4">
                  <select class="form-control" name="hora_ent">
                     <option>10:00</option> 
                     <option>10:30</option>
                     <option>11:00</option>
                     <option>11:30</option>
                     <option>12:00</option>
                     <option>12:30</option>
                     <option>13:00</option>
                     <option>13:30</option>
                     <option>14:00</option>
                     <option>14:30</option>
                     <option>15:00</option> 
                     <option>15:30</option>
                     <option>16:00</option>
                     <option>16:30</option>
                     <option>17:00</option>
                     <option>17:30</option>
                     <option>18:00</option>
                  </select>
               </div>
            </div>  
            <input type="hidden" value="<?php echo $hora ?>" name="hora">
            <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
            <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
            <button type="submit" name="vacuna" class="btn btn-danger">Actualizar</button>
         </form>
         </div>
      </div>
   </div>
</div>
</body>  
</html>
<?php include_once('../layouts/footer.php'); ?>
