<?php
  $page_title = 'Admin página de inicio';
  require_once('../../modelo/load.php');
  page_require_level(1);
  // Checkin What level user has permission to view this page
  $all_sucursal = find_all('sucursal');  
  $user = current_user(); 
  $idSucursal = $user['idSucursal'];

  ini_set('date.timezone','America/Mexico_City');
  $fecha=date('Y/m/d',time());
    
  $month = date('m');
  $year = date('Y');
  $day = date("d", mktime(0,0,0, $month+1, 0, $year));
  $fechaFin = date("Y/m/d", strtotime($year.$month.$day));
  $fechaIni = date('Y/m/d', mktime(0,0,0, $month, 1, $year));

  $adminSuc = "";
  $montofin = 0;
  $monto2 = 0;

  if(isset($_POST['sucursal'])){  
    $adminSuc =  remove_junk($db->escape($_POST['sucursal']));//prueba
  }

  $c_product       = count_by_id('products');
  $c_sucursales    = count_su_id('sucursal');
  $c_user          = count_by_id('users');

  if ($adminSuc != ""){
     $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$adminSuc);
     $alertaProductos = alertaProductos($adminSuc);
     $consCaja = saldoCajaDia($fecha,$adminSuc,'4');
     $consEfecCred = saldoEfectivoDia($fecha,$adminSuc,'6');
     $consRetEfec = saldoEfectivoDia($fecha,$adminSuc,'7');
     $consVentaEfectivo = pagoPeriodoPortipo($adminSuc,'1',$fecha,$fecha);
     $consVentaTrans = pagoPeriodoPortipo($adminSuc,'2',$fecha,$fecha);
     $consGastoEfectivo = gastoPeriodoPortipo($adminSuc,'1',$fecha,$fecha);
     $consVentaTotalMes = ventasPeriodoSuc($adminSuc,$fechaIni,$fechaFin);
     $consGastoDia = gastosPeriodoSuc($adminSuc,$fecha,$fecha);
     $consGastoDiaEfec = gastoPeriodoPortipo($adminSuc,'1',$fecha,$fecha);
     $consGastoDiaTrans = gastoPeriodoPortipo($adminSuc,'2',$fecha,$fecha);
     $consGastoDiaDep = gastoPeriodoPortipo($adminSuc,'3',$fecha,$fecha);
     $consGastoDiaTar = gastoPeriodoPortipo($adminSuc,'4',$fecha,$fecha);
     $consGastoMes = gastosPeriodoSuc($adminSuc,$fechaIni,$fechaFin);
     $consVentaDia = ventasPeriodoSuc($adminSuc,$fecha,$fecha);
     $consVentaDep = pagoPeriodoPortipo($adminSuc,'3',$fecha,$fecha);
     $consVentaTar = pagoPeriodoPortipo($adminSuc,'4',$fecha,$fecha);
     $clientes = mejoresClientes($adminSuc);
     $consCreditoDiaEfec = pagoCreditoDia($adminSuc,$fecha,'1');
     $consCreditoDia = pagoCreditoDia($adminSuc,$fecha,'');
 }else{
     $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$idSucursal);
     $alertaProductos = alertaProductos($idSucursal);
     $consCaja = saldoCajaDia($fecha,$idSucursal,'4');
     $consEfecCred = saldoEfectivoDia($fecha,$idSucursal,'6');
     $consRetEfec = saldoEfectivoDia($fecha,$idSucursal,'7');
     $consVentaEfectivo = pagoPeriodoPortipo($idSucursal,'1',$fecha,$fecha);
     $consVentaTrans = pagoPeriodoPortipo($idSucursal,'2',$fecha,$fecha);
     $consGastoEfectivo = gastoPeriodoPortipo($idSucursal,'1',$fecha,$fecha);
     $consVentaTotalMes = ventasPeriodoSuc($idSucursal,$fechaIni,$fechaFin);
     $consGastoDia = gastosPeriodoSuc($idSucursal,$fecha,$fecha);
     $consGastoDiaEfec = gastoPeriodoPortipo($idSucursal,'1',$fecha,$fecha);
     $consGastoDiaTrans = gastoPeriodoPortipo($idSucursal,'2',$fecha,$fecha);
     $consGastoDiaDep = gastoPeriodoPortipo($idSucursal,'3',$fecha,$fecha);
     $consGastoDiaTar = gastoPeriodoPortipo($idSucursal,'4',$fecha,$fecha);
     $consGastoMes = gastosPeriodoSuc($idSucursal,$fechaIni,$fechaFin);
     $consVentaDia = ventasPeriodoSuc($idSucursal,$fecha,$fecha);
     $consVentaDep = pagoPeriodoPortipo($idSucursal,'3',$fecha,$fecha);
     $consVentaTar = pagoPeriodoPortipo($idSucursal,'4',$fecha,$fecha);
     $clientes = mejoresClientes($idSucursal);     
     $consCreditoDiaEfec = pagoCreditoDia($idSucursal,$fecha,'1');
     $consCreditoDia = pagoCreditoDia($idSucursal,$fecha,'');
  }

  $nuevosClientes = nuevosClientes();

  $nom_suc = $sucursal['nom_sucursal'];
  $gasto_dia = $consGastoDia['total'];
  $venta_dia = $consVentaDia['totalVentas'];  
