<?php $user =    current_user();?>
<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="UTF-8">
      <title>
         <?php 
            if (!empty($page_title))
               echo remove_junk($page_title);
            elseif(!empty($user))
               echo ucfirst($user['name']);
            else 
               echo "Sistema simple de inventario";
         ?>
      </title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
      <link rel="stylesheet" href="../../libs/css/main.css" />
   </head>
   <body>
   <?php  if ($session->isUserLoggedIn(true)): 
      ini_set('date.timezone','America/Mexico_City');
      $time1=date('H:i',time());
      $time2=date('d-m-Y',time());
   ?> 
   <?php if ($cliente->isAccessTokenExpired()) unset($_SESSION['mailUsuario']);?>
      <header id="header">
         <div class="logo pull-left"> AnimalDiux - Inventario </div>
            <div class="header-content">
               <div class="header-date pull-left">
                  <strong><?php echo $time2."  ".$time1;?></strong>
               </div>
            <div class="pull-right clearfix">
               <ul class="info-menu list-inline list-unstyled">
                  <li class="profile">
                     <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                        <img src="../../libs/imagenes/<?php echo $cliente->isAccessTokenExpired() ? 'usuarios/'.$user['image'] : 'Google.png'; ?>" alt="user-image" class="img-circle img-inline">
                        <span><?php echo $cliente->isAccessTokenExpired() ? remove_junk(ucfirst($user['name'])):$_SESSION['mailUsuario']; ?> <i class="caret"></i></span>
                     </a>
                     <ul class="dropdown-menu">
                        <li>
                           <a href="../perfil/profile.php?id=<?php echo (int)$user['id'];?>">
                              <i class="glyphicon glyphicon-user"></i>
                              Perfil
                           </a>
                        </li>
                        <li>
                           <a href="../perfil/edit_account.php" title="edit account">
                              <i class="glyphicon glyphicon-cog"></i>
                              Configuración
                           </a>
                        </li>
                        <?php if ($user['user_level'] == 1):?>
                           <li><!--Iniciar/Cerrar sesión Con Google-->
                              <a href="<?php echo $cliente->isAccessTokenExpired() ? $cliente->createAuthUrl() : '../login/logoutGoogle.php?url='.current_url(); ?>">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                                    <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                                 </svg>
                                 <?php echo $cliente->isAccessTokenExpired() ? '&nbsp;Iniciar Con Google' : '&nbsp;Cerrar Con Google' ?>
                              </a>
                           </li>
                        <?php endif ?>
                        <li class="last">
                           <a href="../login/logout.php">
                              <i class="glyphicon glyphicon-off"></i>
                              Salir
                           </a>
                        </li>
                     </ul>
                  </li>
               </ul>
            </div>
         </div>
      </header>
      <div class="sidebar">
         <?php if($user['user_level'] === '1'): ?>    <!-- admin menu -->
            <?php include_once('admin_menu.php');?>
         <?php elseif($user['user_level'] === '2'): ?><!-- Special user -->
            <?php include_once('special_menu.php');?>
         <?php elseif($user['user_level'] === '3'): ?><!-- User menu -->
            <?php include_once('user_menu.php');?>
         <?php endif;?>
      </div>
   <?php endif;?>

   <div class="page">