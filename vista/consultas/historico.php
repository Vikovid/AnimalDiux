<?php
  $page_title = 'Lista de sucursales';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $all_sucursal = find_all('sucursal');
  $categorias = find_all('categories');

  $producto = isset($_POST['producto']) ? $_POST['producto']:'';
  $idSucursal =  isset($_POST['sucursal']) ? $_POST['sucursal']:'';
  $idCategoria =  isset($_POST['categoria']) ? $_POST['categoria']:'';

  if ($idSucursal != ""){
     $sucursal = buscaRegistroPorCampo("sucursal","idSucursal",$idSucursal);
     $nomSucursal = $sucursal['nom_sucursal'];
  }

  $historico = histProductos($producto,$idSucursal,$idCategoria);
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<body onload="foco();">
  <form name="form1" method="post" action="historico.php">
     <br>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="pull-right">
               <div class="form-group">
                  <div class="col-md-4">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-barcode"></i>
                        </span>
                        <input type="text" class="form-control" name="producto" long="21" oninput="mayusculas(event)">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control" name="categoria">
                        <option value="">Selecciona una categoria</option>
                        <?php  foreach ($categorias as $id): ?>
                        <option value="<?php echo (int)$id['id'] ?>">
                        <?php echo $id['name'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>  
                  <div class="col-md-2">
                     <select class="form-control" name="sucursal">
                        <option value="">Selecciona una sucursal</option>
                        <?php  foreach ($all_sucursal as $id): ?>
                        <option value="<?php echo (int)$id['idSucursal'] ?>">
                        <?php echo $id['nom_sucursal'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>  
                  <a href="#" onclick="his();" class="btn btn-primary">Buscar</a>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
                  <?php
                     if ($idSucursal != ""){
                  ?>
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
                  <th class="text-center" style="width: 16%;"> Producto </th>
                  <th class="text-center" style="width: 16%;"> Categoría </th>
                  <th class="text-center" style="width: 5%;"> Cantidad Inicial</th>
                  <th class="text-center" style="width: 5%;"> Cantidad Final</th>
                  <th class="text-center" style="width: 10%;"> Sucursal </th>
                  <th class="text-center" style="width: 5%;"> Movimiento </th>
                  <th class="text-center" style="width: 5%;"> Comentario </th>
                  <th class="text-center" style="width: 10%;"> Usuario </th>
                  <th class="text-center" style="width: 10%;"> Vendedor </th>
                  <th class="text-center" style="width: 10%;"> Fecha </th>
                  <th class="text-center" style="width: 8%;"> Hora </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($historico as $historico):?>
               <?php 
                  $usuario = find_by_id('users',$historico['usuario']);
                  
                  if ($usuario != null)
                     $nombre = $usuario['username'];
                  else
                     $nombre = "";
               ?>     
               <tr>
                  <td><?php echo remove_junk($historico['name']); ?></td>
                  <td><?php echo remove_junk($historico['categoria']); ?></td>
                  <td class="text-right"> <?php echo remove_junk($historico['qtyin']); ?></td>
                  <td class="text-right"> <?php echo remove_junk($historico['qtyfinal']); ?></td>
                  <td><?php echo remove_junk($historico['nom_sucursal']); ?></td>
                  <td class="text-center"><?php echo remove_junk($historico['movimiento']); ?></td>
                  <td class="text-center"><?php echo remove_junk($historico['comentario']); ?></td>
                  <td><?php echo remove_junk($nombre); ?></td>
                  <td><?php echo remove_junk($historico['vendedor']); ?></td>
                  <td class="text-center"><?php echo date("d-m-Y", strtotime ($historico['fechaMov'])); ?></td>
                  <td class="text-center"><?php echo date("H:i:s", strtotime ($historico['horaMov'])); ?></td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>