?>
<?php include_once('../layouts/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Página de inicio</title>
</head>

<script language="Javascript">

function resumen(){
  document.form1.action = "admin.php";
  document.form1.submit();
}

function foco(){
  document.form1.sucursal.focus();
}

</script>

<body onload="foco();">
  <form name="form1" method="post" action="admin.php">

<div class="row">
   <div class="col-md-6">
     <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-3">
      <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-green">
            <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_user['total']; ?> </h2>
          <p class="text-muted">Usuarios</p>
        </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-blue">
            <i class="glyphicon glyphicon-shopping-cart"></i>
         </div>
         <div class="panel-value pull-right">
            <h2 class="margin-top"> <?php  echo $c_product['total']; ?> </h2>
            <p class="text-muted">Productos</p>
         </div>
      </div>
   </div>
   <div class="col-md-3">
      <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-red">
            <i class="glyphicon glyphicon-home"></i>
         </div>
         <div class="panel-value pull-right">
            <h2 class="margin-top"> <?php  echo $c_sucursales['total']; ?> </h2>
            <p class="text-muted">Sucursales Activas</p>
         </div>
      </div>
   </div>
   <img src="../../libs/imagenes/Logo.png" height="120" width="150" alt="" align="center">
</div>
<div class="row">
   <div class="col-md-10">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div>
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
                  <a href="#" onclick="resumen();" class="btn btn-primary">Buscar</a> 
                  <div class="pull-right">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Sucursal:</span>
                        <?php echo $nom_suc; ?>
                     </strong>
                  </div>
               </div>   
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>alerta de existencias</span>
                     </strong>
                  </div>
                  <div class="panel-body">
                     <table class="table table-striped table-bordered table-condensed">
                        <thead>
                        <tr>
                           <th>Producto</th>
                           <th>Cantidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($alertaProductos as $alerta): ?>
                        <tr>
                           <td><?php echo remove_junk(first_character($alerta['name'])); ?></td>
                           <td><?php echo remove_junk(first_character($alerta['quantity'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Resumen</span>
                     </strong>
                  </div>
                  <div class="panel-body">
   		               <?php    
                        if ($consCaja != null)
                           $montofin = $consCaja['total'];
      
                        echo "<b> ENTRADA DE CAJA</b>";
                        echo"...........";
                        echo "$";
                        echo $montofin;
                        echo"<br>";

                        if ($consVentaEfectivo != null)
                           $ventas_efectivo = $consVentaEfectivo['total'];
      
                        echo "<b> VENTA EN EFECTIVO</b>";
                        echo"........";
                        echo "$";
                        echo $ventas_efectivo;
                        echo"<br>";
            
                        if ($consCreditoDiaEfec != null)
                           $monto2 = $consCreditoDiaEfec['total'];
      
                        echo "<b>PAGOS DE CREDITO</b>";
                        echo".........";
                        echo "$";
                        echo $monto2;
                        echo"<br>";
        
                        if ($consGastoEfectivo != null)
                           $gasto_dia_efe = $consGastoEfectivo['total'];
      
                        echo "<b>GASTO EN EFECTIVO</b>";
                        echo"......";
                        echo "-$";
                        echo $gasto_dia_efe;
                        echo"<br>";

                        if ($consRetEfec != null)
                           $retiro_efectivo = $consRetEfec['total'];
      
                        echo "<b>RETIRO DE EFECTIVO</b>";
                        echo"......";
                        echo "-$";
                        echo $retiro_efectivo;
                        echo"<br>";

                        $total_dia=$ventas_efectivo+$montofin-$gasto_dia_efe-$retiro_efectivo+$monto2;
		                 ?>
                  </div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Total del dia en efectivo</span>
                        <?php echo "$";echo $total_dia; ?>
                     </strong>
                  </div>
                  <div class="panel-body">
                     <?php $total_ventas_daily_tran1 = $consVentaTrans['total']; ?>
                  </div>
               </div>
               <div class="col-md-15">
                  <div class="panel panel-default">
                     </h4>
                     </a>
                  </div>
               </div>
            </div>
 <!-- aqui va los desgrose de Gastos-->
 
            <div class="col-md-3">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Gastos del dia </span>
                        <?php echo "$";	echo $gasto_dia; ?>
                     </strong>
                  </div>
                  <div class="panel-body">
 		                 <div class="col-md-15">
                     </div>
	                   <?php 
		                    echo "<b> EN EFECTIVO </b> ";
		                    $gasto_dia_efe = $consGastoDiaEfec['total'];
		                    echo "<br>";
		                    echo "$";
		                    echo $gasto_dia_efe;
		                    echo "<br>";
		                    echo "<b> EN TRANFERENCIA </b> ";
		                    $gasto_dia_tran = $consGastoDiaTrans['total'];
		                    echo "<br>";
		                    echo "$";
		                    echo $gasto_dia_tran;
		                    echo "<br>";
		                    echo "<b> EN DEPOSITO </b> ";
		                    $gasto_dia_depo = $consGastoDiaDep['total'];
		                    echo "<br>";
		                    echo "$";
		                    echo $gasto_dia_depo;
		                    echo "<br>";
		                    echo "<b> EN TARJETA </b> ";
		                    $gasto_dia_tar = $consGastoDiaTar['total'];
		                    echo "<br>";
		                    echo "$";
		                    echo $gasto_dia_tar;
		                    echo "<br>";
		                    $gasto_mes = $consGastoMes['total'];
	                   ?>
                     </h4>
                     <span class="list-group-item-text pull-right">
                     </span>
                     </a>
                  </div>
               </div>
            </div>
            <!-- aqui va los desgrose de Ventas-->
            <div class="col-md-3">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Ventas del Dia</span>
                        <?php echo "$";	echo $venta_dia; ?>
                     </strong>
                  </div>
                  <div class="panel-body">
	                   <div class="col-md-15">
                     </div>
	                   <?php 
	                      echo "<b> EN EFECTIVO </b> ";
		                    echo "<br>";
		                    echo "$";
                        echo $ventas_efectivo;
		                    echo "<br>";
		                    echo "<b> EN TRANFERENCIA </b> ";
		                    echo "<br>";
		                    echo "$";
		                    echo $total_ventas_daily_tran1;
		                    echo "<br>";
		                    echo "<b>EN DEPOSITO </b> ";
		                    $deposito = $consVentaDep['total'];
		                    echo "<br>";
		                    echo "$";
		                    echo $deposito;
		                    echo "<br>";
		                    echo "<b> EN TARJETA </b> ";
		                    $tarjeta = $consVentaTar['total'];
		                    echo "<br>";
		                    echo "$";
		                    echo $tarjeta;
		                    echo "<br>";
                        echo "<b> EN CREDITO </b> ";
                        $credito = $consCreditoDia['total'];
                        echo "<br>";
                        echo "$";
                        echo $credito;
                        echo "<br>";
	                   ?>
	                </div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Resumen del mes</span>
                     </strong>
                  </div>
                  <div class="panel-body">
                     <table class="table table-striped table-bordered table-condensed">
                        <thead>
                           <tr>
	                            <div class="col-md-15">
    	                           <div class="panel panel-default">
    	                              <span class="list-group-item-text pull-right">
                                    </h4>
                                    </a>
                                    </span>
                                    <?php
	  	                                 echo "<b>VENTAS DEL MES</b>";
		                                   echo "  ";
		                                   $totalVentasMes = $consVentaTotalMes['totalVentas'];
  		                                 echo "<br>";
		                                   echo "$";
		                                   echo $totalVentasMes;	
		                                   echo "<br>";
		                                   echo "<b>GASTO DEL MES </b>";
		                                   echo "  ";
		                                   echo"<br>";
		                                   echo "$";
		                                   echo $gasto_mes;
		                                   echo"<br>";
		                                   echo "<b> TOTAL DEL MES</b>";
		                                   echo"<br>";
		                                   echo "$";
                                       $total_mes=$totalVentasMes-$gasto_mes;
		                                   echo $total_mes;
 	                                  ?>
                                 </div>
                              </div>
                           </tr>
                        </thead>
                     </table>
                  </div>
                  <!-- aqui van los clientes-->
                  <div class="col-md-15">
                     <div class="panel panel-default">
                        <div class="panel-heading">
                           <strong>
                              <span class="glyphicon glyphicon-th"></span>
                              <span>Los Mejores Clientes</span>
                           </strong>
                        </div>
                        <div class="panel-body">
                        <table class="table table-striped table-bordered table-condensed">
                           <thead>
                              <tr>
                                 <th>id</th>
                                 <th>Nombre</th>
                                 <th>Cantidad</th>
                                 <th>Puntos</th>
                              </tr>
                           </thead>
                           <tbody>
                           <?php foreach ($clientes as $cliente): ?>
                              <tr>
                                 <td><?php echo remove_junk(first_character($cliente['idCliente'])); ?></td>
                                 <td><?php echo remove_junk(first_character($cliente['nom_cliente'])); ?></td>
                                 <td><?php echo remove_junk(first_character($cliente['venta'])); ?></td>
                                 <td><?php echo remove_junk(first_character(floor($cliente['puntos'])));?></td>
                              </tr>
                           <?php endforeach; ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="panel panel-default">
               <div class="panel-heading">
                  <strong>
                     <span class="glyphicon glyphicon-th"></span>
                     <span>Nuevos Clientes</span>
                  </strong>
               </div>
               <div class="panel-body">
                  <table class="table table-striped table-bordered table-condensed">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Nombre</th>
                           <th>Correo</th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php foreach ($nuevosClientes as $cliente): ?>
                        <tr>
                           <td class="text-center"><?php echo count_id();?></td>
                           <td><?php echo remove_junk(first_character($cliente['nom_cliente'])); ?></td>
                           <td><?php echo remove_junk(first_character($cliente['correo'])); ?></td>
                        </tr>
                     <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
