<?php
   $page_title = 'Información De La Mascota';
   require_once('../../modelo/load.php');
   page_require_level(2);

   $_SESSION['idmas'] = isset($_POST['idMascotas']) ? $_POST['idMascotas']:'';
   $idmas = $_SESSION['idmas'];

   $name = 			"";
   $estatus = 		"";
   $nombre = 		"";
   $especie = 		"";
   $raza = 			"";
   $color = 		"";
   $peso = 			"";
   $alimento = 	"";
   $sexo = 			"";
   $estado = 		"";
   $edad = 			"";
   $nom_cliente = "";
   $idMascotas = 	"";
   $foto = 			"";
   $idCredencial ="";
   $dir_cliente = "";
   $tel_cliente = "";
   $correo = 		"";

   $desparasitaciones = array();
   $consultas = 	array();
   $vacunas = 		array();
   $esteticas = 	array();
   $vacunas = 		array();
   $estudios = 	array();

   if ($idmas != ""){
      $consMascota = buscaEstadoNombMasc($idmas);
                
      if ($consMascota != NULL){
         $estatus =  $consMascota['estatus'];
         $name =     $consMascota['nombre'];
      }

      $consMascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idmas);

      $nombre = 		$consMascota['nombre'];
      $especie = 		$consMascota['especie'];
      $raza = 			$consMascota['raza'];
      $color = 		$consMascota['Color'];
      $peso = 			$consMascota['peso'];
      $alimento = 	$consMascota['alimento'];
      $sexo = 			$consMascota['sexo'];
      $estado = 		$consMascota['estado'];
      $edad = 			$consMascota['fecha_nacimiento'];
      $idMascotas = 	$consMascota['idMascotas'];
      $foto = 			$consMascota['foto'];

      $consCliente = buscaClienteMascota($idmas);

      $idCredencial = 	$consCliente['idcredencial'];
      $nom_cliente = 	$consCliente['nom_cliente'];
      $dir_cliente = 	$consCliente['dir_cliente'];
      $tel_cliente = 	$consCliente['tel_cliente'];
      $correo = 			$consCliente['correo'];

      $consultas = 			buscaConsultas($idmas);
      $esteticas = 			buscaEsteticas($idmas);
      $vacunas = 				buscaVacunas($idmas);
      $desparasitaciones = buscaDesparasitaciones($idmas);
      $estudios = 			buscaEstudios($idmas);
   }
