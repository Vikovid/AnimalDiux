<?php
   require_once('../../modelo/load.php');
   $page_title = 'Actividades quincenales';
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $fechaInicio = date("Y/m/01");
   
   if (date("j") < 16){
      $fechaFinPrimerQuin = strtotime('+14 day',strtotime($fechaInicio)); 
      $fechaFinal = date ('Y/m/d',$fechaFinPrimerQuin);
      $fechaIni = strtotime ( $fechaInicio );
      $fechaIni = date ('d/m/Y',$fechaIni);
      $fechaFin = date ('d/m/Y',$fechaFinPrimerQuin);
   }else{
      $fechaIniSegundaQuin = strtotime('+15 day',strtotime($fechaInicio)); 
      $fechaInicio = date ('Y/m/d',$fechaIniSegundaQuin);
      $fechaIni = date ('d/m/Y',$fechaIniSegundaQuin);
      $fechaFinal = date("Y/m/t");
      $fechaFin = strtotime ($fechaFinal);
      $fechaFin = date ('d/m/Y',$fechaFin);
   }
   $vacunas = buscaVacunasXResp($fechaInicio,$fechaFinal);
   $esteticas = buscaEsteticasXResp($fechaInicio,$fechaFinal);
   $consultas = buscaConsultasXResp($fechaInicio,$fechaFinal);
   $desparasitaciones = buscaDesparasitacionesXResp($fechaInicio,$fechaFinal);
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
                                <h3> Actividades de la quincena </h3>
                                <?php echo "Quincena del: $fechaIni al: $fechaFin"; ?>
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
                    <span>Desparasitación</span>
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
