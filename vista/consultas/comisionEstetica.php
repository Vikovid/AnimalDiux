<?php
   $page_title = 'Comisiones de Estética';
   require_once('../../modelo/load.php');

   // Checkin What level user has permission to view this page
   page_require_level(1);

   $meses = array('01'=>'Enero',
                  '02'=>'Febrero',
                  '03'=>'Marzo',
                  '04'=>'Abril',
                  '05'=>'Mayo',
                  '06'=>'Junio',
                  '07'=>'Julio',
                  '08'=>'Agosto',
                  '09'=>'Septiembre',
                  '10'=>'Octubre',
                  '11'=>'Noviembre',
                  '12'=>'Diciembre');

   $all_sucursal = find_all('sucursal');
   $responsables = find_all('users');

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   $responsable = (isset($_POST['responsable']) && $_POST['responsable'] != '') ? $_POST['responsable']:'';
   $idSucursal =  (isset($_POST['sucursal'])    && $_POST['sucursal'] !='')     ? $_POST['sucursal']   :'';

   $mes =  (isset($_POST['mes'])  && $_POST['mes']!='')  ? $_POST['mes']  : date('m');
   $anio = (isset($_POST['anio']) && $_POST['anio']!='') ? $_POST['anio'] : date('Y');

   if($idSucursal != ""){
      $consulta = buscaRegistroPorCampo('sucursal','idSucursal',$idSucursal);
      $sucursal = $consulta['nom_sucursal'];
   }

   $fechaInicial = $anio."-".$mes."-01";

   $fechaPivote  = date('Y-m-d',strtotime($fechaInicial));
   $numDias =      date('t', strtotime($fechaInicial));

   $iniPQ = $fechaPivote;
   $finPQ = date("Y-m-d",strtotime($fechaPivote."+ 14 days"));
   $iniSQ = date("Y-m-d",strtotime($fechaPivote."+ 15 days"));
   $finSQ = date("Y-m-d",strtotime($anio."-".$mes."-".$numDias));

   $primCom = comisionesEstetica($responsable,$idSucursal,$iniPQ,$finPQ);
   $segCom =  comisionesEstetica($responsable,$idSucursal,$iniSQ,$finSQ);
?>

<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<script type="text/javascript">
   function comisionEst(){
      document.form1.action = "comisionEstetica.php";
      document.form1.submit();
   }
</script>

<body onload="focoResponsable();">
<form name="form1" method="post" action="comisionEstetica.php">
   <div class="row col-md-12">
      <?php echo display_msg($msg); ?>
      <?php echo $meses[$mes]."/".$anio ?>
   </div>
   <div class="row col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-3">
                  <select class="form-control" name="responsable">
                     <option value="">Responsable</option>
                     <?php  foreach ($responsables as $id): ?>
                        <option value="<?php echo $id['username'] ?>">
                           <?php echo $id['name'] ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-3">
                  <select class="form-control" name="sucursal">
                     <option value="">Sucursal</option>
                     <?php  foreach ($all_sucursal as $id): ?>
                        <option value="<?php echo (int)$id['idSucursal'] ?>">
                           <?php echo $id['nom_sucursal'] ?>
                        </option>
                     <?php endforeach; ?>
                    </select>
               </div>
               <div class="col-md-2">
                  <select class="form-control" name="mes">
                     <option value="">Mes</option>
                     <?php foreach ($meses as $mesNum => $mesNom): ?>
                        <option value="<?php echo $mesNum ?>"><?php echo $mesNom ?></option>
                     <?php endforeach ?>
                  </select>
               </div>
               <div class="col-md-2">
                  <select class="form-control" name="anio">
                     <option value="">Anio</option>
                     <?php $i = (int)2020; while($i<=2040): ?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                     <?php $i++; endwhile; ?>
                  </select>
               </div>
               <a href="#" onclick="comisionEst();" class="btn btn-primary">Buscar</a>
               <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
               <?php if ($idSucursal != ""){ ?>
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
         <div class="panel-body">

            <?php if ($primCom): ?>
               <div class="form-control">
                  <strong><?php echo "Quincena del ".$iniPQ." al ".$finPQ?></strong><br>
               </div>
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 9%;">Responsable</th>
                        <th class="text-center" style="width: 7%;">Venta</th>
                        <th class="text-center" style="width: 7%;"> Comisión</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($primCom as $com1):?>
                     <tr>
                        <td><?php echo remove_junk($com1['vendedor']); ?></td>
                        <td class="text-right"><?php echo '$'.money_format('%.2n',$com1['venta']); ?></td>
                        <td class="text-right"> <?php echo '$'.money_format('%.2n',$com1['comision']); ?></td>
                     </tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
            <?php endif; ?>

            <?php if ($segCom): ?>
               <div class="form-control">
                  <strong><?php echo "Quincena del ".$iniSQ." al ".$finSQ?></strong><br>
               </div>
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 9%;">Responsable</th>
                        <th class="text-center" style="width: 7%;">Venta</th>
                        <th class="text-center" style="width: 7%;"> Comisión</th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($segCom as $com2):?>
                     <tr>
                        <td><?php echo remove_junk($com2['vendedor']); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$com2['venta']); ?></td>
                        <td class="text-right"> <?php echo money_format('%.2n',$com2['comision']); ?></td>
                     </tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
            <?php endif; ?>

         </div>
      </div>
   </div>
</form>
</body>
<?php include_once('../layouts/footer.php'); ?>