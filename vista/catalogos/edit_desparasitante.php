<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar desparasitante';
   // Checkin What level user has permission to view this page
   page_require_level(3);
   $desparasitante = find_by_id('desparasitantes',(int)$_GET['id']);

   if(!$desparasitante){
      $session->msg("d","Missing desparasitante id.");
      redirect('desparasitante.php');
   }
   if(isset($_POST['desparasitante'])){
      $req_fields = array('nombre');
      validate_fields($req_fields);

      if(empty($errors)){
         $d_nombre  = remove_junk($db->escape($_POST['nombre']));

         $resultado = actRegistroPorCampo('desparasitantes','nombre',$d_nombre,'id',$desparasitante['id']);

         if($resultado){
            $session->msg('s',"Desparasitante ha sido actualizado.");
            redirect('desparasitante.php', false);
         }else{
            $session->msg('d',' Lo siento, falló la actualización.');
            redirect('edit_desparasitante.php?id='.$desparasitante['id'], false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('edit_desparasitante.php?id='.$desparasitante['id'], false);
      }
   }
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
            <span>Editar desparasitante</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
            <form method="post" action="edit_desparasitante.php?id=<?php echo (int)$desparasitante['id'] ?>">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" value="<?php echo remove_junk($desparasitante['nombre']);?>">
                  </div>
               </div>
               <button type="submit" name="desparasitante" class="btn btn-danger">Actualizar</button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>