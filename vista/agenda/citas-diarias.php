<?php
   require_once('../../modelo/load.php');
   $page_title = 'Consulta de citas por día';  
   page_require_level(3);

   ini_set ('date.timezone', 'America/Mexico_City');
   $fecha_actual = date('Y-m-d', time());
   $hora_actual =  date('H:i', time());

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
   $vm_scu = "";
  
   if(isset($_POST['sucursal']))
      $vm_scu =   remove_junk($db->escape($_POST['sucursal']));

   if(isset($_GET['date'])) {
      $fechaInicial = $_GET['date'];
      $fechaFinal   = $_GET['date'];
   } else {
      $anio = (isset($_POST['anio']) && $_POST['anio'] != '') ? $_POST['anio'] : date('Y');
      $mes =  (isset($_POST['mes'])  && $_POST['mes']  != '') ? $_POST['mes']  : date('m');
      $dia =  (isset($_POST['dia'])  && $_POST['dia']  != '') ? $_POST['dia']  : date('d');

      $fechaInicial = $anio.'/'.$mes."/".$dia;
      $fechaFinal   = $anio.'/'.$mes."/".$dia;  
   }

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));
   $fechIni =  date('d-m-Y', strtotime($fechaInicial));

   if ($vm_scu!=""){
      $nomSucursal = $sucursal['nom_sucursal'];
      $sucursal =    buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
      $citas =       citasSucFecha($vm_scu,$fechaIni,$fechaFin);
   } else 
      $citas =       citasFecha($fechaIni,$fechaFin);
?>
<?php include_once('../layouts/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
   <title>Citas por día</title>
</head>

<body onload="foco();diasMes();">
   <form name="form1" method="post" action="citas-diarias.php">
      <div class="row col-md-12">
         <?php echo display_msg($msg); ?>
      </div>
      <span>Citas del día:</span>
      <?php echo "$fechIni"; ?>
      <?php if ($vm_scu!="") { ?>
         <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
         <span>Sucursal: <?php echo $nomSucursal; ?></span>
      <?php } ?>
      <div class="row col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div class="form-group">
                  <div class="col-md-3">
                     <select class="form-control" name="sucursal">
                        <option value="">Sucursal</option>
                        <?php  foreach ($all_sucursal as $id): ?>
                           <option value="<?php echo (int)$id['idSucursal'] ?>"><?php echo $id['nom_sucursal'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>  
                  <div class="col-md-2">
                     <select class="form-control" name="anio" onchange="diasMes();">
                        <option value="">Año</option>
                        <?php $i = (int)2020; while($i <= 2040):?>
                           <option value="<?php echo $i ?>"> <?php echo $i ?></option>
                        <?php $i += 1; endwhile; ?>
                     </select>
                  </div>  
                  <div class="col-md-2">
                     <select class="form-control" name="mes" onchange="diasMes();">
                        <option value="">Mes</option>
                        <?php while(key($meses)): ?>
                           <option value="<?php echo key($meses); ?>"> <?php echo remove_junk($meses[key($meses)]); ?> </option>
                        <?php next($meses); endwhile;?>
                     </select>
                  </div>
                  <div class="col-md-2">
                     <select class="form-control" name="dia">
                        <option value="">Día</option>
                     </select>                
                  </div>
                  <a href="#" onclick="citasDiarias();" class="btn btn-primary">Buscar</a>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
               </div>
            </div>
            <div class="panel-body">
               <div class="form-group col-md-12">
                  <?php if ($cliente->isAccessTokenExpired()):?>
                     <p>No ha iniciado sesión con Google<img src="../../libs/imagenes/Google.png" class="google-logo">. Recuerde que si no inicia sesión con Google, los cambios realizados que haga en sus citas <b>No se verán reflejados</b> en su cuenta Google Calendar<img src="../../libs/imagenes/Calendar.png" class="google-logo">.</p>
                  <?php endif;?>
               </div>
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 10%;"> Mascota</th>
                        <th class="text-center" style="width: 30%;"> Responsable</th>
                        <th class="text-center" style="width: 9%;">  Fecha cita </th>
                        <th class="text-center" style="width: 7%;">  Hora </th>
                        <th class="text-center" style="width: 44%;"> Nota </th>
                        <?php if (!$cliente->isAccessTokenExpired()): ?>
                           <th class="text-center"> Sincronizado<br><img src="../../libs/imagenes/Google.png" class="google-logo"><img src="../../libs/imagenes/Calendar.png" class="google-logo"></th>
                        <?php endif ?>
                        <th class="text-center" style="width: 44%;"> Acciones </th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($citas as $cita):?>
                     <tr>
                        <td><?php echo remove_junk($cita['nombre']); ?></td>
                        <td><?php echo remove_junk($cita['responsable']); ?></td>
                        <td class="text-center"><?php echo date("d-m-Y", strtotime ($cita['fecha_cita'])); ?></td>
                        <td class="text-center"><?php echo date("H:i", strtotime ($cita['hora'])); ?></td>
                        <td><textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($cita['nota']); ?></textarea></td>
                        <?php if (!$cliente->isAccessTokenExpired()): ?>
                           <td class="text-center"><?php echo $cita['idEvent']!=null ? 'Sincronizado':'No Sincronizado'?></td>
                        <?php endif ?>
                        <td class="text-center">
                           <div class="btn-group">
                              <?php if ((($fecha_actual == date("Y-m-d", strtotime ($cita['fecha_cita']))) && ($hora_actual < date("H:i", strtotime ($cita['hora'])))) || ($fecha_actual < date("Y-m-d", strtotime ($cita['fecha_cita'])))){ ?>
                                 <a href="editarCita.php?id=<?php echo (int)$cita['id'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                                    <span class="glyphicon glyphicon-edit"></span>
                                 </a>
                                 <a href="deleteCita.php?id=<?php echo (int)$cita['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                                    <span class="glyphicon glyphicon-trash"></span>
                                 </a>
                              <?php } ?>
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>         
         </div>   
      </div>
   </form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>