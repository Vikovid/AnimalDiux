<?php
  $page_title = 'Lista de consultas';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $all_sucursal = find_all('sucursal');
  $encargados = find_all('users');

  $idSucursal = "";
  $responsable = "";

  if (isset($_POST['sucursal'])){  
     $idSucursal = remove_junk($db->escape($_POST['sucursal']));
  }

  if (isset($_POST['responsable'])){  
     $responsable = remove_junk($db->escape($_POST['responsable']));
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script language="Javascript">

function histConsulta(){
   document.form1.action = "histConsulta.php";
   document.form1.submit();
}

</script>


<body onload="responsable();">
  <form name="form1" method="post" action="histConsulta.php">
<?php
   if ($idSucursal != ""){
      $sucursal = buscaRegistroPorCampo("sucursal","idSucursal",$idSucursal);
      $nomSucursal = $sucursal['nom_sucursal'];
   }
?>
<br>
<?php
   if($idSucursal != ""){
     if ($responsable != "") {
        $historico = histConsUsuSuc($responsable,$idSucursal);
     }else{
        $historico = histConsSuc($idSucursal);
     }
   }else{
     if ($responsable != "")
        $historico = histConsUsu($responsable);
     else
        $historico = histConsulta();
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
                     <option value="">Selecciona responsable</option>
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
               <a href="#" onclick="histConsulta();" class="btn btn-primary">Buscar</a>
               <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
               <?php if ($idSucursal != ""){ ?>
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
               <th class="text-center" style="width: 11%;"> Responsable </th>
               <th class="text-center" style="width: 10%;"> Cliente</th>
               <th class="text-center" style="width: 10%;"> Mascota</th>
               <th class="text-center" style="width: 8%;"> Sucursal</th>
               <th class="text-center" style="width: 8%;"> Movimiento </th>
               <th class="text-center" style="width: 7%;"> Fecha </th>
               <th class="text-center" style="width: 7%;"> Hora </th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($historico as $historico):?>
               <tr>
                  <td><?php echo remove_junk($historico['username']); ?></td>
                  <td><?php echo remove_junk($historico['nom_cliente']); ?></td>
                  <td><?php echo remove_junk($historico['nombre']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($historico['nom_sucursal']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($historico['movimiento']); ?></td>
                  <td class="text-center"><?php echo date("d-m-Y", strtotime ($historico['fecha'])); ?></td>
                  <td class="text-center"><?php echo date("H:i:s", strtotime ($historico['hora'])); ?></td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
   </div>
</div>
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>
