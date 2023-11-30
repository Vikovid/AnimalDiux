<?php
  $page_title = 'Lista de cancelaciones';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $all_sucursal = find_all('sucursal');
  $p_scu = "";

  if(isset($_POST['sucursal'])){  
     $p_scu =  remove_junk($db->escape($_POST['sucursal']));//prueba
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<!DOCTYPE html>
<html>
<head>
<title>Lista de productos</title>
</head>
<body onload="sucursal();">
  <form name="form1" method="post" action="cancelaciones.php">
  	
<?php
   if($p_scu!=""){
    $cancelacion = cancelacionesXSuc($p_scu);
   }else{
    $cancelacion = cancelaciones();
   }
?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-5">
                  <select class="form-control" name="sucursal">
                     <option value="">Selecciona una sucursal</option>
                     <?php  foreach ($all_sucursal as $id): ?>
                     <option value="<?php echo (int)$id['idSucursal'] ?>">
                     <?php echo $id['nom_sucursal'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <a href="#" onclick="cancelacion();" class="btn btn-primary">Buscar</a>             
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th class="text-center" style="width: 3%;">#</th>
                  <th class="text-center" style="width: 47%;"> Producto </th>
                  <th class="text-center" style="width: 10%;"> Sucursal </th>
                  <th class="text-center" style="width: 15%;"> Usuario </th>
                  <th class="text-center" style="width: 8%;"> fecha </th>
                  <th class="text-center" style="width: 18%;"> Razón de la cancelación </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($cancelacion as $cancelacion):?>
               <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td> <?php echo remove_junk($cancelacion['name']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($cancelacion['nom_sucursal']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($cancelacion['usuario']); ?></td>
                  <td class="text-center"> <?php echo date("d-m-Y", strtotime ($cancelacion['date'])); ?></td>
                  <td> <?php echo remove_junk($cancelacion['mensaje']); ?></td>
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
