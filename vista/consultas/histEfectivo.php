<?php
  $page_title = 'Lista de sucursales';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $all_sucursal = find_all('sucursal');
  $encargados = find_all('users');

  $p_suc = "";
  $p_usu = "";

  if (isset($_POST['sucursal'])){  
     $p_suc =  remove_junk($db->escape($_POST['sucursal']));//prueba
  }

  if (isset($_POST['responsable'])){  
     $p_usu =  remove_junk($db->escape($_POST['responsable']));//prueba
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<body onload="responsable();">
  <form name="form1" method="post" action="histEfectivo.php">
<?php
   if ($p_suc != ""){
      $sucursal = buscaRegistroPorCampo("sucursal","idSucursal",$p_suc);
      $nomSucursal = $sucursal['nom_sucursal'];
   }
?>
<br>
<?php
   if($p_suc != ""){
     if ($p_usu != "") {
        $historico = histEfecUsuSuc($p_usu,$p_suc);
     }else{
        $historico = histEfecSuc($p_suc);
     }
   }else{
     if ($p_usu != "")
        $historico = histEfecUsu($p_usu);
     else
        $historico = histEfectivo();
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
               <div class="col-md-3">
                  <select class="form-control" name="responsable">
                     <option value="">Selecciona usuario</option>
                     <?php  foreach ($encargados as $id): ?>
                     <option value="<?php echo $id['id'] ?>">
                     <?php echo $id['name'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-3">
                  <select class="form-control" name="sucursal">
                     <option value="">Selecciona una sucursal</option>
                     <?php  foreach ($all_sucursal as $id): ?>
                     <option value="<?php echo (int)$id['idSucursal'] ?>">
                     <?php echo $id['nom_sucursal'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <a href="#" onclick="histEfectivo();" class="btn btn-primary">Buscar</a>
               <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
               <?php if ($p_suc != ""){ ?>
                  <div class="pull-right">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Sucursal:</span>
                        <?php echo $nomSucursal; ?>
                     </strong>
                  </div>
               <?php } ?>
            </div>   
         </div>   
      </div>
   </div>
   <div class="panel-body">
      <table class="table table-bordered">
         <thead>
            <tr>
               <th class="text-center" style="width: 11%;"> Movimiento </th>
               <th class="text-center" style="width: 10%;"> Cantidad Inicial</th>
               <th class="text-center" style="width: 10%;"> Cantidad Final</th>
               <th class="text-center" style="width: 8%;"> Cantidad Movimiento</th>
               <th class="text-center" style="width: 8%;"> Sucursal </th>
               <th class="text-center" style="width: 11%;"> Usuario </th>
               <th class="text-center" style="width: 11%;"> Vendedor </th>
               <th class="text-center" style="width: 7%;"> Fecha </th>
               <th class="text-center" style="width: 7%;"> Hora </th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($historico as $historico):?>
               <tr>
                  <td><?php echo remove_junk($historico['movimiento']); ?></td>
                  <td class="text-right"> <?php echo remove_junk($historico['cantIni']); ?></td>
                  <td class="text-right"> <?php echo remove_junk($historico['cantFinal']); ?></td>
                  <?php $cantMov = abs($historico['cantFinal'] - $historico['cantIni']); ?>
                  <td class="text-right"> <?php echo money_format('%.2n',$cantMov); ?></td>
                  <td class="text-center"> <?php echo remove_junk($historico['nom_sucursal']); ?></td>
                  <td><?php echo remove_junk($historico['username']); ?></td>
                  <td><?php echo remove_junk($historico['vendedor']); ?></td>
                  <td class="text-center"><?php echo date("d-m-Y", strtotime ($historico['fechaMov'])); ?></td>
                  <td class="text-center"><?php echo date("H:i:s", strtotime ($historico['horaMov'])); ?></td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
   </div>
</div>
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>
