<?php
   require_once('../../modelo/load.php');
   $page_title = 'Actividades mensuales';

   // Checkin What level user has permission to view this page
   page_require_level(1);
 
   $mes = date('M');
   $month = date('m');
   $year = date('Y');
   $day = date("d", mktime(0,0,0, $month+1, 0, $year));
   $fechaFin = date("Y/m/d", strtotime($year.$month.$day));
   $fechaIni = date('Y/m/d', mktime(0,0,0, $month, 1, $year));

   $vacunas = buscaVacunasXResp($fechaIni,$fechaFin);
   $esteticas = buscaEsteticasXResp($fechaIni,$fechaFin);
   $consultas = buscaConsultasXResp($fechaIni,$fechaFin);
   $desparasitaciones = buscaDesparasitacionesXResp($fechaIni,$fechaFin);
?>
<?php include_once('../layouts/header.php'); ?>

<body>
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
                             <h3>Actividades del mes</h3>
                             <?php 
                                if ($mes == "Jan"){echo "Enero";}
                                if ($mes == "Feb"){echo "Febrero";}
                                if ($mes == "Mar"){echo "Marzo";}
                                if ($mes == "Apr"){echo "Abril";}
                                if ($mes == "May"){echo "Mayo";}
                                if ($mes == "Jun"){echo "Junio";}
                                if ($mes == "Jul"){echo "Julio";}
                                if ($mes == "Aug"){echo "Agosto";}
                                if ($mes == "Sep"){echo "Septiembre";}
                                if ($mes == "Oct"){echo "Octubre";}
                                if ($mes == "Nov"){echo "Noviembre";}
                                if ($mes == "Dec"){echo "Diciembre";}
                             ?>
                             </span>
                          </div>
                       </div>
                    </div>   
                 </div>   
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
                    <span>Vacunas</span>
                 </strong>
              </div>
              <div class="panel-body">
              <table class="table table-striped table-bordered table-condensed">
              <thead>
                 <tr>
                    <th>Responsable</th>
                    <th>Número</th>
                 </tr>
              </thead>
              <tbody>
              <?php foreach ($vacunas as $vacuna):?>
                 <tr>  
                    <td><?php echo remove_junk($vacuna['responsable']); ?></td>
                    <td><?php echo remove_junk($vacuna['numero']); ?></td>
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
                    <span>Estética</span>
                 </strong>
              </div>
              <div class="panel-body">
              <table class="table table-striped table-bordered table-condensed">
                 <thead>
                    <tr>
                       <th>Responsable</th>
                       <th>Número</th>
                    </tr>   
                 </thead>
                 <tbody>
                 <?php foreach ($esteticas as $estetica):?>
                    <tr>  
                       <td><?php echo remove_junk($estetica['responsable']); ?></td>
                       <td><?php echo remove_junk($estetica['numero']); ?></td>
                    </tr>
                 <?php endforeach; ?>
                 </tbody>
              </table>
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
                    <span>Consultas</span>
                 </strong>
              </div>
              <div class="panel-body">
              <table class="table table-striped table-bordered table-condensed">
                 <thead>
                    <tr>
                       <th>Responsable</th>
                       <th>Número</th>
                    </tr>
                 </thead>
                 <tbody>
                 <?php foreach ($consultas as $consulta):?>
                    <tr>   
                       <td><?php echo remove_junk($consulta['responsable']); ?></td>
                       <td><?php echo remove_junk($consulta['numero']); ?></td>
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
                    <span>Desparacitación</span>
                 </strong>
              </div>
              <div class="panel-body">
              <table class="table table-striped table-bordered table-condensed">
                 <thead>
                    <tr>
                       <th>Responsable</th>
                       <th>Número</th>
                    </tr>
                 </thead>
                 <tbody>
                 <?php foreach ($desparasitaciones as $desparasitacion):?>
                    <tr>      
                       <td><?php echo remove_junk($desparasitacion['responsable']); ?></td>
                       <td><?php echo remove_junk($desparasitacion['numero']); ?></td>
                    </tr>
                 <?php endforeach; ?>
                 </tbody>
              </table>
              </div>
           </div>
        </div>
     </div>        
  </form>
</body>
<?php include_once('../layouts/footer.php'); ?>
