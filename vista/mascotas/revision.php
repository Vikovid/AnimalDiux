<?php
   $page_title = 'Revisión';
   require_once('../../modelo/load.php');
   include ('../../libs/fpdf/pdf.php');
   // Checkin What level user has permission to view this page
   page_require_level(2);
   $user = current_user(); 
   $usuario = $user['name'];
   $idUsuario = $user['id'];
   $idSucursal = $user['idSucursal'];

   $idMasc = "";

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $hora_actual=date('H:i:s',time());

   $idmas= isset($_GET['idMas']) ? $_GET['idMas']:'';
  
   if(isset($_POST['revision'])){
      $idMasc  = remove_junk($db->escape($_POST['idMascotas']));
      $req_fields = array('receta','problema','diagnostico');
      validate_fields($req_fields);
      if(empty($errors)){
         $receta  = remove_junk($db->escape($_POST['receta']));
         $problema  = remove_junk($db->escape($_POST['problema']));
         $temp  = remove_junk($db->escape($_POST['temp']));
         $peso  = remove_junk($db->escape($_POST['peso']));
         $idmas2  = remove_junk($db->escape($_POST['idMascotas']));
         $diagnostico  = remove_junk($db->escape($_POST['diagnostico']));
         $Nota  = remove_junk($db->escape($_POST['Nota']));

         $resultado = altaConsulta($receta,$diagnostico,$problema,$peso,$temp,$idmas2,$fecha_actual,'0',$usuario,$Nota);

         if($resultado){
            $session->msg('s',"Registro Exitoso. ");
         
            $consMascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idMasc);

            $nombre=$consMascota['nombre'];
            $especie=$consMascota['especie'];
            $raza=$consMascota['raza'];
            $Color=$consMascota['Color'];
            $alimento=$consMascota['alimento'];
            $sexo=$consMascota['sexo'];
            $estado=$consMascota['estado'];
            $edad=$consMascota['fecha_nacimiento'];

            $consCliente = buscaClienteMascota($idMasc);

            $nom_cliente=$consCliente['nom_cliente'];
            $idcredencial=$consCliente['idcredencial'];

            altaHistConsulta($idUsuario,$idcredencial,$idMasc,$idSucursal,"Revision",$fecha_actual,$hora_actual);

            $Recomendaciones = "RECOMENDACIONES:";
            $Tratamiento = "TRATAMIENTO:";
            $Diagnostico = "Diagnóstico: ";

            $fecha=date('d-m-Y',time());

            if ($edad != '0000-00-00'){
               $fecha_nacimiento = new DateTime($edad);
               $hoy = new DateTime();
               $edadMas = $hoy->diff($fecha_nacimiento);

               $anios = $edadMas->y;
               $meses = $edadMas->m;
               $dias = $edadMas->d;
            }

            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->AliasNBPages();
            //$pdf->Cell(190,10,utf8_decode('Receta Medica:'),0,1,'C');
            // $pdf->Cell(190,7,$nombre,0, 1 ,'C');

            $pdf->Cell(90,6,'  ',0,1,'C');
            $pdf->Cell(90,6,'  ',0,1,'C');
            $pdf->Cell(90,10,'  ',0,1,'C');
            //$pdf->Cell(190,7,utf8_decode('Información del Propetario'),1, 1 ,'C',0);
            $pdf-> SetFillColor(135,207,235);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(10,6,utf8_decode('Tutor:'),0,0,'C',0);
            $pdf->Cell(100,6,utf8_decode($nom_cliente),0,0,'L',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');

            $pdf->Cell(39,6,utf8_decode('Fecha:'),0,0,'C',0);
            $pdf->Cell(24,6,($fecha),0,0,'L',0);
            $pdf->Cell(90,6,'  ',0,1,'C');
            $pdf->Cell(90,6,'  ',0,1,'C');
            //$pdf->Cell(190,7,utf8_decode('Información del Paciente'),1, 1 ,'C',0);

            $pdf->Cell(20,6,utf8_decode('Paciente:'),0,0,'C',0);
            $pdf->Cell(35,6,utf8_decode($nombre),0,0,'L',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');

            $pdf->Cell(10,6,'Raza:',0,0,'C',0);
            $pdf->Cell(47,6,utf8_decode($raza),0,0,'L',0);
            $pdf->Cell(10,6,'Edad:',0,0,'C',0);
            $pdf->Cell(33,6,utf8_decode($anios." Años ".$meses." meses "),0,0,'L',0);
            $pdf->Cell(90,10,'  ',0,1,'C');
            $pdf->Cell(12,6,utf8_decode('Peso:'),0,0,'C',0);
            $pdf->Cell(14,6,$peso." Kg",0,0,'L',0);
            $pdf->Cell(24,6,'',0,0,'L',0);
            $pdf->Cell(20,6,utf8_decode('Sexo:'),0,0,'C',0);
            $pdf->Cell(42,6,($sexo),0,0,'L',0);
            $pdf->Cell(25,6,utf8_decode('Temperatura:'),0,0,'C',0);
            $pdf->Cell(12,6,utf8_decode($temp),0,0,'L',0); 
            $pdf->Cell(9,6,utf8_decode('°C'),0,0,'L',0);
            $pdf->Cell(7,6,utf8_decode('ID:'),0,0,'L',0);
            $pdf->Cell(7,6,utf8_decode($idcredencial),0,0,'L',0);
            $pdf->Cell(90,6,'  ',0,1,'C');
            $pdf->Cell(190,10,'  ',0,1,'C');
            $pdf->SetFont('Arial','',12);
            //$pdf->Cell(25,6,utf8_decode('Diagnóstico:         '),0,0,'L',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');
            //$pdf->MultiCell(170,6,utf8_decode($diagnostico),0,'L',0);
            $strText = str_replace('\r','',$diagnostico);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n','\n',$strText);
            $strText = str_replace('\n\n','\n',$strText);

            $strText = $Diagnostico.$strText;

            $pos = strpos($strText,'\n');

            if ($pos == 0 && $pos != ""){
               $strText=substr($strText,2,strlen($strText));
            }

            $cont = 0;

            if (strpos($strText,'\n') == false){
               $strText = $strText."\\n";
            }

            $pdf->SetFont('Arial','',11);
            $pdf->SetXY(10,90);
            //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

            while (strlen($strText) > 0){
               $strPos = strpos($strText,'\n');
               if ($strPos == false){
                  $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
                  $strText = "";
               }else{
                  $strTextAux = substr($strText,0,$strPos);
                  if ($cont == 0){
                     $pdf->SetXY(10,90);
                     $cont++;
                  }
                  $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
                  $strText = substr($strText,$strPos+2,strlen($strText));
               }
            }

            $pdf->Cell(190,6,'  ',0,1,'C');
            //$pdf->Cell(32,6,utf8_decode('TRATAMIENTO:'),0,0,'C',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');
            $strText = str_replace('\r','',$receta);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n','\n',$strText);
            $strText = str_replace('\n\n','\n',$strText);

            $strText = $Tratamiento."\\n\n".$strText;

            $pos = strpos($strText,'\n');

            if ($pos == 0 && $pos != ""){
               $strText=substr($strText,2,strlen($strText));
            }

            $cont = 0;

            if (strpos($strText,'\n') == false){
               $strText = $strText."\\n";
            }

            $pdf->SetFont('Arial','',11);
            $pdf->SetXY(10,105);
            //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

            while (strlen($strText) > 0){
               $strPos = strpos($strText,'\n');
               if ($strPos == false){
                  $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
                  $strText = "";
               }else{
                  $strTextAux = substr($strText,0,$strPos);
                  if ($cont == 0){
                     $pdf->SetXY(10,105);
                     $cont++;
                  }
                  $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
                  $strText = substr($strText,$strPos+2,strlen($strText));
               }
            }

            $pdf->Cell(190,6,'  ',0,1,'C');

            //$pdf->Cell(35,6,utf8_decode('Recomendaciones:'),0,0,'C',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');
            $strText = str_replace('\r','',$Nota);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n','\n',$strText);
            $strText = str_replace('\n\n','\n',$strText);

            $strText = $Recomendaciones."\\n\n".$strText;
            //$strText = strtoupper($strText);

            $pos = strpos($strText,'\n');

            if ($pos == 0 && $pos != ""){
               $strText=substr($strText,2,strlen($strText));
            }

            $cont = 0;

            if (strpos($strText,'\n') == false){
               $strText = $strText."\\n";
            }

            $pdf->SetFont('Arial','',11);
            $pdf->SetXY(10,177);
            //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

            while (strlen($strText) > 0){
               $strPos = strpos($strText,'\n');
               if ($strPos == false){
                  $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
                  $strText = "";
               }else{
                  $strTextAux = substr($strText,0,$strPos);
                  if ($cont == 0){
                     $pdf->SetXY(10,177);
                     $cont++;
                  }
                  $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
                  $strText = substr($strText,$strPos+2,strlen($strText));
               }
            }
            $pdf->Output('ticket.pdf', 'I');
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('revision.php?idMas='.$idMasc, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('revision.php?idMas='.$idMasc,false);
      }
   }

   $consMascota = buscaClienteMascota($idmas);

   $nomMascota = $consMascota['nombre'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<body>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Consulta de:</span>
               <span><?php echo $nomMascota ?></span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form name="form1" method="post" action="revision.php" class="clearfix">
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-scale"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="peso" placeholder="Peso">Kg
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-info-sign"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="temp" placeholder="Temperatura">C
                  </div>
               </div>
               <br>
               <br>
               <br>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="problema" class="form-control" placeholder="Historial clínico" maxlength="4000" rows="10" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="diagnostico" class="form-control" placeholder="Diagnóstico (2 Renglones)" maxlength="150" rows="2" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="receta" class="form-control" placeholder="Receta (12 Renglones)" maxlength="1204" rows="12" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="Nota" class="form-control" placeholder="Recomendaciones (10 Renglones)" maxlength="1204" rows="10" style="resize: none" oninput="mayusculas(event)"></textarea></p>
                  </div>
               </div>
               <input type="hidden" value="<?php echo $idmas ?>" name="idMascotas">
               <div class="form-group" align="center">
                  <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
                  <button type="submit" name="revision" class="btn btn-danger">Imprimir y Guardar</button>
               </div>
            </form>
            </div>
         </div>
      </div>
   </div>
</div>
</body>
<?php include_once('../layouts/footer.php'); ?>
