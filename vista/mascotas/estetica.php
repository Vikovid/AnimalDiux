<?php
   require_once('../../modelo/load.php');
   $page_title = 'Estetica';  
   
   // Checkin What level user has permission to view this page
   page_require_level(3);
   
   $encargados = find_all('users');

   $idmas = isset($_GET['idMas']) ? $_GET['idMas']:'';

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual = date('Y-m-d',time());

   if(isset($_POST['responsable'])){

      $idMasc =     remove_junk($db->escape($_POST['id']));
      $req_fields = array('responsable','observaciones');
      validate_fields($req_fields);

      if (empty($errors)) {
         
         $observaciones = remove_junk($db->escape($_POST['observaciones']));
         $responsable =   remove_junk($db->escape($_POST['responsable']));
         $hora_ent =      remove_junk($db->escape($_POST['hora_ent']));
         $id =            remove_junk($db->escape($_POST['id']));

         $resultado = altaEstetica($responsable,$observaciones,'0',$hora_ent,$fecha_actual,$id,'');

         altaEstancia($id,'Estetica','0',$responsable,$hora_ent,$fecha_actual,$fecha_actual,'');
         altaHistEstancia($id,'Estetica',$responsable,$fecha_actual,$hora_ent,$fecha_actual);

         if ($resultado) {
            $session->msg('s',"Registro Exitoso. ");
            redirect('history.php?idMascotas='.$idMasc, false);
         } else {
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('estetica.php?idMas='.$idMasc, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('estetica.php?idMas='.$idMasc,false);
      }
   }

   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre =  $mascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="estetica.php">
   <div class="row col-md-8">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-8">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Estética de: <?php echo $nombre ?></span>
            </strong>     
         </div>
         <div class="panel-body clearfix">
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Responsable:</label>
               </div>
               <div class="col-md-8">
                  <select class="form-control" name="responsable">
                     <option value="">Seleccione Responsable</option>
                     <?php  foreach ($encargados as $id): ?>
                        <option value="<?php echo $id['name'] ?>">
                           <?php echo remove_junk($id['name']); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label>Hora de entrega:</label>
               </div>
               <div class="col-md-8">
                  <select class="form-control" name="hora_ent">
                  <?php
                     $hora_inicio = strtotime('10:00');
                     $hora_fin = strtotime('18:00');
                     for ($hora = $hora_inicio; $hora <= $hora_fin; $hora += 1800) { // 1800 segundos = 30 minutos
                        $hora_actual = date('H:i', $hora);
                        echo "<option>$hora_actual</option>";
                     }
                  ?>
                  </select>
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <textarea name="observaciones" class="form-control" placeholder="Escribe las observaciones que te piden los clientes" maxlength="500" rows="5" style="resize: none"></textarea>
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="col-md-4">
                  <label for="inputPassword">Día:</label>
               </div>
               <div class="col-md-8">
                  <strong>
                     <strong><?php echo $time2."  ".$time1;?></strong>
                  </strong>
               </div>
            </div>
            <div class="form-group col-md-12">
               <input type="hidden" value="<?php echo $idmas ?>" name="id">
               <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
               <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
               <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
            </div>
         </div>
      </div>
   </div>
</form>

<?php include_once('../layouts/footer.php'); ?>