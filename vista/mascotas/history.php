<?php
   $page_title = 'Información De La Mascota';
   require_once('../../modelo/load.php');

   // Checkin What level user has permission to view this page
   page_require_level(3);

   // if (isset($_POST['idMascotas']) && $_POST['idMascotas'] != '')
   //    $_SESSION['idMascotasPOST'] = $_POST['idMascotas'];
   // $idmas = $_SESSION['idMascotasPOST'];

   if (isset($_POST['idMascotas']) && !empty($_POST['idMascotas']))
      $idmas = $_POST['idMascotas'];
   elseif (isset($_GET['idMascotas']))
      $idmas = $_GET['idMascotas'];
   else
      $idmas = '';

   $idCredencial = "";
   $dir_cliente =  "";
   $tel_cliente =  "";
   $nom_cliente =  "";
   $idMascotas =   "";
   $alimento =     "";
   $especie =      "";
   $estatus =      "";
   $nombre =       "";
   $estado =       "";
   $correo =       "";
   $color =        "";
   $name =         "";
   $raza =         "";
   $peso =         "";
   $sexo =         "";
   $edad =         "";
   $foto =         "";

   $desparasitaciones = array();
   $consultas =         array();
   $esteticas =         array();
   $estudios =          array();
   $vacunas =           array();

   if ($idmas != ""){
      $consMascota = buscaEstadoNombMasc($idmas);
                
      if ($consMascota != NULL){
         $estatus = $consMascota['estatus'];
         $name =    $consMascota['nombre'];
      }

      $consMascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);

      $idMascotas = $consMascota['idMascotas'];
      $alimento =   $consMascota['alimento'];
      $especie =    $consMascota['especie'];
      $nombre =     $consMascota['nombre'];
      $estado =     $consMascota['estado'];
      $color =      $consMascota['Color'];
      $raza =       $consMascota['raza'];
      $peso =       $consMascota['peso'];
      $sexo =       $consMascota['sexo'];
      $edad =       $consMascota['fecha_nacimiento'];
      $foto =       $consMascota['foto'];

      $consCliente = buscaClienteMascota($idmas);

      $idCredencial = $consCliente['idcredencial'];
      $nom_cliente =  $consCliente['nom_cliente'];
      $dir_cliente =  $consCliente['dir_cliente'];
      $tel_cliente =  $consCliente['tel_cliente'];
      $correo =       $consCliente['correo'];

      $desparasitaciones = buscaDesparasitaciones($idmas);
      $consultas =         buscaConsultas($idmas);
      $esteticas =         buscaEsteticas($idmas);
      $estudios =          buscaEstudios($idmas);
      $vacunas =           buscaVacunas($idmas);
   }
?>

<?php include_once('../layouts/header.php');?>

