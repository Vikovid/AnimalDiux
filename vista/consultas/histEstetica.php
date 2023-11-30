<?php
  $page_title = 'Histórico de estética';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $all_sucursal = find_all('sucursal');
  $responsables = find_all('users');

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());

  $p_suc = "";
  $p_usu = "";

  if (isset($_POST['sucursal'])){  
     $p_suc =  remove_junk($db->escape($_POST['sucursal']));//prueba
  }

  if (isset($_POST['responsable'])){  
     $p_usu =  remove_junk($db->escape($_POST['responsable']));//prueba
  }

  if($p_suc != ""){
     if ($p_usu != "") {
        $historico = histEsteticaVendSuc($p_usu,$p_suc);
     }else{
        $historico = histEsteticaSuc($p_suc);
     }
     $consulta= buscaRegistroPorCampo('sucursal','idSucursal',$p_suc);
     $sucursal=$consulta['nom_sucursal'];
  }else{
     if ($p_usu != "")
        $historico = histEsteticaVend($p_usu);
     else
        $historico = histEstetica();
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<body onload="focoResponsable();">
  <form name="form1" method="post" action="histEstetica.php">
  <br>
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
                       <?php  foreach ($responsables as $id): ?>
                       <option value="<?php echo $id['username'] ?>">
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
                 <a href="#" onclick="histEstetica();" class="btn btn-primary">Buscar</a>
                 <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
                 <?php if ($p_suc != ""){ ?>
                 <div class="pull-right">
                    <strong>
                       <span class="glyphicon glyphicon-th"></span>
                       <span>Sucursal:</span>
                       <?php echo $sucursal; ?>
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
              <th class="text-center" style="width: 9%;">Responsable</th>
              <th class="text-center" style="width: 26%;">Producto</th>
              <th class="text-center" style="width: 7%;">Venta L-V</th>
              <th class="text-center" style="width: 7%;">Venta S-D</th>
              <th class="text-center" style="width: 7%;"> Comisión L-V </th>
              <th class="text-center" style="width: 7%;"> Comisión S-D </th>
              <th class="text-center" style="width: 8%;">Fecha</th>
              <th class="text-center" style="width: 7%;">Hora</th>
           </tr>
        </thead>
        <tbody>
           <?php foreach ($historico as $historico):?>
           <tr>
              <td><?php echo remove_junk($historico['vendedor']); ?></td>
              <td><?php echo remove_junk($historico['name']); ?></td>
              <td class="text-right"><?php echo money_format('%.2n',$historico['ventaLV']); ?></td>
              <td class="text-right"><?php echo money_format('%.2n',$historico['ventaSD']); ?></td>
              <td class="text-right"> <?php echo money_format('%.2n',$historico['comisionLV']); ?></td>
              <td class="text-right"> <?php echo money_format('%.2n',$historico['comisionSD']); ?></td>
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
