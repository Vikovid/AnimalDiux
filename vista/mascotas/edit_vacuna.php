<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar vacuna';
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $idmas= $_GET['idMas'];

   $vacuna = buscaRegistroPorCampo('vacuna','idvacuna',(int)$_GET['idvacuna']);
   $tipoVacuna = $vacuna['vacuna'];

   $encargados = find_all('users');
   $vacunas = find_all('vacunas');

   if(!$vacuna){
      $session->msg("d","Missing vacuna id.");
      redirect('history.php');
   }

   if(isset($_POST['responsable'])){
      $req_fields = array('responsable','fechaCaducidad');
      validate_fields($req_fields);

      if(empty($errors)){
         $v_responsable = remove_junk($db->escape($_POST['responsable']));
         $v_vacuna = remove_junk($db->escape($_POST['tipoVacuna']));
         $v_fechaCad = remove_junk($db->escape($_POST['fechaCaducidad']));
         $v_fechaVacuna = remove_junk($db->escape($_POST['fechaVacuna']));
         $v_nota = remove_junk($db->escape($_POST['nota']));

         $resultado = actVacuna($v_responsable,$v_vacuna,$v_fechaCad,$v_fechaVacuna,$v_nota,$vacuna['idvacuna']);

         if($resultado){
            $session->msg('s',"Vacuna ha sido actualizada. ");
            redirect('edit_vacuna.php?idvacuna='.$vacuna['idvacuna'].'&idMas='.$vacuna['idMascota'], false);
         }else{
            $session->msg('d',' Lo siento, falló la actualización.');
            redirect('edit_vacuna.php?idvacuna='.$vacuna['idvacuna'].'&idMas='.$vacuna['idMascota'], false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('edit_vacuna.php?idvacuna='.$vacuna['idvacuna'].'&idMas='.$vacuna['idMascota'], false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Edición de la vacuna</title>
</head>

<body>
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
            <span>Editar vacuna</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
         <form method="post" name="form1" action="edit_vacuna.php?idvacuna=<?php echo (int)$vacuna['idvacuna'] ?>&idMas=<?php echo $vacuna['idMascota']?>">
            <div class="form-group">
               <div class="row">
                  <label class="col-sm-3 col-form-label">Responsable:</label>
                  <div class="col-md-4">
                     <select class="form-control" name="responsable">
                        <option value="">Selecciona responsable</option>
                        <?php  foreach ($encargados as $resp): ?>
                        <option value="<?php echo $resp['username']; ?>" <?php if($vacuna['responsable'] === $resp['username']): echo "selected"; endif; ?> >
                        <?php echo remove_junk($resp['name']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <div class="row">
                  <label class="col-sm-3 col-form-label">Vacuna:</label>
                  <div class="col-md-4">
                     <select class="form-control" name="tipoVacuna">
                        <option value="">Selecciona vacuna</option>
                        <?php  foreach ($vacunas as $vac): ?>
                        <option value="<?php echo $vac['nombre']; ?>" <?php if($vacuna['vacuna'] === $vac['nombre']): echo "selected"; endif; ?> >
                        <?php echo remove_junk($vac['nombre']); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>
				    <div class="form-group">
               <div class="row">                
                  <label class="col-sm-3 col-form-label">Fecha caducidad:</label>
                  <div class="col-md-4">
                     <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-th-large"></i>
                           </span>
                           <input type="date" class="form-control" name="fechaCaducidad" value="<?php echo remove_junk($vacuna['fechaCaducidad']);?>">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <div class="row">        
                  <label class="col-sm-3 col-form-label">Fecha sig. Vacuna:</label>        
                  <div class="col-md-4">
                     <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-th-large"></i>
                           </span>
                           <input type="date" class="form-control" name="fechaVacuna" value="<?php echo remove_junk($vacuna['fechaSigVacuna']);?>">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="form-group row">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <p><textarea name="nota" class="form-control" placeholder="Nota" maxlength="500" rows="5" style="resize: none"><?php echo remove_junk($vacuna['nota']);?></textarea></p>
               </div>
            </div>
            <input type="hidden" value="<?php echo $tipoVacuna ?>" name="vacunaAux">
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
