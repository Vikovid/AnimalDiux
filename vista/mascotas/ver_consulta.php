<?php
   require_once('../../modelo/load.php');
   require('../../libs/fpdf/pdf.php');
   $page_title = 'Vista de consulta';

   // Checkin What level user has permission to view this page
   page_require_level(3);

   $consulta = buscaRegistroPorCampo('Consulta','idconsulta',(int)$_GET['idconsulta']);
   $idMas =    $consulta['idMascota'];
   $fecha =    $consulta['fecha'];

   $mascota = buscaRegistroPorCampo('Mascotas','idMascotas',$idMas);
   $nombre =  $mascota['nombre'];

   $anios = 0;
   $meses = 0;
   $dias =  0;

   $consMasc = buscaClienteMascota($idMas);
           
   $Recomendaciones = "RECOMENDACIONES:";
   $Tratamiento =     "TRATAMIENTO:";
   $Diagnostico =     "Diagnóstico: ";

   $raza =         $consMasc['raza'];
   $sexo =         $consMasc['sexo'];
   $edad =         $consMasc['fecha_nacimiento'];
   $Color =        $consMasc['Color'];
   $nombre =       $consMasc['nombre'];
   $estado =       $consMasc['estado'];
   $especie =      $consMasc['especie'];
   $alimento =     $consMasc['alimento'];
   $nom_cliente =  $consMasc['nom_cliente'];
   $idcredencial = $consMasc['idcredencial'];

   if (isset($_POST['Imprimir']) && $_POST['Imprimir'] === "1") {
      $c_diagnostico = remove_junk($db->escape($_POST['diagnostico']));
      $c_problema =    remove_junk($db->escape($_POST['problema']));
      $c_receta  =     remove_junk($db->escape($_POST['receta']));
      $c_temp =        remove_junk($db->escape($_POST['temp']));
      $c_peso =        remove_junk($db->escape($_POST['peso']));
      $c_nota =        remove_junk($db->escape($_POST['receta']));

      $fecha = date('d-m-Y',time());

      if ($edad != '0000-00-00'){
         $fecha_nacimiento = new DateTime($edad);
         $hoy =              new DateTime();
         $edadMas =          $hoy->diff($fecha_nacimiento);

         $anios = $edadMas->y;
         $meses = $edadMas->m;
         $dias =  $edadMas->d;
      }

      $pdf = new PDF();
      $pdf->AddPage();
      $pdf->AliasNBPages();
      //$pdf->Cell(190,10,utf8_decode('Receta Medica:'),0,1,'C');
      //$pdf->Cell(190,7,$nombre,0, 1 ,'C');
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
      $pdf->Cell(13,6,utf8_decode('Peso:'),0,0,'C',0);
      $pdf->Cell(6,6,$c_peso." Kg",0,0,'L',0);
      $pdf->Cell(31,6,'',0,0,'L',0);
      $pdf->Cell(20,6,utf8_decode('Sexo:'),0,0,'C',0);
      $pdf->Cell(42,6,($sexo),0,0,'L',0);
      $pdf->Cell(25,6,utf8_decode('Temperatura:'),0,0,'C',0);
      $pdf->Cell(6,6,utf8_decode($c_temp),0,0,'L',0); 
      $pdf->Cell(7,6,utf8_decode('°C'),0,0,'L',0); 
      $pdf->Cell(7,6,utf8_decode('ID:'),0,0,'C',0);
      $pdf->Cell(7,6,utf8_decode($idcredencial),0,0,'L',0); 
      $pdf->Cell(90,6,'  ',0,1,'C');
      $pdf->Cell(190,6,'  ',0,1,'C');
      $pdf->SetFont('Arial','',12);
      //$pdf->Cell(25,6,utf8_decode('Diagnóstico:         '),0,0,'L',0);
      //$pdf->Cell(90,6,'  ',0,1,'C');
      //$pdf->MultiCell(170,6,utf8_decode($c_diagnostico),0,'L',0);
      $strText = str_replace('\r','',$c_diagnostico);
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

      if ($pos == 0 && $pos != "")
         $strText=substr($strText,2,strlen($strText));

      $cont = 0;

      if (strpos($strText,'\n') == false)
         $strText = $strText."\\n";

      $pdf->SetFont('Arial','',11);
      $pdf->SetXY(10,90);
      //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

      while (strlen($strText) > 0) {
         $strPos = strpos($strText,'\n');

         if ($strPos == false){
            $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
            $strText = "";
         } else {

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
      $strText = str_replace('\r','',$c_receta);
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
      //$strText = strtoupper($strText);

      $pos = strpos($strText,'\n');

      if ($pos == 0 && $pos != "")
         $strText=substr($strText,2,strlen($strText));

      $cont = 0;

      if (strpos($strText,'\n') == false)
         $strText = $strText."\\n";

      $pdf->SetFont('Arial','',11);
      $pdf->SetXY(10,107);
      //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

      while (strlen($strText) > 0) {
         $strPos = strpos($strText,'\n');

         if ($strPos == false){
            $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
            $strText = "";
         } else {
            $strTextAux = substr($strText,0,$strPos);
            
            if ($cont == 0){
               $pdf->SetXY(10,107);
               $cont++;
            }

            $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
            $strText = substr($strText,$strPos+2,strlen($strText));
         }
      }

      $pdf->Cell(190,6,'  ',0,1,'C');

      $strText = str_replace('\r','',$c_nota);
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

      if ($pos == 0 && $pos != "")
         $strText=substr($strText,2,strlen($strText));

      $cont = 0;

      if (strpos($strText,'\n') == false)
         $strText = $strText."\\n";

      $pdf->SetFont('Arial','',11);
      $pdf->SetXY(10,173);
      //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

      while (strlen($strText) > 0){
         $strPos = strpos($strText,'\n');

         if ($strPos == false){
            $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
            $strText = "";
         }else{
            $strTextAux = substr($strText,0,$strPos);
            
            if ($cont == 0){
               $pdf->SetXY(10,173);
               $cont++;
            }

            $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
            $strText = substr($strText,$strPos+2,strlen($strText));
         }
      }

      $pdf->Output('ticket.pdf', 'I');
   }
?>

<?php include_once('../layouts/header.php'); ?>

<script type="text/javascript">
   function imprimir(){
      document.form1.Imprimir.value = "1";
      document.form1.action = "ver_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'] ?>";
      document.form1.submit();
   }
</script>

<form name="form1" method="post" action="history.php" class="clearfix">
   <div class="row col-md-9">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="row col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <div class="form-group">
                  <span class="glyphicon glyphicon-folder-open"></span>
                  <span>Vista de consulta de: <?php echo $nombre ?></span>
               </div>
               <div class="form-group">
                  <span class="glyphicon glyphicon-calendar"></span>
                  <span>Fecha: <?php echo $fecha ?></span>
               </div>
            </strong>
         </div>
         <div class="panel-body">
            <div class="form-group row">
               <div class="col-md-6">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-scale"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="peso" placeholder="Peso" value="<?php echo remove_junk($consulta['peso']); ?>" readonly>Kg.
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-info-sign"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="temp" placeholder="Temperatura" value="<?php echo remove_junk($consulta['temperatura']); ?>" readonly>°C
                  </div>
               </div>
            </div>
            <div class="form-group input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <textarea name="problema" class="form-control" placeholder="Historial clínico" maxlength="300" rows="3" style="resize: none" readonly><?php echo remove_junk($consulta['problema']); ?></textarea>
            </div>
            <div class="form-group input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <textarea name="diagnostico" class="form-control" placeholder="Diagnóstico" maxlength="100" rows="1" style="resize: none" readonly><?php echo remove_junk($consulta['diagnostico']); ?></textarea>
            </div>
            <div class="form-group input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <textarea name="receta" class="form-control" placeholder="Receta" maxlength="1204" rows="12" style="resize: none" readonly><?php echo remove_junk($consulta['consulta']); ?></textarea>
            </div>
            <div class="form-group input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <textarea name="receta" class="form-control" placeholder="Nota" maxlength="100" rows="1" style="resize: none" readonly><?php echo remove_junk($consulta['nota']); ?></textarea>
            </div>
            <div class="form-group" align="center">
               <button type="submit" name="consulta" class="btn btn-danger">Regresar</button>
               <a href="#" onclick="imprimir();" class="btn btn-success">Imprimir</a>

               <input type="hidden"  class="form-control" value="<?php echo $idMas ?>" name="idMascotas">
               <input type="hidden" value="0" name="Imprimir">
            </div>
         </div>
      </div>
   </div>
</form>

<?php include_once('../layouts/footer.php'); ?>