?>
<script type="text/javascript">
	if (window.history.replaceState) { // verificamos disponibilidad
	   window.history.replaceState(null, null, window.location.href);
	}
	/*history.pushState(null, null, location.href);
	history.back();
	history.forward();
	window.onpopstate = function () { history.go(1); };*/
	function preventBack(){window.history.forward();}

	setTimeout("preventBack()", 0);

	window.onunload=function(){null};

	function mascota() {
	  	if (document.form1.idMascotas.value == "") {
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
<?php include_once('../layouts/header.php');?>

<!DOCTYPE html>
<html>
<head>
<title>Historial Clínico</title>
</head>

<body onload="mascota();">
<form name="form1" method="post" action="history.php">  <div class="row">
<div class="col-md-12">
   <?php echo display_msg($msg); ?>
</div>
<div class="col-md-12">
   <div class="panel panel-default">
      <div class="panel-heading clearfix">
         <div class="pull-right">
            <div class="form-group">
               <div class="col-md-2">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <h5>Información de la mascota</h5>
                     </span>
                  </div>    
               </div>
               <div class="text-right">
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Estatus de <?php echo $name ?> : <?php echo $estatus ?></span>
                  <div>
                     <!-- En el href se manda el valor del parámetro en este caso, envia-->
                     <a href="../pdf/historialpdf.php?idMas=<?php echo $idmas;?>" class="btn btn-danger btn-sm ">PDF</a>  
                     <a href="consulta.php?idMas=<?php echo $idmas;?>" class="btn btn-primary btn-success">Consulta</a> 
                     <a href="revision.php?idMas=<?php echo $idmas;?>" class="btn btn-warning btn-success">Revisión</a> 
                     <a href="estetica.php?idMas=<?php echo $idmas;?>" class="btn btn-primary">Estética</a>  
                     <a href="vacuna.php?idMas=<?php echo $idmas;?>" class="btn btn-primary">Vacuna</a>  
                     <a href="add_estancia.php?idMas=<?php echo $idmas;?>" class="btn btn-warning">Estancia</a>
                     <a href="desparasitacion.php?idMas=<?php echo $idmas;?>" class="btn btn-info btn-s">desparasitación</a> 
                     <a href="aplicacion.php?idMas=<?php echo $idmas;?>" class="btn btn-info btn-s">aplicación</a> 
                     <a href="estudio.php?idMas=<?php echo $idmas;?>" class="btn btn-warning">estudio</a> 
                     <a href="cita.php?idMas=<?php echo $idmas;?>" class="btn btn-primary btn-success">cita</a>
                     <input type="hidden" name="idMascotas" value="<?php echo $idmas;?>">
                  </div>
               </div>   
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 30%;">
  		      <table class="table table-bordered">
  			    <span>
               <h4 class="text-center"><text style="font-weight:bold;">Información de la Mascota</text></h4>
            </span>
            <thead>
               <tr>
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Nombre:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$nombre"; ?></td>    
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Especie:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$especie"; ?></td>             
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Raza:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$raza"; ?></td>             
               </tr> 
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Color:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$color"; ?></td>             
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Peso:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$peso"; ?></td>             
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Alimento:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$alimento"; ?></td>
              </tr>
              <tr>     
                <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Sexo:</text></td>
                <td class="text-center" style="width: 10%;"><?php echo "$sexo"; ?></td>             
              </tr>
              <tr>     
                <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Estado:</text></td>
                <td class="text-center" style="width: 10%;"><?php echo "$estado"; ?></td>             
              </tr>
              <tr>     
                <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Fecha de nacimiento:</text></td>
                <td class="text-center" style="width: 10%;"><?php echo date("d-m-Y", strtotime ($edad));?></td>
              </tr>
            </thead>
            </table>
         </div>    
         <div style="float:left;width: 30%;">
            <table class="table table-bordered">
  		      <span>
              <h4 class="text-center">
                <text style="font-weight:bold;">Información del cliente</text>
              </h4>
            </span>
            <thead>
       		     <tr>
                  <td class="text-center" style="width: 10%:bold;"><text style="font-weight:bold;">Id Cliente:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$idCredencial"; ?></td>    
               </tr>
       		     <tr>
                  <td class="text-center" style="width: 10%:bold;"><text style="font-weight:bold;">Cliente:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$nom_cliente"; ?></td>    
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Dirección:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$dir_cliente"; ?></td>
               </tr> 
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Teléfono:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$tel_cliente"; ?></td>
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Correo:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$correo"; ?></td>             
               </tr>
            </thead>
            </table>   
         </div>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         <!--img src='imgMascotas.php?id=<?php echo $idmas; ?>' width="250" height="350"-->
         <?php if ($foto != ""){ 
            echo "<img src='data:image/jpg; base64,".base64_encode($foto)."' width='250' height='350'>";
         } ?> 
      </div>
      <div class="panel-body">
         <div>

<?php function calcular_edad($fecha){

$fecha_nac = new DateTime(date('Y/m/d',strtotime($fecha))); // Creo un objeto DateTime de la fecha ingresada
$fecha_hoy =  new DateTime(date('Y/m/d',time())); // Creo un objeto DateTime de la fecha de hoy
$edad = date_diff($fecha_hoy,$fecha_nac); // La funcion ayuda a calcular la diferencia, esto seria un objeto
return $edad;
}

if ($edad != "0000-00-00" && $edad != ""){
   $edad = calcular_edad($edad);
}

if ($edad != "0000-00-00" && $edad != ""){?>
   <span>
      <h4>
         <?php echo "$nombre "; echo "Tiene {$edad->format('%Y')} años, {$edad->format('%m')} meses y {$edad->format('%d')} dias."; ?>
      </h4>
   </span>
<?php } ?>
         </div>
      </div>
<!--------la parte de vacunas-->
      <div class="col-md-6">
	       <div class="panel panel-default">
	    	    <div class="panel-heading">
               <strong>
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Vacuna</span>
               </strong>
            </div>
            <div class="panel-body">
               <div style="float:left;width: 100%;">
               <table class="table table-bordered table-striped">
               <thead> 
               <tbody>
                  <tr>
                     <th class="text-center" style="width: 10%;"> Fecha </th>
                     <th class="text-center" style="width: 20%;"> Vacunas </th>
                     <th class="text-center" style="width: 35%;"> Nota </th>
                     <th class="text-center" style="width: 5%;"> Acción </th>
                  </tr>
               </tbody>
               </thead>
               <tbody>
               <?php foreach ($vacunas as $vacuna):?>
                  <tr>
                     <td class="text-justify"><?php echo date("d-m-Y",strtotime ($vacuna['fecha'])); ?></td>
                     <td class="text-justify"><?php echo remove_junk($vacuna['vacuna']); ?></td>
                     <td><textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($vacuna['nota']); ?></textarea></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <a href="edit_vacuna.php?idvacuna=<?php echo (int)$vacuna['idvacuna'];?>&idMas=<?php echo $idmas;?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
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
      </tbody>
   </table>
</div>
<!--------la parte de estetica-->
<div class="col-md-6">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Estética</span>
         </strong>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 100%;">
         <table class="table table-bordered table-striped">
            <thead>
            <tbody>
               <tr>
                  <th class="text-center" style="width: 10%;">Fecha</th>
                  <th class="text-center" style="width: 50%;">Observaciones</th>
                  <th class="text-center" style="width: 5%;">Acción</th>
               </tr>
            </tbody>
            </thead>
            <tbody>
               <?php foreach ($esteticas as $estetica):?>
               <tr>
                  <td class="text-justify"><?php echo date("d-m-Y", strtotime ($estetica['fecha'])); ?></td>
                  <td><textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($estetica['observaciones']); ?></textarea></td>
                  <td class="text-center">
                     <div class="btn-group">
                        <a href="edit_estetica.php?idestetica=<?php echo (int)$estetica['idestetica'];?>&idMas=<?php echo $idmas;?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
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
<!--parte de estudio clinico-->
<div class="col-md-6">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Estudios clínicos</span>
         </strong>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 100%;">
            <table class="table table-bordered table-striped">
               <thead>
               <tbody>
                  <tr>
                     <th class="text-center" style="width: 10%;">Nombre</th>
                     <th class="text-center" style="width: 30%;">Descripción</th>
                     <th class="text-center" style="width: 8%;">Fecha</th>
                     <th class="text-center" style="width: 5%;">Acción</th>
                  </tr>
               </tbody>
               </thead>
               <tbody>
               <?php foreach ($estudios as $estudio):?>
                  <tr>
                     <td class="text-justify"><?php echo $estudio['nombre'] ?></td>
                     <td class="text-justify"><?php echo $estudio['descripcion'] ?></td>
                     <td class="text-justify"><?php echo date("d-m-Y", strtotime ($estudio['fecha'])); ?></td>
                     <td class="text-center">
                       <!--button onclick="openModelPDF('<?php //echo $val['url'] ?>')" class="btn btn-primary" type="button">Ver Archivo Modal</button-->
                          <a class="btn btn-primary" target="_black" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/AnimalDiux/' . $estudio['url']; ?>" >Ver Archivo</a>
                     </td>
                  </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
         </div>
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
<!--parte de desparasitación-->
<div class="col-md-6">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Desparasitación</span>
         </strong>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 100%;">
            <table class="table table-bordered table-striped">
            <thead>
            <tbody>
               <tr>
                  <th class="text-center" style="width: 10%;"> Fecha </th>
                  <th class="text-center" style="width: 30%;"> Desparasitante </th>
                  <th class="text-center" style="width: 35%;"> Nota </th>
               </tr>
            </tbody>            
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
         <div style="float:left;width: 100%;">
         <table class="table table-bordered table-striped">
            <thead>
            <tbody>              
               <tr>
                  <th class="text-center" style="width: 3%;">#</th>
                  <th class="text-center" style="width: 70%;"> Diagnóstico </th>
                  <th class="text-center" style="width: 10%;"> Fecha </th>
                  <th class="text-center" style="width: 5%;"> Acciones </th>
               </tr>
            </tbody>
            </thead>
            <tbody>
               <?php foreach ($consultas as $consulta):?>
               <tr>
                  <td class="text-center" ><?php echo count_id();?></td>
                  <td class="text-justify" > <?php echo remove_junk($consulta['diagnostico']); ?></td>
                  <td class="text-center" style="width: 05%;"> <?php echo date("d-m-Y", strtotime ($consulta['fecha'])); ?></td>
                  <td class="text-center">
                     <div class="btn-group">
                        <a href="ver_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="btn btn-primary btn-xs" title="Consultar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-eye-open"></span>
                        </a>
                        <a href="edit_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
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
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>