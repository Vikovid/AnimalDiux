<?php
   ob_start();
   require_once('modelo/load.php');
   if($session->isUserLoggedIn(true)) { redirect('vista/login/home.php', false);}
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
<link rel="stylesheet" href="libs/css/main.css" />

<div class="login-page">   
   <div class="text-center">
      <h2>Iniciar sesión </h2>
   </div>
   <?php echo display_msg($msg); ?>
   <form method="post" action="vista/login/auth.php" class="clearfix">
      <div class="form-group">
         <label for="username" class="control-label">Usuario</label>
         <input type="name" class="form-control" name="username" placeholder="Usuario">
      </div>
      <div class="form-group">
         <label for="Password" class="control-label">Contraseña</label>
         <input type="password" name= "password" class="form-control" placeholder="Contraseña">
      </div>
      <div class="form-group">
         <button type="submit" class="btn btn-info  pull-right">Entrar</button>
      </div>
   </form>
   <div class="form-group">
      <a href="libs/documentos/politicaPrivacidad.php" target="_blank"> Política de Privacidad </a>
   </div>
</div>