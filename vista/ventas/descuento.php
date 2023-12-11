<?php 
   $page_title = 'Descuento/Forma de pago';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(3);
   $vendedores = find_all('users');
   $user = current_user(); 
   $usuario = $user['name'];
   $usu = $user['id'];
   $sucursal = $user['idSucursal'];
   $idCliente= isset($_POST['idCliente']) ? $_POST['idCliente']:'';

   $nomCliente = "";
   $venta = 0;
   $puntos = 0;
   $totaldesc = 0;

   $respSuma = sumaCampo('precio','temporal','usuario',$usuario);
   $total = $respSuma['total'];      

   $parametros = find_by_id('parametros',1);
   $porcDescuento = $parametros['porcDescuento'];
   $descAdicional = $parametros['descAdicional'];

   if ($idCliente != ""){

      $respPuntos = obtenPuntos($idCliente);

      if ($respPuntos != null){
         $puntos = $respPuntos['venta'];
         $venta = $respPuntos['venta'];
         $nom_clienteC = $respPuntos['nom_cliente'];
      }

      if ($nomCliente == ""){

         $consCliente = buscaRegistroPorCampo('cliente','idcredencial',$idCliente);

         if ($consCliente != null)
            $nomCliente = $consCliente['nom_cliente'];
      }

      $consNumRegs = cuentaRegsTemporal($usuario,$sucursal);

      if ($consNumRegs != null and $puntos >= 1){
         $totaldesc = ((int)$puntos * ($porcDescuento/100)) * $consNumRegs['numRegs'];
      }
   }
?>
<script language="Javascript">

function aplicar(){
   
   var efectivo = 0;
   var transferencia = 0;
   var deposito = 0;
   var tarjeta = 0;
   var sumaTotal = 0;

   if (document.form1.efectivo.value != "")
      efectivo = parseFloat(document.form1.efectivo.value);
   if (document.form1.transferencia.value != "")
      transferencia = parseFloat(document.form1.transferencia.value);
   if (document.form1.deposito.value != "")
      deposito = parseFloat(document.form1.deposito.value);
   if (document.form1.tarjeta.value != "")
      tarjeta = parseFloat(document.form1.tarjeta.value);

   sumaTotal = (efectivo + transferencia + deposito + tarjeta).toFixed(2);

   if (document.form1.hayDescuento.value == "0"){
      if (sumaTotal < document.form1.total.value){
        alert("La suma de cantidades es menor al total de compra");
        return -1;
      }

      if (sumaTotal > document.form1.total.value){
        alert("La suma de cantidades es mayor al total de compra");
        return -1;
      }
   }

   if (document.form1.hayDescuento.value == "1"){
      if (sumaTotal < document.form1.totalConDesc.value){
        alert("La suma de cantidades es menor al total con descuento");
        return -1;
      }

      if (sumaTotal > document.form1.totalConDesc.value){
        alert("La suma de cantidades es mayor al total con descuento");
        return -1;
      }
   }

   if (document.form1.efectivo.value == "" && document.form1.transferencia.value == "" && document.form1.deposito.value == "" && document.form1.tarjeta.value == ""){
        alert ("Debe proporcionar las cantidades a cobrar");
        return -1;
   }

   if (document.form1.vendedor.value == ""){
        alert("Debe seleccionar a un vendedor.");
        return -1;
   }

   document.form1.action = "ventas.php";
   document.form1.submit();
}

function suma(){
   var efectivo = 0;
   var transferencia = 0;
   var deposito = 0;
   var tarjeta = 0;
   var sumaTotal = 0;
   var totTarjeta = 0;

   if (document.form1.efectivo.value != "")
      efectivo = parseFloat(document.form1.efectivo.value);
   if (document.form1.transferencia.value != "")
      transferencia = parseFloat(document.form1.transferencia.value);
   if (document.form1.deposito.value != "")
      deposito = parseFloat(document.form1.deposito.value);
   if (document.form1.tarjeta.value != ""){
      tarjeta = parseFloat(document.form1.tarjeta.value);
      totTarjeta = tarjeta * .05;
   }else{
      totTarjeta = "";
   }

   sumaTotal = efectivo + transferencia + deposito + tarjeta;
   
   document.form1.sumaTotal.value = sumaTotal.toFixed(2);
   document.form1.totTarjeta.value = totTarjeta;   
}

function regresa(){
   document.form1.action = "add_sale.php";
   document.form1.submit();
}

function vuelto(){
   var pago = 0;
   var efectivo = 0;
   var cambio = 0;

   if (document.form1.pago.value != ""){
      pago = parseFloat(document.form1.pago.value);
      efectivo = parseFloat(document.form1.efectivo.value);

      cambio = pago - efectivo;
   }else{
      cambio = "";
   }
   
   document.form1.cambio.value = cambio;
}

function descuento(){
  var descAdicional = 0;

  descAdicional = 1 - document.form1.porcDescAdicional.value/100;

  if (document.form1.porcDescAdicional.value > 0){
     document.form1.total.value = (document.form1.totalOrig.value * descAdicional).toFixed(2);
     document.form1.totalConDesc.value = (document.form1.totalConDescOrig.value * descAdicional).toFixed(2);
  }else{
     document.form1.total.value = document.form1.totalOrig.value;
     document.form1.totalConDesc.value = document.form1.totalConDescOrig.value;
  }
}

