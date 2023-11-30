<?php
   $page_title = 'Lista de mascotas';
   require_once('../../modelo/load.php');
   page_require_level(2);

   $mascota = isset($_POST['idMascota']) ? $_POST['idMascota']:'';  
   $cont = 0;

   if ($mascota != "")
      $mascotas = is_numeric($mascota) ? buscaMascotaCliente($mascota):buscaMascotaNombre($mascota);
   else
      $mascotas = mascotas();
?>

<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
   <title>Lista de mascotas</title>
</head>
<body onload="valorMascota();">
   <form name="form1" method="post" action="clinica.php">
      <div class="row">
         <div class="col-md-12">
            <?php echo display_msg($msg); ?>
         </div>
         <div class="col-md-12">
            <div class="panel panel-default">
               <div class="panel-heading clearfix">
                  <div class="form-group">
                     <table width="100%" align="center">
                        <tr>  
                           <td width="30%">
                              <input name="idMascota" type="text" class="form-control" size="51" maxlength="50">
                           </td>
                           <td width="5%">&nbsp;</td>
                           <td width="20%">
                              <input type="submit" id="boton" class="btn btn-primary" name="Submit" value="Buscar" />
                           </td>
                           <td width="20%">
                              <input type="button" id="boton" class="btn btn-primary" name="Agregar" value="Abrir" onClick="datosMascota();" />
                           </td>
                        </tr>
                     </table>
                  </div>   
               </div>
               <div class="panel-body">
                  <table class="table table-bordered">
                     <thead>
                        <tr>
                           <td width="2%"><b>Sel</td>
                           <td width="15%"><b>Nombre de la Mascota</td>
                           <td width="48%"><b>Nombre del dueño</td> 
                           <td class="text-center" width="10%"><b>Acciones</td> 
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($mascotas as $mascota):?>
                           <tr>
                              <?php if ($cont == 0){ ?>
                                 <td width="3%"><input type='radio' name='empresa' value='<?php echo $mascota["idMascotas"] ?>' onClick='valor();' checked/></td>
                              <?php }else{ ?>
                                 <td width="3%"><input type='radio' name='empresa' value='<?php echo $mascota["idMascotas"] ?>' onClick='valor();'/></td>
                              <?php } ?>
                              <td width="15%"><?php echo $mascota['nombre'] ?></td>
                              <td width="58%"><?php echo utf8_encode($mascota['nom_cliente']) ?></td>
                              <td class="text-center">
                                 <div class="btn-group">
                                    <a href="../pdf/tarjetapdf.php?id=<?php echo (int)$mascota['idMascotas']; ?>" class="btn btn-danger btn-xs ">
                                       PDF
                                    </a>
                                    <a href="edit_masc.php?id=<?php echo (int)$mascota['idMascotas']; ?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                                       <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a href="delete_mascota.php?id=<?php echo (int)$mascota['idMascotas']; ?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                                       <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                 </div>
                              </td>
                           </tr>
                        <?php $cont++; endforeach;?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="idMascotas" value="">
   </form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
