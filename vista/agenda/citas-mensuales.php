<?php
   require_once('../../modelo/load.php');
   
   $page_title = 'Citas mensuales';
   page_require_level(3);

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

   $diasSemana = array('Lunes',
                       'Martes',
                       'Miércoles',
                       'Jueves',
                       'Viernes',
                       'Sábado',
                       'Domingo');
   
   $all_sucursal = find_all('sucursal');

   ini_set('date.timezone', 'America/Mexico_City');
   $fecha_actual = date('Y-m-d', time());
   $hora_actual =  date('H:i',   time());

   $vm_scu = (isset($_POST['sucursal'])) ? remove_junk($db->escape($_POST['sucursal'])):"";
   $anio =   (isset($_POST['anio']) && $_POST['anio'] != '') ? remove_junk($db->escape($_POST['anio'])):date('Y');
   $mes =    (isset($_POST['mes'])  && $_POST['mes'] != '')  ? remove_junk($db->escape($_POST['mes'])) :date('m');

   $primerDiaMes = date("N", strtotime("$anio-$mes-01"));
   $numDiasMes =   date('t', strtotime("$anio-$mes-01"));

   $fechaInicial = $anio . "-" . $mes . "-01";
   $numDias =      date('t', strtotime($fechaInicial));
   $fechaFinal =   $anio . "-" . $mes . "-" . $numDias;

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));
   $fechIni =  date('d-m-Y', strtotime($fechaInicial));
   $fechFin =  date('d-m-Y', strtotime($fechaFinal));

   if ($vm_scu != "") {
      $consulta =    buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
      $nomSucursal = $consulta['nom_sucursal'];
      $citas =       citasSucFecha($vm_scu,$fechaIni,$fechaFin);
   } else
      $citas = citasFecha($fechaIni,$fechaFin);
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script type="text/javascript">
   function agendarCitasCalendar(){
      if (window.confirm("¿Está seguro de agendar todas sus citas del mes de <?php 
                           if (isset($_POST['mes']) && $_POST['mes'] != '') echo $meses[$_POST['mes']];
                           else echo $meses[date('m')];
                        ?> a Google Calendar?")) {
         document.form1.action = 
            "agendarCitasCalendar.php?"+
            "anio=<?php echo (isset($_POST['anio']) && $_POST['anio'] !='') ? $_POST['anio']:date('Y') ?>"+
            "&mes=<?php echo (isset($_POST['mes']) && $_POST['mes'] != '') ? $_POST['mes']:date('m') ?>";
         document.form1.submit();
      } else return false;
   }
</script>
<!DOCTYPE html>
<html>
<head>
   <title> Citas Mensuales </title>
   <link rel="stylesheet" href="../../libs/css/main.css" />
</head>
<body onload="focoSucursal();">
   <form name="form1" method="post" action="citas-mensuales.php">
      <div class="row col-md-12">
         <?php echo display_msg($msg); ?>
      </div>

      <span>Período:</span>
      <?php echo "del $fechIni al $fechFin"; ?>
      <?php echo ($vm_scu != "")? '<span>&nbsp;&nbsp;&nbsp;&nbsp;</span><span>Sucursal:</span>'.$nomSucursal:'';?>

      <div class="row col-md-12">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div class="form-group">
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
                        <?php while(key($meses)): ?>
                           <option value="<?php echo key($meses); ?>">
                              <?php echo remove_junk($meses[key($meses)]); ?>
                           </option>
                        <?php next($meses); endwhile;?>
                     </select>
                  </div>  
                  <div class="col-md-2">
                     <select class="form-control" name="anio">
                        <option value="">Año</option>
                        <?php $i = (int)2020; while ($i <= 2040):?>
                           <option value="<?php echo $i ?>"> <?php echo $i ?> </option>
                        <?php $i += 1; endwhile;  ?>
                     </select>
                  </div>
                  <a href="#" onclick="citasMens();" class="btn btn-primary">Buscar</a>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
               </div>
            </div>
            <div class="panel-body">
               <div class="form-group col-md-12">
               <center>
                  <?php if (!$cliente->isAccessTokenExpired()) {  ?>
                     <a href="#" onclick="return agendarCitasCalendar();" class="google-sync-button">
                        Haga clic aquí para agendar todas las citas del mes de 
                        <?php 
                           if (isset($_POST['mes']) && $_POST['mes'] != '') echo $meses[$_POST['mes']];
                           else echo $meses[date('m')];
                        ?>
                        que no estén sincronizadas con Google Calendar
                        <img src="../../libs/imagenes/Calendar.png" class="google-logo">
                     </a>
                  
                  <?php } else { ?>
                     <p>No ha iniciado sesión con Google<img src="../../libs/imagenes/Google.png" class="google-logo">. Recuerde que si no inicia sesión con Google, los cambios realizados que haga en sus citas <b>No se verán reflejados</b> en su cuenta Google Calendar<img src="../../libs/imagenes/Calendar.png" class="google-logo">.</p>
                  <?php } ?>
               </center>
               </div>
               <br>
               <br>
               <div class="container">
                  <div class="calendar">
                     <div class="headerCalendar">
                        <div class="month"> 
                           <?php 
                              if (isset($_POST['mes']) && $_POST['mes'] != '') echo $meses[$_POST['mes']];
                              else echo $meses[date('m')];
                              if (!$cliente->isAccessTokenExpired()) echo "<img src='../../libs/imagenes/Google.png' class='google-logo'><img src='../../libs/imagenes/Calendar.png' class='google-logo'>"
                           ?>
                        </div>
                        <div class="year">
                           <?php 
                              if (isset($_POST['anio']) && $_POST['anio'] != '') echo $_POST['anio'];
                              else echo date('Y');
                           ?>
                        </div>
                     </div>
                     <div class="weekdays">
                        <?php foreach ($diasSemana as $dia) echo "<div class='day'>".$dia."</div>"; ?>
                     </div>
                     <div class="days"> 
                        <?php
                           $primerDiaMes -= 1;
                           for ($i = 0; $i < $primerDiaMes; $i++)
                              echo '<div class="day"></div>';

                           for ($dia = 1; $dia <= $numDiasMes; $dia++) {
                              $citaEnDia = false;
                              $date =      $anio.'/'.$mes.'/'.$dia;
                              $img =       '';

                              foreach ($citas as $cita) {
                                 $citaDia = date('j', strtotime($cita['fecha_cita']));

                                 if ($citaDia == $dia) {
                                    $citaEnDia = true;
                                    if ($cita['idEvent'] != null && !$cliente->isAccessTokenExpired())
                                       $img = '<br><img src="../../libs/imagenes/Calendar.png" class="google-logo">';
                                    break;
                                 }
                              }  
                              $extraClass = $citaEnDia ? ' highlight' : '';
                              echo '<a href="citas-diarias.php?date='.$date.'" class="day'.$extraClass.'">'.$dia.$img.'</a>';
                            }
                        ?>
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