</script>

<?php include_once('../layouts/header.php'); ?>

<form name="form1" method="post" action="ventas.php">
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
    <div class="col-md-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Descuento/Forma de Pago</span>
         </strong>
        </div>
     
        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td width="50%">Cliente</td>
                <td width="50%">Puntos</td>
              </tr>
              <tr>
                <td width="50%"><?php echo remove_junk($nomCliente); ?></td>
                <td width="50%"><?php echo floor($venta) ?></td>
              </tr>
            </thead>
          </table>
             
          <?php 
              if ($puntos >= 1)
                 $totalfin = round($total - $totaldesc,2); 
              else
                 $totalfin = $total;
          ?>
   
          <div class="input-group">
            <div class="form">
              <span>Total de compra</span>
              <input type="number" name="total" value="<?php echo $total ?>" readonly="readonly">
              <span> Aplicando Descuento </span>
              <input type="number" name="totalConDesc" value="<?php echo $totalfin ?>" readonly="readonly">
              <input type="hidden" name="totaldes" value="<?php echo $totaldesc ?>" readonly="readonly">
              <input type="hidden" name="puntosdes" value="<?php echo floor($puntos) ?>">
              <input type="hidden" name="idCliente" value="<?php echo $idCliente ?>">
              <input type="hidden" name="user" value="<?php echo $usuario ?>">
              <input type="hidden" name="idSuc" value="<?php echo $sucursal ?>">
              <input type="hidden" name="idUsu" value="<?php echo $usu ?>">
              <input type="hidden" name="totalOrig" value="<?php echo $total ?>">
              <input type="hidden" name="totalConDescOrig" value="<?php echo $totalfin ?>">
              <input type="hidden" name="descAdicional" value="<?php echo $descAdicional ?>">
            </div>
          </div>

          <div class="input-group">
          	<div class="form">
          	   <br>
               <select class="form-control" name="vendedor">
                  <option value="">Selecciona vendedor</option>
                  <?php  foreach ($vendedores as $id): ?>
                  <option value="<?php echo $id['username'] ?>">
                  <?php echo remove_junk($id['name']); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
          </div>

          <div class="input-group">
            <div class="form">
               <br>                   
            </div>   
          </div>

          <div class="form-group">
             <div class="row">           
               <div class="col-md-3">
                 <div class="input-group">
                   <span class="input-group-addon">
                     <i class="glyphicon glyphicon-usd"></i>
                   </span>
             <?php
              if ($venta == 0) {?>
                      <select class="form-control" name="hayDescuento">
                         <option  value="0">Sin descuento</option>
                      </select>
             <?php }else{ ?>
                      <select class="form-control" name="hayDescuento">
                         <option value="0">Sin descuento</option>
                         <option value="1">Con descuento</option>
                      </select>
             <?php } ?>
                 </div>
               </div>
               <div class="col-md-4">
                   <span>Descuento&nbsp;&nbsp;&nbsp;</span>
                   <input type="radio" name="porcDescAdicional" value="0" onclick="descuento();" checked>
                   <span>0%&nbsp;&nbsp;&nbsp;</span>
                   <input type="radio" name="porcDescAdicional" value="3" onclick="descuento();">
                   <span>3%&nbsp;&nbsp;&nbsp;</span>
                   <input type="radio" name="porcDescAdicional" value="5" onclick="descuento();">
                   <span>5%&nbsp;&nbsp;&nbsp;</span>
                   <input type="radio" name="porcDescAdicional" value="10" onclick="descuento();">
                   <span>10%</span>
               </div>
             </div>
          </div>
             
          <div class="form-group">
             <div class="row">           
               <div class="col-md-3">
                 <div class="input-group">
                   <span class="input-group-addon">
                     <i class="glyphicon glyphicon-usd"></i>
                   </span>
                   <input type="number" step="0.01" class="form-control" name="efectivo" placeholder="Efectivo" onkeyup="suma();">
                 </div>
               </div>
             </div>
          </div>

          <div class="form-group">
             <div class="row">           
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="transferencia" placeholder="Transferencia" onkeyup="suma();">
                   </div>
                </div>
             </div>
          </div>

          <div class="form-group">
             <div class="row">           
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="deposito" placeholder="Depósito" onkeyup="suma();">
                   </div>
                </div>
             </div>
          </div>

          <div class="form-group">
             <div class="row">           
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="tarjeta" placeholder="Tarjeta" onkeyup="suma();">
                   </div>
                </div>
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="totTarjeta" placeholder="+ 5%" readonly>
                   </div>
                </div>
             </div>
          </div>

          <div class="form-group">
             <div class="row">           
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="sumaTotal" placeholder="Suma Total" readonly>
                   </div>
                </div>
             </div>
          </div>

          <div class="form-group">
             <div class="row">           
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="pago" placeholder="Pago" onkeyup="vuelto();">
                   </div>
                </div>
                <div class="col-md-3">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="cambio" placeholder="Cambio" readonly>
                   </div>
                </div>
             </div>
          </div>
        </div>
        <br>
        <input type="button" name="button" onclick="aplicar();" class="btn btn-primary" value="Realizar Venta">
        <a href="#" onclick="regresa();" class="btn btn-danger">Regresar</a> 
      </div>
   </div>
</div>
</form>
<?php include_once('../layouts/footer.php'); ?>
