<?php
  $page_title = 'Lista de ventas';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  $formaPago = "";
  $mes = "";
  $anio = "";
  $ticketAnt = 0;
  $nomCliente = "";

  if (isset($_POST['formaPago']))  
     $formaPago =  remove_junk($db->escape($_POST['formaPago']));

  if (isset($_POST['mes']))
     $mes =  remove_junk($db->escape($_POST['mes']));//prueba

  if (isset($_POST['anio']))
     $anio =  remove_junk($db->escape($_POST['anio']));//prueba

  if ($mes == "" && $anio == ""){                          
     $mes = date('m');
     $anio = date('Y');
     $day = date("d", mktime(0,0,0, $mes+1, 0, $anio));
     $fechaInicial = $anio."/".$mes."/01";
     $fechaFinal = $anio."/".$mes."/".$day;
  }

  if ($mes != "" && $anio == ""){
     $anio = date('Y');
     $fechaInicial = $anio."/".$mes."/01";
     $numDias = date('t', strtotime($fechaInicial));
     $fechaFinal = $anio."/".$mes."/".$numDias;
  }

  if ($mes == "" && $anio != ""){
     $mes = date('m');
     $fechaInicial = $anio."/".$mes."/01";
     $numDias = date('t', strtotime($fechaInicial));
     $fechaFinal = $anio."/".$mes."/".$numDias;
  }

  if ($mes != "" && $anio != ""){
     $fechaInicial = $anio."/".$mes."/01";
     $numDias = date('t', strtotime($fechaInicial));
     $fechaFinal = $anio."/".$mes."/".$numDias;
  }

  $fechaIni = date('Y/m/d', strtotime($fechaInicial));
  $fechaFin = date("Y/m/d", strtotime($fechaFinal));
  $fechIni = date ('d-m-Y', strtotime($fechaInicial));
  $fechFin = date ('d-m-Y', strtotime($fechaFinal));

  $sales = venta($fechaIni,$fechaFin);
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function ventas(){
  document.form1.action = "sales.php";
  document.form1.submit();
}

function foco(){
  document.form1.encargado.focus();
}

function excel(){
  document.form1.action = "../excel/ventasExcel.php";
  document.form1.submit();
}

</script>

