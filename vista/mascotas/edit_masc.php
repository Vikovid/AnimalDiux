<?php
  $page_title = 'Editar mascota';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  $idmasc = buscaRegistroPorCampo('Mascotas','idMascotas',(int)$_GET['id']);
  $sexoAux = $idmasc['sexo'];
  $edoAux = $idmasc['estado'];
  $foto = $idmasc['foto'];
  $idMasc = $idmasc['idMascotas'];

  if(!$idmasc){
     $session->msg("d","Missing mascota id.");
     redirect('clinica.php');
  }

  if(isset($_POST['edit'])){
     $req_fields = array('nombre','especie','raza');
     validate_fields($req_fields);

     if(empty($errors)){

        $name = remove_junk($db->escape($_POST['nombre']));
        $especie = remove_junk($db->escape($_POST['especie']));
        $raza= remove_junk($db->escape($_POST['raza']));
        $color = remove_junk($db->escape($_POST['color']));
        $peso = remove_junk($db->escape($_POST['peso']));
        $alimento = remove_junk($db->escape($_POST['alimento']));
        $sexo = remove_junk($db->escape($_POST['sexo']));
        $estado = remove_junk($db->escape($_POST['estado']));
        $fecha_nac = remove_junk($db->escape($_POST['fecha_nac']));
        $foto = "";

        if(is_uploaded_file($_FILES['mascota']['tmp_name'])){
           $file_name = $_FILES['mascota']['name'];

           if ($file_name != '' || $file_name != null) {
              $file_type = $_FILES['mascota']['type'];
              list($type, $extension) = explode('/', $file_type);

              if ($extension == "gif" || $extension == "jpg" || 
                 $extension == "jpeg" || $extension == "png"){

                 $file_tmp_name = $_FILES['mascota']['tmp_name'];

                 $fp = fopen($file_tmp_name, 'r+b');
                 $data = fread($fp, filesize($file_tmp_name));
                 fclose($fp);            

                 $foto = $db->escape($data);

                 if (empty($file_name) || empty($file_tmp_name)){
                    $session->msg('d','La ubicación del archivo no se encuenta disponible.');
                    redirect('edit_masc.php?id='.$idmasc['idMascotas'], false);
                 }

                 if ($idmasc['foto'] != ''){
                    $borrado = $db->query("UPDATE Mascotas SET foto = '' WHERE idMascotas = $idMasc");
                    if (!$borrado){
                       $session->msg('d','Error al borrar el archivo original.');
                       redirect('edit_masc.php?id='.$idmasc['idMascotas'], false);
                    }
                 }
              }else{
                 $session->msg('d','Formato de archivo no válido.');
                 redirect('edit_masc.php?id='.$idmasc['idMascotas'], false);
              }
          }
          $resultado = actMascota($name,$especie,$raza,$color,$alimento,$sexo,$estado,$fecha_nac,$peso,$foto,$idmasc['idMascotas']);
       }else{
          $resultado = actMascota($name,$especie,$raza,$color,$alimento,$sexo,$estado,$fecha_nac,$peso,'',$idmasc['idMascotas']);
       } 

       if($resultado){
          $session->msg('s',"La mascota ha sido actualizada. ");
          redirect('clinica.php', false);
       }else{
          $session->msg('d','Lo siento. Falló la actualización.');
          redirect('edit_masc.php?id='.$idmasc['idMascotas'], false);
       }
  //aqui esta ok
    }else{
       $session->msg("d", $errors);
       redirect('edit_masc.php?id='.$idmasc['idMascotas'], false);
    }
 }
?>
<?php include_once('../layouts/header.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Lista de productos</title>
</head>

<script language="Javascript">

function valorAnt(){
   document.form1.sexo.value=document.form1.sexoAux.value;
   document.form1.estado.value=document.form1.edoAux.value;
}

</script>

<body onload="valorAnt();">
<div class="row">
  <div class="col-md-10">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-8">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Editar Mascota</span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-7">
            <form method="post" name="form1" action="edit_masc.php?id=<?php echo (int)$idmasc['idMascotas'] ?>" enctype="multipart/form-data">

               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" value="<?php echo remove_junk($idmasc['nombre']);?>" placeholder="Edita el nombre" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="especie" value="<?php echo remove_junk($idmasc['especie']);?>" placeholder="Edita la especie" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="raza" value="<?php echo remove_junk($idmasc['raza']);?>" placeholder="Edita la raza" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="color" value="<?php echo remove_junk($idmasc['Color']);?>" placeholder="Edita el color">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="peso" value="<?php echo remove_junk($idmasc['peso']);?>" placeholder="Edita el peso">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="alimento" value="<?php echo remove_junk($idmasc['alimento']);?>" placeholder="Edita el alimento">
                  </div>
               </div>
               <div class="form-group">
                  <select id="sexo" class="form-control" name="sexo">
                     <optgroup label="Edita el Sexo">
                         <option value="Hembra">Hembra</option>
                         <option value="Macho">Macho</option>
                     </optgroup>
                  </select>
               </div>  
               <div class="form-group">
                  <select id="Estado" class="form-control" name="estado">
                     <optgroup label="Edita el Estado">
                        <option value="Fertil">Fertil</option>
                        <option value="Esterlizado">Esterlizado</option>
                     </optgroup>
                  </select>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="date" class="form-control" name="fecha_nac" value="<?php echo remove_junk($idmasc['fecha_nacimiento']);?>">
                  </div>
               </div>
               <div class="panel-heading">
                  <div class="panel-heading clearfix">
                     <span class="glyphicon glyphicon-camera"></span>
                     <span>Cambiar foto de mascota</span>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="panel profile">
                        <?php if ($foto != ""){ 
                           echo "<img src='data:image/jpg; base64,".base64_encode($foto)."' width='150' height='200'>";
                        } ?>
                     </div>
                  </div>
                  <div class="col-md-8">
                     <div class="form-group">
                        <input type="file" name="mascota" multiple="multiple" class="btn btn-primary btn-file"/>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <input type="hidden" name="sexoAux" value="<?php echo $sexoAux ?>">
         <input type="hidden" name="edoAux" value="<?php echo $edoAux ?>">
         <button type="submit" name="edit" class="btn btn-danger">Actualizar</button>
         </form>
      </div>
   </div>
</div>
</body>  
</html>  
<?php include_once('../layouts/footer.php'); ?>
