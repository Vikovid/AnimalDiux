<?php
  $page_title = 'Editar cliente';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  $idcreden= $_GET['IdCredencial'];

  if(isset($_GET['IdCredencial']))
     $clientz = buscaRegistroPorCampo("cliente","idcredencial",$idcreden);

  if(isset($_POST['nombre'])) {
     $idcreden= $_GET['IdCredencial'];
     $name = $_POST['nombre'];
     $direc = $_POST['direccion'];
     $telcliente= $_POST['telefono'];
     $email = $_POST['email'];
     $idcre = $_POST['credencial'];

     $resultado = actCliente($name,$direc,$telcliente,$email,$idcreden);

     if($resultado){
        $session->msg('s',"Cliente ha sido actualizado.");
        redirect('cliente.php?IdCredencial='.(int)$idcreden, false);
     }else{
        $session->msg('d','Lo siento no se actualizaron los datos.');
        redirect('edit_client.php?IdCredencial='.(int)$idcreden, false);
     }
  }
?>
<?php include_once('../layouts/header.php'); ?>

<div class="row">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Editar cliente</span>
         </strong>
      </div>
      <div class="panel-body">
         <div class="col-md-7">
            <form method="post" name="form1" action="edit_client.php?IdCredencial=<?php echo $_GET['IdCredencial'];?>">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="nombre" value="<?php echo remove_junk($clientz['nom_cliente']); ?>" placeholder="Edita el nombre" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-signpost-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 1.414V4h2V1.414a1 1 0 0 0-2 0zM1 5a1 1 0 0 1 1-1h10.532a1 1 0 0 1 .768.36l1.933 2.32a.5.5 0 0 1 0 .64L13.3 9.64a1 1 0 0 1-.768.36H2a1 1 0 0 1-1-1V5zm6 5h2v6H7v-6z"/>
                        </svg></i>
                     </span>
                     <input type="text" class="form-control" name="direccion" value="<?php echo remove_junk($clientz['dir_cliente']); ?>" placeholder="Edita la dirección" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-telephone-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M2.267.98a1.636 1.636 0 0 1 2.448.152l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z"/>
                          </svg></i>
                     </span>
                     <input type="number" class="form-control" name="telefono" pattern="[0-9]+" minlength="8" maxlength="10" required value="<?php echo $clientz['tel_cliente']; ?>" placeholder="Edita el número">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg></i>
                     </span>
                     <input type="text" class="form-control" name="email" value="<?php echo $clientz['correo']; ?>" placeholder="Edita el correo">
                  </div>
               </div>
               <div class="text-center">
                  <button type="submit" name="update" class="btn btn-danger">Actualizar</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
