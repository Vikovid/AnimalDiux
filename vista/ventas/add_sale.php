<?php
  $page_title = 'Lista de sucursales';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $user = current_user(); 
  $usuario = $user['name'];
  $usu = $user['id'];
  $sucursal = $user['idSucursal'];
  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());

  $codigo="";
  $aux="|";  
  $totalcondescuento = 0;
  $totaldes = 0;
  $SubTotal = 0;
  $cont = 0;

  $codigo= isset($_POST['codigo']) ? $_POST['codigo']:'';  

  if($codigo!=""){
     $productos = buscaProductosCod($codigo,$usu,$sucursal);
  }else{
     $productos = buscaProducto($usu,$sucursal);
  }

  $prodsSeleccionados = buscaProdsTempVentas($usuario);
  $respTotal = sumaCampo('precio','temporal','usuario',$usuario);
  $total = $respTotal['total'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
   <title>Registro de Ventas</title>
</head>

<body onload="valor();">
  <form name="form1" method="post" action="add_sale.php">
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <table width="100%" align="center">
                  <tr>  
                     <td width="30%"><input name="codigo" type="text" class="form-control" id="busqueda" size="51" maxlength="50" oninput="mayusculas(event)"></td>
                     <td width="5%">&nbsp;</td>
                     <td width="20%"><input type="submit" id="boton" class="btn btn-primary" name="Submit" value="Buscar" /></td>
                     <td width="20%"><input type="button" id="boton" class="btn btn-primary" name="Agregar" value="Agregar" onClick="agregar();" /></td>
                  </tr>
               </table>
            </div>   
         </div>   
      </div>
   </div>
   <div class="col-md-11">
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
            <tr>
               <td width="3%">Sel</td>
               <td width="70%">Nombre</td>
               <td width="27%">Precio</td> 
            </tr>
            </thead>
            <tbody>
               <?php foreach ($productos as $res):?>
                  <tr>
                     <?php if ($cont == 0){ ?>
                        <td width="3%"><input type='radio' name='empresa' value='<?php echo $res["id"].$aux.$res["sale_price"] ?>' onClick='valor();' checked/></td>
                     <?php }else{ ?>
                        <td width="3%"><input type='radio' name='empresa' value='<?php echo $res["id"].$aux.$res["sale_price"] ?>' onClick='valor();'/></td>
                     <?php } ?>
                     <td width="70%"><?php echo $res['name'] ?></td>
                     <td width="27%"><?php echo $res['sale_price'] ?></td>
                  </tr>
               <?php 
                  $cont++;    
                  endforeach;
               ?>
            </tbody>
         </table>
      </div>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <table width="100%" align="center">       
                  <tr>
                     <td><input type="button" id="boton" class="btn btn-primary" name="Eliminar" value="Eliminar" onClick="eliminar();" /></td>
                     <td width="20%">&nbsp;</td>
                     <td width="=25%"><input type="number" class="form-control" name="idCliente" placeholder="id credencial" long="21"/></td>
                     <td><a href="#" onclick="descuento();" class="btn btn-primary">Realizar Venta</a></td>
                     <td width="5%">&nbsp;</td>
                     <td><a href="#" onclick="apartado();" class="btn btn-primary">Crédito</a></td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-11">
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
            <tr>
               <td width="3%">Sel</td>
               <td width="70%">Nombre</td>
               <td width="7%">Cantidad</td>
               <td width="20%">Precio</td> 
            </tr>
            </thead>
            <tbody>
            <?php foreach ($prodsSeleccionados as $f): ?>
               <tr>
                  <td width="3%"><input type='radio' name='elimina' value='<?php echo $f["cve_temporal"].$aux.$f["product_id"] ?>'/></td>
                  <td width="70%"><?php echo $f['name'] ?></td>
                  <td width="7%"><input type='text' class="form-control" name='cantidad' value='<?php echo $f["qty"] ?>' onChange="multiplica();"/></td>
                  <td width="20%"><?php echo $f['precio'] ?></td>
               </tr>
            <?php endforeach; ?>
            </tbody>
         </table>
      </div>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <table HEIGHT="70px" id="tab" align="right">
                  <tr>
                     <td width="5%" style="font-size:40px" ><b>Total</b></td> 
                     <td width="70%" style="font-size:40px" align="right"><?php echo money_format('%.2n',$total); ?></td>
                  </tr>
               </table>
            </div>   
         </div>
      </div>
   </div>      
   <input type="hidden" name="idProd" value="">
   <input type="hidden" name="precio" value="">
   <input type="hidden" name="multiplos" value="">
   <input type="hidden" name="cveTemp" value="">
   <input type="hidden" name="idSuc" value="<?php echo ucfirst($user['idSucursal']) ?>">
   <input type="hidden" name="user" value="<?php echo ucfirst($user['name']) ?>">
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