<script type="text/javascript">
   if (window.history.replaceState) { // verificamos disponibilidad
      window.history.replaceState(null, null, window.location.href);
   }

   function preventBack () { window.history.forward(); }

   setTimeout("preventBack()", 0);

   window.onunload = function(){null};

   function mascota(){
      if (document.form1.idMascotas.value == ""){
         alert("Recargaste la página");
         document.form1.action = "clinica.php";
         document.form1.submit();
      }
   }
   function openModelPDF(url) {
      $('#modalPdf').modal('show');
      $('#iframePDF').attr('src','<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/Balam/'; ?>'+url);
   }
</script>

<!DOCTYPE html>
<html>
<head>

<style type="text/css">
   .btn { width: 160px; }
   .content {
      display: flex;
      flex-wrap: nowrap;
   }
   .content .element {
      flex: 1;
      margin-right: 10px;
   }
   .content .element:last-child {
      margin-right: 0;
   }
   td {
      text-align: center;
      width: 15%;
   }
   .edit {
      width: 20px;
      height: 20px;
   }
   .archivo {
      width: 100px;
   }
   .view {
      width: 35px;
      height: 35px;
   }
   textarea {
      white-space: normal;
   }
</style>

<title>Historial Clínico</title>

</head>

<body onload="mascota();">
<form name="form1" method="post" action="history.php">
   <div class="row col-md-12">
      <?php echo display_msg($msg); ?>
   </div>

   <div class="row col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group" style="display: inline;">
               <div class="col-md-2">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <h5><strong>Información de: <?php echo remove_junk($nombre); ?></strong></h5>
                     </span>
                  </div>
               </div>
               <div class="text-right">
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Estatus: <?php echo remove_junk($estatus); ?></span>
               </div>
            </div>
            <div class="row form-group" style="display: inline;"></div>
            <div class="row form-group" style="display: inline;">
               <a href="consulta.php?idMas=<?php echo $idmas;?>" class="btn btn-primary btn-success">Consulta</a>
               <a href="revision.php?idMas=<?php echo $idmas;?>" class="btn btn-warning btn-success">Revisión</a>
               <a href="estetica.php?idMas=<?php echo $idmas;?>" class="btn btn-primary">Estética</a>
               <a href="vacuna.php?idMas=<?php echo $idmas;?>" class="btn btn-primary">Vacuna</a>
               <a href="add_estancia.php?idMas=<?php echo $idmas;?>" class="btn btn-warning">Estancia</a>
               <a href="desparasitacion.php?idMas=<?php echo $idmas;?>" class="btn btn-info btn-s">desparasitación</a>
               <a href="aplicacion.php?idMas=<?php echo $idmas;?>" class="btn btn-info btn-s">aplicación</a>
               <a href="estudio.php?idMas=<?php echo $idmas;?>" class="btn btn-warning">estudio</a>
               <!-- <a href="cita.php?idMas=<?php echo $idmas;?>" class="btn btn-primary btn-success">cita</a> -->
               <a href="../pdf/historialpdf.php?idMas=<?php echo $idmas;?>" class="btn btn-danger btn-s ">PDF</a>
               <input type="hidden" name="idMascotas" value="<?php echo $idmas;?>">
            </div>
         </div>
         <div class="panel-body">
            <div class="content">
               <div class="element" style="width: 30%;">
                  <table class="table table-bordered">
                     <h4 class="text-center" style="height: 40px;">
                        <strong>Información de la Mascota</strong>
                     </h4>
                     <tr>
                        <td><strong>Nombre:</strong></td>
                        <td><?php echo remove_junk($nombre); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Especie:</strong></td>
                        <td><?php echo remove_junk($especie); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Raza:</strong></td>
                        <td><?php echo remove_junk($raza); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Color:</strong></td>
                        <td><?php echo remove_junk($color); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Peso:</strong></td>
                        <td><?php echo $peso; ?></td>
                     </tr>
                     <tr>
                        <td><strong>Alimento:</strong></td>
                        <td><?php echo remove_junk($alimento); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Sexo:</strong></td>
                        <td><?php echo utf8_encode($sexo); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Estado:</strong></td>
                        <td><?php echo remove_junk($estado); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Fecha de nacimiento:</strong></td>
                        <td><?php echo $edad;?></td>
                     </tr>
                  </table>
               </div>
               <div class="element" style="width: 30%;">   
                  <table class="table table-bordered">
                     <h4 class="text-center" style="height: 40px;">
                        <strong>Información del cliente</strong>
                     </h4>
                     <tr>
                        <td><strong>Id Cliente:</strong></td>
                        <td><?php echo $idCredencial; ?></td>
                     </tr>
                     <tr>
                        <td><strong>Cliente:</strong></td>
                        <td><?php echo remove_junk($nom_cliente); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Dirección:</strong></td>
                        <td><?php echo remove_junk($dir_cliente); ?></td>
                     </tr>
                     <tr>
                        <td><strong>Teléfono:</strong></td>
                        <td><?php echo $tel_cliente; ?></td>
                     </tr>
                     <tr>
                        <td><strong>Correo:</strong></td>
                        <td><?php echo $correo; ?></td>
                     </tr>
                  </table>
               </div>
               <div class="element" style="width: 30%;">
                  <div style="height: 40px;"></div>
                  <?php if ($foto != ""){ 
                     echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='data:image/jpg; base64,".base64_encode($foto)."' width='150' height='250'>";
                  } ?>
               </div>
            </div>
            <div class="content">
            <?php 
               function calcular_edad($fecha){
                  $fecha_nac = new DateTime(date('Y/m/d',strtotime($fecha)));
                  $fecha_hoy = new DateTime(date('Y/m/d',time()));
                  $edad =      date_diff($fecha_hoy,$fecha_nac);
                  return $edad;
               }
            ?>
            <?php
               if ($edad != "0000-00-00" && $edad != "") $edad = calcular_edad($edad);
               if ($edad != "0000-00-00" && $edad != ""){
            ?>
               <h4>
                  <?php echo "$nombre "; echo "Tiene {$edad->format('%Y')} años, {$edad->format('%m')} meses y {$edad->format('%d')} dias."; ?>
               </h4>
            <?php } ?>
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-12 row">
      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <strong>
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Vacuna</span>
               </strong>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  <tr>
                     <th> Fecha   </th>
                     <th> Vacunas </th>
                     <th> Nota    </th>
                     <th> Acción  </th>
                  </tr>
                  <?php foreach ($vacunas as $vacuna):?>
                  <tr>
                     <td><?php echo $vacuna['fecha']; ?></td>
                     <td><?php echo remove_junk($vacuna['vacuna']); ?></td>
                     <td>
                        <textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($vacuna['nota']); ?></textarea>
                     </td>
                     <td>
                        <a href="edit_vacuna.php?idvacuna=<?php echo (int)$vacuna['idvacuna'];?>&idMas=<?php echo $idmas;?>" class=" edit btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-edit"></span>
                        </a>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </table>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <strong>
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Estética</span>
               </strong>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th>Fecha</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($esteticas as $estetica):?>
                     <tr>
                        <td><?php echo $estetica['fecha']; ?></td>
                        <td>
                           <textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($estetica['observaciones']); ?></textarea>
                        </td>
                        <td class="text-center">
                           <div class="btn-group">
                              <a href="edit_estetica.php?idestetica=<?php echo (int)$estetica['idestetica'];?>&idMas=<?php echo $idmas;?>" class="edit btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                              <span class="glyphicon glyphicon-edit"></span>
                              </a>
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-12 row">
      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading">
               <strong>
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Estudios clínicos</span>
               </strong>
            </div>
            <div class="panel-body">
               <table class="table table-bordered table-striped">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 10%;"> Nombre      </th>
                        <th class="text-center" style="width: 30%;"> Descripción </th>
                        <th class="text-center" style="width: 8%;">  Fecha       </th>
                        <th class="text-center" style="width: 5%;">  Acción      </th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($estudios as $estudio):?>
                     <tr>
                        <td class="text-justify"><?php echo remove_junk($estudio['nombre']) ?></td>
                        <td class="text-justify"><?php echo remove_junk($estudio['descripcion']) ?></td>
                        <td class="text-justify"><?php echo $estudio['fecha']; ?></td>
                        <td class="text-center">
                           <a class="archivo btn btn-primary" target="_black" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/AnimalDiux/' . $estudio['url']; ?>" >Ver Archivo</a>
                        </td>
                     </tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="modal fade" id="modalPdf" tabindex="-1" aria-labelledby="modalPdf" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Ver archivo</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <iframe id="iframePDF" frameborder="0" scrolling="no" width="100%" height="500px"></iframe>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <strong>
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Desparasitación</span>
               </strong>
            </div>
            <div class="panel-body">
               <table class="table table-bordered table-striped">
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 10%;"> Fecha </th>
                        <th class="text-center" style="width: 30%;"> Desparasitante </th>
                        <th class="text-center" style="width: 35%;"> Nota </th>
                     </tr>          
                  </thead>
                  <tbody>
                  <?php foreach ($desparasitaciones as $desparasitacion):?>
                     <tr>
                        <td class="text-justify" > <?php echo date("d-m-Y", strtotime ($desparasitacion['fecha'])); ?></td>
                        <td class="text-justify" > <?php echo remove_junk($desparasitacion['desparasitante']); ?></td>
                        <td><textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($desparasitacion['nota']); ?></textarea></td>
                     </tr>
                  <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Historial Clínico</span>
            </strong>
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th class="text-center" style="width: 1%;" > #</th>
                     <th class="text-center" style="width: 55%;"> Historia Clínica </th>
                     <th class="text-center" style="width: 15%;"> Fecha </th>
                     <th class="text-center" style="width: 5%;" > Acciones </th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($consultas as $consulta):?>
                  <tr>
                     <td class="text-center"><?php echo count_id();?></td>
                     <td>
                     <center>
                        <textarea class="form-control" maxlength="4000" rows="5" style="resize: none;" readonly>
                           <?php echo remove_junk($consulta['problema']); ?>
                        </textarea>
                     </center>
                     </td>
                     <td class="text-center" style="width: 05%;"> <?php echo $consulta['fecha']; ?></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="ver_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="view btn btn-primary btn-md" title="Consultar" data-toggle="tooltip">
                              <span class="glyphicon glyphicon-eye-open"></span>
                           </a>
                           <a href="edit_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="view btn btn-info btn-md" title="Editar" data-toggle="tooltip">
                              <span class="glyphicon glyphicon-edit"></span>
                           </a>
                           <a href="delete_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="view btn btn-danger btn-md" title="Eliminar" data-toggle="tooltip">
                              <span class="glyphicon glyphicon-trash"></span>
                           </a>
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