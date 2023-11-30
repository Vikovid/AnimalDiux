<?php
  $page_title = 'Editar subcategoría';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $subcategoria = find_by_id('subcategorias',(int)$_GET['id']);
  $idCategoria = $_GET['idCat'];
  if(!$subcategoria){
    $session->msg("d","Falta el id de la subcategoria");
    redirect('subcategorias.php?idCat='.$idCategoria);
  }

  if(isset($_POST['editar'])){
     $req_field = array('subcategoria');
     validate_fields($req_field);
     $nombre = remove_junk($db->escape($_POST['subcategoria']));
     if(empty($errors)){
        $resultado = actSubcategoria($nombre,$subcategoria['id']);
        if($resultado) {
           $session->msg("s","Subcategoría actualizada con éxito.");
           redirect('subcategorias.php?idCat='.$subcategoria['idCategoria'],false);
        }else{
           $session->msg("d","Lo siento, falló la actualización.");
           redirect('editarSubCategoria.php?id='.$subcategoria['id'],false);
        }
     }else{
        $session->msg("d", $errors);
        redirect('editarSubCategoria.php?id='.$subcategoria['id'],false);
     }
  }
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-5">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Editando <?php echo remove_junk(ucfirst($subcategoria['nombre']));?></span>
            </strong>
         </div>
         <div class="panel-body">
            <form method="post" action="editarSubCategoria.php?id=<?php echo (int)$subcategoria['id'];?>">
            <div class="form-group">
               <input type="text" class="form-control" name="subcategoria" value="<?php echo remove_junk(ucfirst($subcategoria['nombre']));?>">
            </div>
            <button type="submit" name="editar" class="btn btn-primary">Actualizar subcategoría</button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
