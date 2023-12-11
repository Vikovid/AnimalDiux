<?php
   $page_title = 'Gastos mensuales por categoria';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $all_categorias = find_all('categories');

   $meses = array(
      "01" => "Enero",
      "02" => "Febrero",
      "03" => "Marzo",
      "04" => "Abril",
      "05" => "Mayo",
      "06" => "Junio",
      "07" => "Julio",
      "08" => "Agosto",
      "09" => "Septiembre",
      "10" => "Octubre",
      "11" => "Noviembre",
      "12" => "Diciembre"
   );

   $categ = (isset($_POST['categoria']) && $_POST['categoria']) ? remove_junk($db->escape($_POST['categoria'])):'';
   $anio =  (isset($_POST['anio'])      && $_POST['anio']) ? remove_junk($db->escape($_POST['anio'])):date('Y');
   $mes =   (isset($_POST['mes'])       && $_POST['mes']) ? remove_junk($db->escape($_POST['mes'])):date('m');
   
   $fechaIni = date('Y/m/d', strtotime($anio."/".$mes."/01"));
   $numDias = date('t', strtotime($fechaIni));
   $fechaFin = date("Y/m/d", strtotime($anio."/".$mes."/".$numDias));
   
   $fechIni = date ('d-m-Y', strtotime($fechaIni));
   $fechFin = date ('d-m-Y', strtotime($fechaFin));

   if ($categ != ""){
      $respTotal = gastosMACTotal($categ,$fechaIni,$fechaFin);
      $gastosDia = gastosMAC($categ,$fechaIni,$fechaFin);
   }else{
      $respTotal = gastosMesAnioTotal($fechaIni,$fechaFin);
      $gastosDia = gastosMesAnio($fechaIni,$fechaFin);      
   }
   
   $total = $respTotal['total'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<form name="form1" method="post" action="monthly_sales_gastos_categoria.php">
   <div class="row col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <span>
      <?php echo "Total: $".($total > 0 ? $total:'0.00').'<br>'?>
      <?php echo "Periodo del: $fechIni al $fechFin."?>
   </span>
   <div class="rowcol-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-3">
                  <select class="form-control" name="categoria">
                     <option value="">Categoria</option>
                     <?php  foreach ($all_categorias as $id): ?>
                        <option value="<?php echo $id['id'] ?>">
                           <?php echo remove_junk($id['name']) ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="col-md-2">
                  <select class="form-control" name="mes" >
                     <option value="">Mes</option>
                     <?php foreach ($meses as $mesNum => $mesNom): ?>
                        <option value="<?php echo $mesNum ?>">
                           <?php echo $mesNom ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="anio" >
                     <option value="">Año</option>
                     <?php $i = (int)2020; while ($i <= 2040):?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                     <?php $i++; endwhile; ?>
                  </select>
               </div>  
               <a href="#" onclick="ventasGastosCatMens();" class="btn btn-primary">Buscar</a>
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
            </div>   
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
               <tr>
                  <th class="text-center" style="width: 01%;"> #</th>
                  <th class="text-left"   style="width: 20%;"> Categoria</th>
                  <th class="text-left"   style="width: 20%;"> Descripción</th>
                  <th class="text-center" style="width: 15%;"> Proveedor </th>
                  <th class="text-center" style="width: 15%;"> Monto </th>
                  <th class="text-center" style="width: 05%;"> Metodo de pago </th>
                  <th class="text-center" style="width: 15%;"> Fecha </th>
                  <th class="text-center" style="width: 09%;"> Acciones </th>
               </tr>
               </thead>
               <tbody>
               <?php foreach ($gastosDia as $sale):?>
               <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td class="text-left"><?php echo remove_junk($sale['name']); ?></td>
                  <td class="text-left"><?php echo remove_junk($sale['descripcion']); ?></td>
                  <td class="text-center"><?php echo remove_junk($sale['nom_proveedor']); ?></td>
                  <td class="text-center"><?php echo '$'.$sale['total']; ?></td>
                  <td class="text-center"><?php echo remove_junk($sale['tipo_pago']); ?></td>
                  <td class="text-center"><?php echo date("d-m-Y", strtotime ($sale['fecha'])); ?></td>
                  <td class="text-center"> 
                     <a href="../gastos/edit_gasto.php?id=<?php echo $sale['id']?>&idProveedor=<?php echo $sale['idProveedor'] ?>&idCategoria=<?php echo $sale['categoria'] ?>&id_pago=<?php echo $sale['tipo_pago'] ?>" class="btn btn-success btn-xs" title="Editar Gasto" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                     </a>
<!--                      <a href="../gastos/delete_gasto.php?id=<?php echo (int)$sale['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-trash"></span>
                     </a> -->
                  </td>
               </tr>
               <?php endforeach;?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</form>
<?php include_once('../layouts/footer.php'); ?>