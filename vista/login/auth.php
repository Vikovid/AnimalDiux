<?php include_once('../../modelo/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);
$_SESSION['usuario']=$username;
ini_set('date.timezone','America/Mexico_City');
$fecha_actual=date('Y-m-d',time());
$msg = "";

if(empty($errors)){
   $user_id = authenticate($username, $password);
   if($user_id){
      //create session with id
      $session->login($user_id);
      //Update Sign in time
      updateLastLogIn($user_id);
      resetCaja($fecha_actual);
      //$msg = messages($fecha_actual);
      if ($msg != ""){
          $session->msg("s", $msg); 
      }else{
          $session->msg("s", "Bienvenido a Animal Diux"); 
      }

      //depurarBD();
      //redirect('index.php',false);
      echo '<script> window.location="home.php";</script>';
   }else{
      $session->msg("d", "Nombre de usuario y/o contraseña incorrecto.");
      //redirect('index.php',false);
      echo '<script> window.location="../../index.php";</script>';
   }
}else{
   $session->msg("d", $errors);
   // redirect('index.php',false);
   echo '<script> window.location="../../index.php";</script>';
}
?>
