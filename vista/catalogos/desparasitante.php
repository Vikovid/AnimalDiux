<?php
   require_once('../../modelo/load.php');
   $page_title = 'Lista de desparasitantes';
   // Checkin What level user has permission to view this page
   page_require_level(3);
  
   $nombre = isset($_POST['desparasitante']) ? $_POST['desparasitante']:'';

   if(isset($_POST['agregar'])){
      $req_fields = array('desparasitante');
      validate_fields($req_fields);
      if(empty($errors)){
         $resultado = altaDesparasitante($nombre);

         if($resultado){
            $session->msg('s',"Desparasitante agregado exitosamente.");
            redirect('desparasitante.php', false);
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('desparasitante.php', false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('desparasitante.php',false);
      }
   }
 
   if ($nombre!="")
      $desparasitante = desparasitantes($nombre);
   else
      $desparasitante = find_all("desparasitantes");
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">
   function foco(){
      document.form1.desparasitante.focus();
   }
   function desparasitantes(){
      document.form1.action = "desparasitante.php";
      document.form1.submit();
   }
</script>   

<!DOCTYPE html>
<html>
<head>
   <title>Lista de Desparasitantes</title>
</head>

<body onload="foco();">
   <form name="form1" method="post" action="desparasitante.php">
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
                              <input type="text" class="form-control" name="desparasitante" long="50" placeholder="Desparasitante">
                           </div>
                        </div>  
                        <a href="#" onclick="desparasitantes();" class="btn btn-primary">Buscar</a> 
                        <button type="submit" name="agregar" class="btn btn-primary">Agregar desparasitante</button>
                     </div>   
                  </div>   
               </div>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 3%;">#</th>
                        <th class="text-center" style="width: 72%;">Nombre</th>
                        <th class="text-center" style="width: 5%;">Acciones</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($desparasitante as $desparasitante):?>
                     <tr>
                        <td class="text-center"><?php echo count_id();?></td>
                        <td> <?php echo utf8_decode($desparasitante['nombre']); ?></td>
                        <td class="text-center">
                           <div class="btn-group">
                              <a href="edit_desparasitante.php?id=<?php echo (int)$desparasitante['id'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-edit"></span>
                              </a>
                              <a href="delete_desparasitante.php?id=<?php echo (int)$desparasitante['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
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
   </form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>