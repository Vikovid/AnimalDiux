<?php
  $page_title = 'Agregar Mascotas';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  if(isset($_POST['add_mascotas'])){
     $req_fields = array('nom_mascota','idcliente');
     validate_fields($req_fields);
     if(empty($errors)){
        $nombre  = remove_junk($db->escape($_POST['nom_mascota']));
        $especie  = remove_junk($db->escape($_POST['especie']));
        $raza = remove_junk($db->escape($_POST['raza']));
        $color = remove_junk($db->escape($_POST['color']));
        $peso = remove_junk($db->escape($_POST['peso']));
        $alimento = remove_junk($db->escape($_POST['alimento']));
        $sexo = remove_junk($db->escape($_POST['sexo']));
        $estado = remove_junk($db->escape($_POST['Estado']));
        $fecha_nacimiento = remove_junk($db->escape($_POST['fecha_nacimiento']));
        $idcliente = remove_junk($db->escape($_POST['idcliente']));
        $foto = "";
        $nom_cliente = "";

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
                    redirect('add_mascotas.php', false);
                 }
              }else{
                 $session->msg('d','Formato de archivo no válido.');
                 redirect('add_mascotas.php', false);
              }
           }
        } 

        $consCliente = buscaRegistroPorCampo('cliente','idcredencial',$idcliente);
        $nomCliente = $consCliente['nom_cliente'];

        if ($nomCliente != ""){
           $resultado = altaMascota($nombre,$especie,$raza,$color,$alimento,$sexo,$estado,$fecha_nacimiento,$idcliente,$foto,$peso);

           if($resultado){
              $session->msg('s',"Mascota agregada exitosamente.");
              redirect('add_mascotas.php', false);
           }else{
              $session->msg('d','Lo siento, falló el registro.');
              redirect('add_mascotas.php', false);
           }
        }else{
           $session->msg('d','El id proporcionado no existe.');
           redirect('add_mascotas.php', false);
        }
     }else{
        $session->msg("d", $errors);
        redirect('add_mascotas.php',false);
     }
  }
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
  <div class="col-md-17">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar Mascota</span>
            </strong>
            <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form name="form" method="post" action="add_mascotas.php" enctype="multipart/form-data">
               <table style="width:100%">
                  <tr>
                     <td>
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M4 1h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H4z"/>
                              <path d="M13.784 14c-.497-1.27-1.988-3-5.784-3s-5.287 1.73-5.784 3h11.568z"/>
                              <path fill-rule="evenodd" d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                              </svg>
                              </span>
                              <input type="text" class="form-control" name="nom_mascota" placeholder="Nombre de la Mascota">
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                              </svg>
                              </span>
                              <input type="text" class="form-control" name="especie" placeholder="Especie">
                           </div>
                        </div>
                     </td>
                     <td>   
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-award-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M8 0l1.669.864 1.858.282.842 1.68 1.337 1.32L13.4 6l.306 1.854-1.337 1.32-.842 1.68-1.858.282L8 12l-1.669-.864-1.858-.282-.842-1.68-1.337-1.32L2.6 6l-.306-1.854 1.337-1.32.842-1.68L6.331.864 8 0z"/>
                              <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1 4 11.794z"/>
                              </svg>
                              </span>
                              <input type="text" class="form-control" name="raza" placeholder="Raza" >
                           </div>
                        </div>
                     </td>  
                  </tr>   
                  <tr>       
                     <td>
                        <br>	
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                              </svg>
                              </span>
                              <input type="text" class="form-control" name="color" placeholder="Color">
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                              </svg>
                              </span>
                              <input type="number" step="0.01" class="form-control" name="peso" placeholder="peso">
                           </div>
                        </div>
                     </td>
                     <td> 
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                              </svg>
                              </span>
                              <input type="text" class="form-control" name="alimento" placeholder="Alimento">
                           </div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <div class="col-md-12">
                           <select id="sexo" class="form-control" name="sexo">
                              <optgroup label="Sexo">
                                 <option value="Hembra">Hembra</option>
                                 <option value="Macho">Macho</option>
                              </optgroup>
                           </select>
                        </div>  
                     </td> 
                     <td>
                        <div class="col-md-12">
                           <select id="Estado" class="form-control" name="Estado">
                              <optgroup label="Estado">
                                 <option value="Fertil">Fertil</option>
                                 <option value="Esterlizado">Esterlizado</option>
                              </optgroup>
                           </select>
                        </div>  
                     </td>
                     <td> 
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                              </svg>
                              </span>
                              Fecha de nacimiento
                              <input type="date" class="form-control" name="fecha_nacimiento" 
                  placeholder="fecha nacimiento">
                           </div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td> 
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                              </svg>
                              </span>
                              <input type="number" class="form-control" name="idcliente" placeholder="IdCliente">
                           </div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <div class="form-group">
                           <div class="input-group">
                              <span class="input-group-btn">
                              <i class="glyphicon glyphicon-th-large"></i>
                              </span>
                              <label for="archivo">Seleccione el archivo:</label>
                              <input name="mascota" type="file" multiple="multiple" class="btn btn-primary btn-file">
                           </div>
                        </div>    
                     </td>
                  </tr>        
               </table>
               <div class="col text-center">
                  <button type="submit" name="add_mascotas" class="btn btn-danger">Agregar Mascota</button>
               </div>
            </form>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
