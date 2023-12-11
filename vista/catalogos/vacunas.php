<?php
   require_once('../../modelo/load.php');
   $page_title = 'Lista de vacunas';
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $nombre = isset($_POST['vacuna']) ? $_POST['vacuna']:'';

   if(isset($_POST['agregar'])){
      $req_fields = array('vacuna');
      validate_fields($req_fields);
   
      if(empty($errors)){
         //$v_nombre  = remove_junk($db->escape($_POST['nombre']));

         $resultado = altaVacunas($nombre);

         if($resultado){
            $session->msg('s',"Vacuna agregada exitosamente.");
            redirect('vacunas.php', false);
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('vacunas.php', false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('vacunas.php',false);
      }
   }
 
   if ($nombre!="")
      $vacunas = vacunas($nombre);
   else
      $vacunas = find_all("vacunas");
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">
   function foco(){
      document.form1.vacuna.focus();
   }
   function vacunas(){
      document.form1.action = "vacunas.php";
      document.form1.submit();
   }
</script>   

<!DOCTYPE html>
<html>
<head>
<title>Lista de vacunas</title>
</head>

<body onload="foco();">
   <form name="form1" method="post" action="vacunas.php">
      <div class="row col-md-9">
         <?php echo display_msg($msg); ?>
      </div>
      <div class="row col-md-9">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div class="pull-right">
                  <div class="form-group">
                     <div class="col-md-6">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <i class="glyphicon glyphicon-barcode"></i>
                           </span>
                           <input type="text" class="form-control" name="vacuna" long="50" placeholder="Vacuna">
                        </div>
                     </div>  
                     <a href="#" onclick="vacunas();" class="btn btn-primary">Buscar</a> 
                     <button type="submit" name="agregar" class="btn btn-primary">Agregar vacuna</button>
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
                     <?php foreach ($vacunas as $vacunas):?>
                     <tr>
                        <td class="text-center"><?php echo count_id();?></td>
                        <td> <?php echo remove_junk($vacunas['nombre']); ?></td>
                        <td class="text-center">
                           <div class="btn-group">
                              <a href="edit_vacunas.php?id=<?php echo (int)$vacunas['id'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-edit"></span>
                              </a>
                              <a href="delete_vacunas.php?id=<?php echo (int)$vacunas['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
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