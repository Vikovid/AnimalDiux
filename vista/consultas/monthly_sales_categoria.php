<?php
   $page_title = 'Venta mensual';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(1);

   $all_categorias = find_all('categories');
   $fechaInicial = "";
   $fechaFinal = "";

   $idCategoria= isset($_POST['categoria']) ? $_POST['categoria']:'';
   if ($idCategoria != "")
      $subcategorias = buscaRegsPorCampo('subcategorias','idCategoria',$idCategoria);
   else
      $subcategorias = array();

   $categorias = find_all('tempcatsubcat');
   
   foreach ($categorias as $cat): 
      $fechaInicial = $cat['fechaInicial'];
      $fechaFinal = $cat['fechaFinal'];
      break;
   endforeach;

   $fechIni = date ('d-m-Y', strtotime($fechaInicial));
   $fechFin = date ('d-m-Y', strtotime($fechaFinal));

   reset($categorias);
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>
<script language="Javascript">
   function excel(){
      document.form1.action = "../excel/monthlysalescategoria.php";
      document.form1.submit();
   }
   function recarga(){
      document.form1.action = "monthly_sales_categoria.php";
      document.form1.submit();
   }
   function ventasGastos() {
      document.form1.action = "tempCatSubcat.php";
      document.form1.submit();
   }
</script>
<!DOCTYPE html>
<html>
<head>
<title>Ventas Mensuales</title>
</head>

<body>
  <form name="form1" method="post" action="monthly_sales_categoria.php">

<span>Período:</span>
<?php echo "del $fechIni al $fechFin";?>

<div class="row">
  <div class="col-md-7">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-10">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-3">
                  <select class="form-control" name="categoria" onchange="recarga();">
                     <option value="">Selecciona una categoría</option>
                     <?php  foreach ($all_categorias as $cats): ?>
                     <?php if(isset($_POST["categoria"]) && $_POST["categoria"]==$cats['id']){ ?>
                     <option value="<?php echo $cats['id'] ?>" selected><?php echo $cats['name'] ?></option>
                     <?php } else { ?>
                     <option value="<?php echo $cats['id'] ?>"><?php echo $cats['name'] ?></option>
                     <?php } ?>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-3">
                  <select class="form-control" name="subcategoria">
                     <option value="">Selecciona una subcategoría</option>
                     <?php  foreach ($subcategorias as $subcat): ?>
                     <option value="<?php echo $subcat['idSubCategoria'] ?>">
                     <?php echo $subcat['nombre'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>               
               <div class="col-md-2">
                  <select class="form-control" name="mes">
                     <option value=""  >Mes</option>
                     <option value="01">Enero</option>
                     <option value="02">Febrero</option>
                     <option value="03">Marzo</option>
                     <option value="04">Abril</option>
                     <option value="05">Mayo</option>
                     <option value="06">Junio</option>
                     <option value="07">Julio</option>
                     <option value="08">Agosto</option>
                     <option value="09">Septiembre</option>
                     <option value="10">Octubre</option>
                     <option value="11">Noviembre</option>
                     <option value="12">Diciembre</option>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="anio">
                     <option value=""    >Año</option>
                     <option value="2020">2020</option>
                     <option value="2021">2021</option>
                     <option value="2022">2022</option>
                     <option value="2023">2023</option>
                     <option value="2024">2024</option>
                     <option value="2025">2025</option>
                     <option value="2026">2026</option>
                     <option value="2027">2027</option>
                     <option value="2028">2028</option>
                     <option value="2029">2029</option>
                     <option value="2030">2030</option>
                     <option value="2031">2031</option>
                     <option value="2032">2032</option>
                     <option value="2033">2033</option>
                     <option value="2034">2034</option>
                     <option value="2035">2035</option>
                     <option value="2036">2036</option>
                     <option value="2037">2037</option>
                     <option value="2038">2038</option>
                     <option value="2039">2039</option>
                     <option value="2040">2040</option>
                  </select>
               </div>  
               <a href="#" onclick="ventasGastos();" class="btn btn-primary">Buscar</a>
               <a href="#" onclick="excel();" class="btn btn-xs btn-success">Excel</a>      
               <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">   
            </div>
         </div>
         <div class="panel-body">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th> Categoría </th>
                     <th> Subcategoría </th>
                     <th class="text-center" style="width: 14%;"> Cantidad</th>
                     <th class="text-center" style="width: 14%;"> Venta </th>
                     <th class="text-center" style="width: 14%;"> Gasto </th>
                     <th class="text-center" style="width: 14%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($categorias as $categoria): ?>
                     <tr>
                        <td><?php echo remove_junk($categoria['categoria']); ?></td>
                        <td><?php echo remove_junk($categoria['subcategoria']); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$categoria['cantidad']); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$categoria['venta']); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$categoria['gasto']); ?></td>
                        <td class="text-right"><?php echo money_format('%.2n',$categoria['ganancia']); ?></td>
                     </tr>
               <?php endforeach;?>
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