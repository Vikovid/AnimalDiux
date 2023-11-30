<?php
   require_once('../../modelo/load.php');
   $page_title = 'Lista de categorías';
   page_require_level(1);

   $nombre = isset($_POST['categoria']) ? $_POST['categoria']:'';

   if (isset($_POST['agregar'])) {
      $req_fields = array('categoria');
      validate_fields($req_fields);

      if (empty($errors)) {
         $resultado = altaCategoria($nombre);
         if ($resultado)
            $session->msg('s',"Categoría agregada exitosamente.");
         else
            $session->msg('d','Lo siento, falló el registro.');
      } else
         $session->msg("d", $errors);

      redirect('categorias.php', false);
   }

   $categorias = isset($_POST['categoria']) ? categoria($_POST['categoria']) : find_all("categories");
?>
<?php include_once('../layouts/header.php'); ?>
<script language="Javascript">
   function foco() {
      document.form1.categoria.focus();
   }
   function categorias(){
      document.form1.action = "categorias.php";
      document.form1.submit();
   }
   function subcategorias() {
      var existe = 0;
      var str =    "";
      for (i=0;i<form1.elements.length;i++) {
         if (form1.elements[i].name == "selCategoria") {
            existe = 1;
            if (form1.elements[i].checked) {
               str = form1.elements[i].value;           
               break;
            }
         }
      }
      if (str == "") {
         alert ("Debe seleccionar una categoría para agregar subcategorías.");
         return -1;
      }
      if (existe == 1 && str != "") {
         document.form1.idCategoria.value = str;
         document.form1.action = "subcategorias.php";
         document.form1.submit();
      }
   }
   function eliminar($categoria) {
      if (confirm("¿Está segur@ de borrar la categoría y todas sus subcategorías?") == true) {
         document.form1.idCategoria.value = $categoria;
         document.form1.action = "delete_categorie.php";
         document.form1.submit();
      } else
         return -1; 
   }
</script>
<!DOCTYPE html>
<html>
<head>
   <title>Lista de categorías</title>
</head>
<body onload="foco();">
   <form name="form1" method="post" action="categorias.php">
      <br>
      <div class="row">
         <div class="col-md-9">
            <?php echo display_msg($msg); ?>
         </div>
         <div class="col-md-9">
            <div class="panel panel-default">
               <div class="panel-heading clearfix">
                  <div class="pull-right">
                     <div class="form-group">
                        <div class="col-md-6">
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <i class="glyphicon glyphicon-barcode"></i>
                              </span>
                              <input type="text" class="form-control" name="categoria" long="50" placeholder="Categoría">
                           </div>
                        </div>  
                        <a href="#" onclick="categorias();" class="btn btn-primary">Buscar</a> 
                        <button type="submit" name="agregar" class="btn btn-primary">Agregar categoría</button>
                        <div class="pull-right">
                           <a href="#" onclick="subcategorias();" class="btn btn-success">Agregar subcategoría</a>
                        </div>
                     </div>   
                  </div>   
               </div>
               <div class="panel-body">
                  <table class="table table-bordered">
                     <thead>
                        <tr>
                           <th class="text-center" style="width: 3%; ">#</th>
                           <th class="text-center" style="width: 3%; ">Sel</th>
                           <th class="text-center" style="width: 72%;">Nombre</th>
                           <th class="text-center" style="width: 5%; ">Acciones</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($categorias as $categoria):?>
                           <tr>
                              <td class="text-center"><?php echo count_id();?></td>
                              <td width="3%">
                                 <input type='radio' name='selCategoria' value='<?php echo $categoria['id'] ?>'/>
                              </td>
                              <td> <?php echo utf8_encode($categoria['name']); ?></td>
                              <td class="text-center">
                                 <div class="btn-group">
                                    <a href="edit_categorie.php?id=<?php echo (int)$categoria['id'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                                       <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a href="#" onclick="eliminar(<?php echo (int)$categoria['id'];?>);" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip"> 
                                       <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" class="form-control" name="idCategoria" value="">
   </form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>