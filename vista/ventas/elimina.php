<?php
$page_title = 'cancelacion';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  $idSucursal= isset($_POST['idSuc']) ? $_POST['idSuc']:'';
  $usuario= isset($_POST['user']) ? $_POST['user']:'';
  $auxTemp= isset($_POST['cveTemp']) ? $_POST['cveTemp']:'';
  $cveTemporal = substr($auxTemp,0,strpos($auxTemp,'|'));
  $prodId = substr($auxTemp,strpos($auxTemp,"|")+1);

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());

  if(isset($_POST['mensaje'])){
     $req_fields = array('mensaje');
     validate_fields($req_fields);
     if(empty($errors)){
        $p_elimina  = isset($_POST['mensaje']) ? $_POST['mensaje']:'';
        $p_idProducto  = isset($_POST['producto']) ? $_POST['producto']:'';
        $p_idSucursal  = isset($_POST['sucursal']) ? $_POST['sucursal']:'';
        $p_cveTemp  = isset($_POST['claveTemp']) ? $_POST['claveTemp']:'';
        $p_usuario  = isset($_POST['usuario']) ? $_POST['usuario']:'';

        altaCancelacion($p_idProducto,$p_idSucursal,$p_usuario,$fecha_actual,$p_elimina);

        borraProdTemporal($p_cveTemp,$p_idSucursal);
	      echo '<script> window.location="add_sale.php";</script>';
     }
  }    	
?>
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
               <span>¿Por que realizaste una cancelación?</span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form method="post" action="elimina.php" class="clearfix">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <input type="text" class="form-control" name="mensaje" placeholder="elimina">
                  </div>
               </div>
               <button type="submit" name="add_mensaje" class="btn btn-danger">Enviar</button>
               <input type="hidden" name="producto" value="<?php echo ucfirst($prodId) ?>">
               <input type="hidden" name="sucursal" value="<?php echo ucfirst($idSucursal) ?>">
               <input type="hidden" name="claveTemp" value="<?php echo ucfirst($cveTemporal) ?>">
               <input type="hidden" name="usuario" value="<?php echo ucfirst($usuario) ?>">
            </form>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
