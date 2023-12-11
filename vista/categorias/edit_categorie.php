<?php
   $page_title = 'Editar categoría';
   require_once('../../modelo/load.php');

   // Checkin What level user has permission to view this page
   page_require_level(1);

   $categorie = find_by_id('categories',(int)$_GET['id']);
   if(!$categorie){
      $session->msg("d","Missing categorie id.");
      redirect('categorias.php');
   }

   if(isset($_POST['edit_cat'])){
      $req_field = array('categorie-name');
      validate_fields($req_field);
      $cat_name = remove_junk($db->escape($_POST['categorie-name']));

      if(empty($errors)){
         $resultado = actCategoria($cat_name,$categorie['id']);
         if($resultado) {
            $session->msg("s", "Categoría actualizada con éxito.");
            redirect('categorias.php',false);
         }else{
            $session->msg("d", "Lo siento, actualización falló.");
            redirect('edit_categorie.php?id='.$categorie['id'],false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('edit_categorie.php?id='.$categorie['id'],false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>

<div class="row col-md-5">
   <?php echo display_msg($msg); ?>
</div>
<div class="row col-md-5">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Editando <?php echo remove_junk(ucfirst($categorie['name']));?></span>
         </strong>
      </div>
      <div class="panel-body">
         <form method="post" action="edit_categorie.php?id=<?php echo (int)$categorie['id'];?>">
            <div class="form-group">
               <input type="text" class="form-control" name="categorie-name" value="<?php echo remove_junk(ucfirst($categorie['name']));?>">
            </div>
            <button type="submit" name="edit_cat" class="btn btn-primary">Actualizar categoría</button>
         </form>
      </div>
   </div>
</div>

<?php include_once('../layouts/footer.php'); ?>