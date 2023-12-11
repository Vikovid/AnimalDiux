<?php
   require_once('../../modelo/load.php');
   $page_title = 'Estudio';
   // Checkin What level user has permission to view this page
   page_require_level(3);
  
   $idmas= isset($_GET['idMas']) ? $_GET['idMas']:'';

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   $dir =   '../../libs/uploads/estudios/';
   $dirBD = 'libs/uploads/estudios/';

   if (isset($_POST['enviar'])) {
      $idMasc  = remove_junk($db->escape($_POST['id']));
      
      if(is_uploaded_file($_FILES['archivo']['tmp_name'])) {
         $req_fields = array('nombre','descripcion');
         validate_fields($req_fields);

         if (empty($errors)) {
            $descripcion = remove_junk($db->escape($_POST['descripcion']));
            $nombre =      remove_junk($db->escape($_POST['nombre']));
            $idMasc =      remove_junk($db->escape($_POST['id']));

            $file_name =   $_FILES['archivo']['name'];

            $new_name_file = null;

            if ($file_name != '' || $file_name != null) {
               $file_type = $_FILES['archivo']['type'];
               list($type, $extension) = explode('/', $file_type);

               if ($extension == "pdf" || $extension == "gif" || $extension == "jpg" || 
                  $extension == "jpeg" || $extension == "png"){

                  if (!file_exists($dir))
                     mkdir($dir, 0777, true);
        
                  $file_tmp_name = $_FILES['archivo']['tmp_name'];
                  //$new_name_file = 'files/' . date('Ymdhis') . '.' . $extension;
                  $new_name_file = $dir . file_name($file_name) . '.' . $extension;
                  $archivoBD = $dirBD . file_name($file_name) . '.' . $extension;

                  if (empty($file_name) || empty($file_tmp_name)){
                     $session->msg('d','La ubicación del archivo no se encuenta disponible.');
                     redirect('estudio.php?idMas='.$idMasc, false);
                  }

                  if (!is_writable($dir)){
                     $session->msg('d',$dir.' Debe tener permisos de escritura!!!.');
                     redirect('estudio.php?idMas='.$idMasc, false);
                  }

                  if (file_exists($new_name_file)){
                     $session->msg('d','El archivo '.$file_name.' ya existe.');
                     redirect('estudio.php?idMas='.$idMasc, false);
                  }

                  if (copy($file_tmp_name, $new_name_file)) {
                     $resultado = altaEstudio($nombre,$descripcion,$archivoBD,$idMasc,$fecha_actual);

                     if($resultado){
                        $session->msg('s', "Registro Exitoso.");
                        redirect('history.php?idMascotas='.$idMasc, false);
                     }else{
                        $session->msg('d',' Lo siento, registro falló.');
                        redirect('estudio.php?idMas='.$idMasc, false);
                     }
                  }
               }else{
                  $session->msg('d','Formato de archivo no válido.');
                  redirect('estudio.php?idMas='.$idMasc, false);
               }
            }
         } else {
            $session->msg("d", $errors);
            redirect('estudio.php?idMas='.$idMasc,false);
         }
      }else{
         $session->msg('d','Debe seleccionar un archivo.');
         redirect('estudio.php?idMas='.$idMasc, false);
      }
   }

   function file_name($string) {

      // Tranformamos todo a minusculas

      $string = strtolower($string);

      //Rememplazamos caracteres especiales latinos

      $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');

      $repl = array('a', 'e', 'i', 'o', 'u', 'n');

      $string = str_replace($find, $repl, $string);

      // Añadimos los guiones

      $find = array(' ', '&', '\r\n', '\n', '+');
      $string = str_replace($find, '-', $string);

      // Eliminamos y Reemplazamos otros carácteres especiales

      $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');

      $repl = array('', '-', '');

      $string = preg_replace($find, $repl, $string);

      return $string;
   }
   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);
   $nombre = $mascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" action="estudio.php" method="post" enctype="multipart/form-data">  
   <div class="row col-md-8">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-6">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Estudio de:</span>
               <span><?php echo $nombre ?></span>
            </strong>     
         </div>
         <div class="panel-body">
            <div class="form-group col-md-12">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="nombre" placeholder="Nombre">
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="input-group">
                  <span class="input-group-addon">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="descripcion" placeholder="Descripción">
               </div>
            </div>
            <div class="form-group col-md-12">
               <div class="input-group">
                  <span class="input-group-btn">
                     <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <label for="archivo">Seleccione el archivo:</label>
                  <input name="archivo" type="file" multiple="multiple" class="btn btn-primary btn-file">
               </div>
            </div>
            <div class="col-md-12">   
               <input type="hidden" value="<?php echo $idmas ?>" name="id">
               <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
               <input type="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
               <button type="submit" name="enviar" class="btn btn-danger">Subir archivo</button>
            </div>
         </div>
      </div>
   </div>
</form>
<?php include_once('../layouts/footer.php'); ?>