<body onload="foco();">
  <form name="form1" method="post" action="sales.php">

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-2">
                  <select class="form-control" name="formaPago">
                     <option value="">Tipo de pago</option>
                     <option value="1">Efectivo</option>
                     <option value="2">Transferencia</option>
                     <option value="3">Deposito</option>
                     <option value="4">Tarjeta</option>
                     <option value="5">Mixto</option>
                  </select>
               </div>      
               <div class="col-md-2">
                  <select class="form-control" name="mes">
                     <option value="">mes</option>
                     <option value="01">Enero</option>
                     <option value="02">Febrero</option>
                     <option value="03">Marzo</option>
                     <option value="04">Abril</option>
                     <option value="05">Mayo</option>
                     <option value="06">Junio</option>
                     <option value="07">Julio</option>
                     <option value="08">Agosto</option>
                     <option value="09">Septiembre</option>
                     <option value="10">Octubre</option>
                     <option value="11">Noviembre</option>
                     <option value="12">Diciembre</option>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="anio">
                     <option value="">año</option>
                     <option value="2020">2020</option>
                     <option value="2021">2021</option>
                     <option value="2022">2022</option>
                     <option value="2023">2023</option>
                     <option value="2024">2024</option>
                     <option value="2025">2025</option>
                     <option value="2026">2026</option>
                     <option value="2027">2027</option>
                     <option value="2028">2028</option>
                     <option value="2029">2029</option>
                     <option value="2030">2030</option>
                     <option value="2031">2031</option>
                     <option value="2032">2032</option>
                     <option value="2033">2033</option>
                     <option value="2034">2034</option>
                     <option value="2035">2035</option>
                     <option value="2036">2036</option>
                     <option value="2037">2037</option>
                     <option value="2038">2038</option>
                     <option value="2039">2039</option>
                     <option value="2040">2040</option>
                  </select>
               </div>  
               <a href="#" onclick="ventas();" class="btn btn-primary">Buscar</a> 
               <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>
               <div class="pull-right">
                  <a href="add_sale.php" class="btn btn-primary">Agregar venta</a>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
               </div>
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered table-striped">
            <thead>
               <tr>
                  <th class="text-center" style="width: 10%;">Vendedor</th>
                  <th> Nombre del producto </th>
                  <th class="text-center" style="width: 10%;"> Cantidad</th>
                  <th class="text-center" style="width: 7%;"> Total </th>
                  <th class="text-center" style="width: 10%;"> Tipo Pago </th>
                  <th class="text-center" style="width: 9%;"> Fecha </th>
                  <th class="text-center" style="width: 10%;"> Acciones </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($sales as $sale):?>
               <?php 
                   
                  $consNumPagos = numTiposPagos($sale['id_ticket']);
                  $numPagos = $consNumPagos['numPagos'];
                  $abonoTotal = $consNumPagos['cantidad'];

                  if ($numPagos == "1"){

                     $consTipoPago = buscaRegistroPorCampo('pagos','id_ticket',$sale['id_ticket']);
                     $idTipoPago = $consTipoPago['id_tipo'];

                     if ($idTipoPago == "1")
                        $tipoPago = "Efectivo";
                     if ($idTipoPago == "2")
                        $tipoPago = "Transferencia";
                     if ($idTipoPago == "3")
                        $tipoPago = "Deposito";
                     if ($idTipoPago == "4")
                        $tipoPago = "Tarjeta";
                  }else{
                 	   $tipoPago = "Mixto";
                     $idTipoPago = "5";
                  }

                  $vendedor = $sale['vendedor'];

                  $cantidad = $sale['qty'];
                  $fecha = date("d-m-Y", strtotime ($sale['date']));

                  if ($sale['tipo_pago'] == "0"){
                     $producto = $sale['name'];
                     $precio = $sale['price'];
                  }

                  if ($sale['tipo_pago'] != "0" && $ticketAnt != $sale['id_ticket']){                 
                     $cliente = buscaRegistroPorCampo('cliente','idcredencial',$sale['idCliente']);
                     if ($cliente != null)
                        $nomCliente = $cliente['nom_cliente'];

                     $producto = "Abono crédito: ".$nomCliente;
                     $precio = $abonoTotal;
                  }
               ?>    
               <?php if ($formaPago == ""){ ?>
                  <?php if($sale['tipo_pago'] == "0"){ ?>             
                  <tr>
                     <td><?php echo remove_junk($vendedor); ?></td>
                     <td><?php echo utf8_decode(remove_junk($producto)); ?></td>
                     <td class="text-center"><?php echo $cantidad; ?></td>
                     <td class="text-right"><?php echo remove_junk($precio); ?></td>
                     <td class="text-center"><?php echo $tipoPago; ?></td>
                     <td class="text-center"><?php echo $fecha; ?></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="consulta_sale.php?idTicket=<?php echo (int)$sale['id_ticket'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>&cliente=<?php echo $nomCliente;?>&total=<?php echo "";?>" class="btn btn-primary btn-xs" title="Consultar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-eye-open"></span>
                           </a>
                           <a href="edit_sale.php?id=<?php echo (int)$sale['id'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>" class="btn btn-warning btn-xs" title="Editar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-edit"></span>
                           </a>
                           <a href="delete_sale.php?id=<?php echo (int)$sale['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-trash"></span>
                           </a>
                        </div>
                     </td>
                  </tr>
                  <?php } ?>                     
                  <?php if($sale['tipo_pago'] != "0" && $ticketAnt != $sale['id_ticket']){ ?>                          
                  <tr>
                     <td><?php echo remove_junk($vendedor); ?></td>
                     <td><?php echo remove_junk($producto); ?></td>
                     <td class="text-center"><?php echo $cantidad; ?></td>
                     <td class="text-right"><?php echo remove_junk($precio); ?></td>
                     <td class="text-center"><?php echo $tipoPago; ?></td>
                     <td class="text-center"><?php echo $fecha; ?></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="consulta_sale.php?idTicket=<?php echo (int)$sale['id_ticket'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>&cliente=<?php echo $nomCliente;?>&total=<?php echo $abonoTotal;?>" class="btn btn-primary btn-xs" title="Consultar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-eye-open"></span>
                           </a>
                           <a href="../credito/deleteCredito.php?idTicket=<?php echo (int)$sale['id_ticket'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-trash"></span>
                           </a>                     
                        </div>
                     </td>
                  </tr>
                  <?php $ticketAnt = $sale['id_ticket']; ?>
               <?php } ?>
            <?php }else{ ?>
               <?php if($sale['tipo_pago'] == "0" && $formaPago == $idTipoPago){ ?>             
                        <tr>
                           <td><?php echo remove_junk($vendedor); ?></td>
                           <td><?php echo utf8_decode(remove_junk($producto)); ?></td>
                           <td class="text-center"><?php echo $cantidad; ?></td>
                           <td class="text-right"><?php echo remove_junk($precio); ?></td>
                           <td class="text-center"><?php echo $tipoPago; ?></td>
                           <td class="text-center"><?php echo $fecha; ?></td>
                           <td class="text-center">
                              <div class="btn-group">
                                 <a href="consulta_sale.php?idTicket=<?php echo (int)$sale['id_ticket'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>&cliente=<?php echo $nomCliente;?>&total=<?php echo "";?>" class="btn btn-primary btn-xs" title="Consultar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-eye-open"></span>
                                 </a>
                                 <a href="edit_sale.php?id=<?php echo (int)$sale['id'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>" class="btn btn-warning btn-xs" title="Editar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-edit"></span>
                                 </a>
                                 <a href="delete_sale.php?id=<?php echo (int)$sale['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-trash"></span>
                                 </a>
                              </div>
                           </td>
                        </tr>
               <?php } ?>                     
               <?php if($sale['tipo_pago'] != "0" && $ticketAnt != $sale['id_ticket'] && $formaPago == $idTipoPago){ ?>
                        <tr>
                           <td><?php echo remove_junk($vendedor); ?></td>
                           <td><?php echo remove_junk($producto); ?></td>
                           <td class="text-center"><?php echo $cantidad; ?></td>
                           <td class="text-right"><?php echo remove_junk($precio); ?></td>
                           <td class="text-center"><?php echo $tipoPago; ?></td>
                           <td class="text-center"><?php echo $fecha; ?></td>
                           <td class="text-center">
                              <div class="btn-group">
                                 <a href="consulta_sale.php?idTicket=<?php echo (int)$sale['id_ticket'];?>&vendedor=<?php echo $sale['vendedor'];?>&fecha=<?php echo $sale['date'];?>&cliente=<?php echo $nomCliente;?>&total=<?php echo $abonoTotal;?>" class="btn btn-primary btn-xs" title="Consultar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-eye-open"></span>
                                 </a>
                                 <a href="../credito/deleteCredito.php?idTicket=<?php echo (int)$sale['id_ticket'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                                 <span class="glyphicon glyphicon-trash"></span>
                                 </a>                     
                              </div>
                           </td>
                        </tr>
                        <?php $ticketAnt = $sale['id_ticket']; ?>
               <?php } ?>
            <?php } ?>
               <?php endforeach;?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>