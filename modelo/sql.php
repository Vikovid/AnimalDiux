<?php
require_once(LIB_PATH_INC.DS."load.php");

/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST,
/* coming from the login form.
/*--------------------------------------------------------------*/
function authenticate($username='', $password='') {
   global $db;
   $username = $db->escape($username);
   $password = $db->escape($password);

   $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);

   $result = $db->query($sql);

   if($db->num_rows($result)){
     $user = $db->fetch_assoc($result);
     $password_request = sha1($password);
     if($password_request === $user['password'] ){
        return $user['id'];
     }
   }
   return false;
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/
function updateLastLogIn($user_id){
    global $db;

    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

function cambiaContrasenia($contrasenia,$id){
    global $db;

    $sql = "UPDATE users SET password ='$contrasenia' WHERE id='$id'";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

function cambiaDatosUsuario($nombre,$nombreUsuario,$id){
    global $db;

    $sql = "UPDATE users SET name ='$nombre',username ='$nombreUsuario' WHERE id='$id'";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

function messages($fecha){
  global $db;

  $sql = "SELECT * FROM  mensaje WHERE fecha = '{$fecha}'";

  $result = $db->query($sql);
  if ($db->num_rows($result)){
     $mens = $db->fetch_assoc($result);
     return $mens['mensaje'];
  }
  return false;
}

/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table,$id){
   global $db;

   $id = (int)$id;
   if(tableExists($table)){
      $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
  
      if($result = $db->fetch_assoc($sql))
         return $result;
      else
         return null;
   }
}

/*--------------------------------------------------------------*/
/* Find current log in user by session id
/*--------------------------------------------------------------*/
function current_user(){
   static $current_user;
   global $db;

   if(!$current_user){
      if(isset($_SESSION['user_id'])):
         $user_id = intval($_SESSION['user_id']);
         $current_user = find_by_id('users',$user_id);
      endif;
   }
   return $current_user;
}

/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
   global $db;
 
   $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
  
   if($table_exit) {
      if($db->num_rows($table_exit) > 0)
         return true;
      else
         return false;
   }
}

/*--------------------------------------------------------------*/
/* Find group level
/*--------------------------------------------------------------*/
function find_by_groupLevel($level){
   global $db;

   $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    
   $result = $db->query($sql);

   return($db->num_rows($result) === 0 ? true : false);
}

function actUsuario($nombre,$usuario,$nivel,$status,$sucursal,$id){
   global $db;

   $sql  = "UPDATE users SET name='{$nombre}',username='{$usuario}',user_level='{$nivel}',";
   $sql .= "status='{$status}',idSucursal='{$sucursal}' WHERE id='$id'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/
function actContrasenia($pass,$id){
    global $db;

    $sql = "UPDATE users SET password='{$pass}' WHERE id ='$id'";

    $result = $db->query($sql);

    return ($result && $db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for cheaking which user level has access to page
/*--------------------------------------------------------------*/
function page_require_level($require_level){
   global $session;
   $current_user = current_user();
   $login_level =  find_by_groupLevel($current_user['user_level']);
   //if user not login
   if (!$session->isUserLoggedIn(true)):
      $session->msg('d','Por favor Iniciar sesión...');
      redirect('../../index.php', false);
      //if Group status Deactive
   elseif($login_level === true):
      $session->msg('d','Este nivel de usuario está inactivo!');
      redirect('../login/home.php',false);
      //cheackin log in User level and Require level is Less than or equal to
   elseif($current_user['user_level'] <= (int)$require_level):
      return true;
   else:
      $session->msg("d", "¡Lo siento!  no tienes permiso para ver la página.");
      redirect('../login/home.php', false);
   endif;
}

function find_all_user(){
   global $db;

   $sql  = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
   $sql .="g.group_name,s.nom_sucursal ";
   $sql .="FROM users u, user_groups g, sucursal s ";
   $sql .="WHERE g.group_level=u.user_level AND s.idSucursal=u.idSucursal ORDER BY u.name ASC";

   $result = find_by_sql($sql);

   return $result;
}

/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;

  $result = $db->query($sql);
  $result_set = $db->while_loop($result);

  return $result_set;
}

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;

   if(tableExists($table)){
      return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id){
   global $db;

   if(tableExists($table)){
      $sql  = "DELETE FROM ".$db->escape($table);
      $sql .= " WHERE id=". $db->escape($id);
      $sql .= " LIMIT 1";
      $db->query($sql);
    
      return ($db->affected_rows() === 1) ? true : false;
   }
}

function consultaCampos($campos,$tabla){
   global $db;

   if(tableExists($tabla)){
      $sql = "SELECT ".$campos." FROM ".$tabla;

      $result = $db->query($sql);

      return $result;
   }
}

function altaUsuario($nombre,$usuario,$pass,$nivel,$sucursal){
   global $db;

   $sql  = "INSERT INTO users (";
   $sql .="id,name,username,password,user_level,status,idSucursal";
   $sql .=") VALUES (";
   $sql .=" '','{$nombre}','{$usuario}','{$pass}','{$nivel}','1','{$sucursal}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

/*Consulta de cliente*/
function join_cliente_table(){
   global $db;
   
   $sql  ="SELECT SUM(a.price/100) AS venta ,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.IdCredencial ";
   $sql .="FROM cliente b LEFT JOIN sales a ON a.idCliente = b.idCredencial and a.descuentos = '0' ";
   $sql .="GROUP BY b.idCredencial";

   return find_by_sql($sql);
}

function join_cliente_table1a($codigo){
   global $db;

   $sql  ="SELECT SUM(a.price/100) AS venta,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.IdCredencial ";
   $sql .="FROM cliente b ";
   $sql .="LEFT JOIN sales a ON a.idCliente = b.idCredencial and a.descuentos = '0' ";
   $sql .="WHERE b.idcredencial = $codigo ";
   $sql .="GROUP BY b.idCredencial ";

   return find_by_sql($sql);
}

function join_cliente_table2a($codigo){
   global $db;

   $sql  ="SELECT SUM(a.price/100) AS venta,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.IdCredencial ";
   $sql .="FROM cliente b ";
   $sql .="LEFT JOIN sales a ON a.idCliente = b.idCredencial and a.descuentos = '0' ";
   $sql .="WHERE b.nom_cliente like '%$codigo%' ";
   $sql .="GROUP BY b.idCredencial";

   return find_by_sql($sql);
}

function buscaRegistroMaximo($tabla,$campo){
   global $db;
   
   if(tableExists($tabla)){
      $sql = "SELECT * FROM {$db->escape($tabla)} WHERE $campo=(SELECT MAX($campo) from $tabla) LIMIT 1";
 
      $query = $db->query($sql);

      if($result = $db->fetch_assoc($query))
         return $result;
      else
         return null;
   }
}

function buscaRegistroPorCampo($tabla,$campo,$valor){
   global $db;

   if(tableExists($tabla)){
      $sql = "SELECT * FROM {$db->escape($tabla)} WHERE $campo='{$db->escape($valor)}' LIMIT 1";

      $query = $db->query($sql);

      if($result = $db->fetch_assoc($query))
         return $result;
      else
         return null;
   }
}

function altaCliente($nombre,$direccion,$telefono,$correo,$credencial){
   global $db;

   $sql  = "INSERT INTO cliente (";
   $sql .=" nom_cliente,dir_cliente,tel_cliente,correo,idcredencial";
   $sql .=") VALUES (";
   $sql .="'{$nombre}','{$direccion}','{$telefono}','{$correo}','{$credencial}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actCliente($nombre,$direccion,$telefono,$correo,$idCliente){
   global $db;

   $sql  ="UPDATE cliente set nom_cliente = '{$nombre}',dir_cliente = '{$direccion}',";
   $sql .="tel_cliente = '{$telefono}',correo = '{$correo}' WHERE idcredencial = '$idCliente'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function borraRegistroPorCampo($tabla,$campo,$valor){
   global $db;

   if(tableExists($tabla)){

      $sql  = "DELETE FROM ".$db->escape($tabla);
      $sql .= " WHERE $campo = '$valor'";

      $db->query($sql);
    
      return ($db->affected_rows() === 1) ? true : false;
   }
}

function join_historico_table(){
   global $db;
   
   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento AND P.id = H.id_producto ";
   $sql .="AND S.idSucursal = H.idSucursal ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}   

function join_his_table1($codigo,$p_scu){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento AND P.id = H.id_producto ";
   $sql .="AND P.Codigo = $codigo AND H.idSucursal = $p_scu ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function join_his_table2($codigo,$p_scu){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento AND P.id = H.id_producto ";
   $sql .="AND P.name like '%$codigo%' and H.idSucursal = $p_scu ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function join_his_table3($p_scu){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento and P.id = H.id_producto ";
   $sql .="AND H.idSucursal = $p_scu ORDER BY H.idHistorico DESC ";

   return find_by_sql($sql);
}

function join_his_table1a($codigo){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento and P.id = H.id_producto AND S.idSucursal = H.idSucursal ";
   $sql .="AND P.Codigo = $codigo ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function join_his_table2a($codigo){
   global $db;

   $sql  ="SELECT H.idHistorico,M.movimiento,P.name,S.nom_sucursal,H.comentario,H.qtyin,";
   $sql .="H.qtyfinal,H.usuario,H.vendedor,H.fechaMov,H.horaMov ";
   $sql .="FROM historico H,movimiento M,products P,sucursal S ";
   $sql .="WHERE M.id_movimiento = H.id_movimiento and P.id = H.id_producto AND S.idSucursal = H.idSucursal ";
   $sql .="AND P.name like '%$codigo%' ORDER BY H.idHistorico DESC";

   return find_by_sql($sql);
}

function histEfecUsuSuc($p_usu,$p_suc){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A,sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and A.usuario = $p_usu ";
   $sql .="and A.idSucursal = $p_suc and M.id_movimiento = A.id_movimiento ORDER BY A.idHistEfectivo DESC";

   return find_by_sql($sql);
}

function histEfecSuc($p_suc){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A, sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and A.idSucursal = $p_suc ";
   $sql .="and M.id_movimiento = A.id_movimiento ORDER BY A.idHistEfectivo DESC";

   return find_by_sql($sql);
}

function histEfecUsu($p_usu){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A, sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and A.usuario = $p_usu ";
   $sql .="and M.id_movimiento = A.id_movimiento ORDER BY A.idHistEfectivo DESC";
    
   return find_by_sql($sql);
}

function histEfectivo(){
   global $db;

   $sql  ="SELECT A.idHistEfectivo,M.movimiento,A.cantIni,A.cantFinal,D.nom_sucursal,E.username,";
   $sql .="A.vendedor,A.fechaMov,A.horaMov ";
   $sql .="FROM histefectivo A, sucursal D, users E, movimiento M ";
   $sql .="WHERE A.idSucursal = D.idSucursal and E.id = A.usuario and M.id_movimiento = A.id_movimiento ";
   $sql .="ORDER BY A.idHistEfectivo DESC LIMIT 10";

   return find_by_sql($sql);
}   

function registrarEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$usuario,$vendedor,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO histefectivo (";
   $sql .="idHistEfectivo,id_movimiento,cantIni,cantFinal,idSucursal,usuario,vendedor,fechaMov,horaMov) "; 
   $sql .="VALUES ('','$movimiento','{$montoActual}','{$montoFinal}','{$idSucursal}','{$usuario}',";
   $sql .="'{$vendedor}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actCaja($monto,$fecha,$idCaja){
   global $db;

   $sql ="UPDATE caja SET monto = '$monto',fecha = '$fecha' WHERE id = '$idCaja'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function cancelaciones(){
   global $db;

   $sql  ="SELECT a.idcancelacion,b.name,c.nom_sucursal,a.usuario,a.date,a.mensaje ";
   $sql .="FROM cancelacion a,sucursal c,products b ";
   $sql .="WHERE a.idproducto = b.id and c.idSucursal = a.idsucursal ";
   $sql .="ORDER BY a.idCancelacion DESC";

   return find_by_sql($sql);
}

function cancelacionesXSuc($p_scu){
   global $db;

   $sql  ="SELECT a.idcancelacion,b.name,c.nom_sucursal,a.usuario,a.date,a.mensaje ";
   $sql .="FROM cancelacion a,sucursal c,products b ";
   $sql .="WHERE a.idproducto = b.id and a.idsucursal = $p_scu and c.idSucursal = a.idsucursal ";
   $sql .="ORDER BY a.idCancelacion DESC";

   return find_by_sql($sql);
}

function altaCategoria($nombre){
   global $db;

   $sql = "INSERT INTO categories (name) VALUES ('{$nombre}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actCategoria($nombre,$id){
   global $db;

   $sql = "UPDATE categories SET name = '{$nombre}' WHERE id = '{$id}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaProveedor($proveedor,$direccion,$telefono,$contacto){
   global $db;

   $sql  = "INSERT INTO proveedor (idProveedor,nom_proveedor,direccion,telefono,contacto";
   $sql .=") VALUES (";
   $sql .=" '','{$proveedor}','{$direccion}','{$telefono}','{$contacto}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actProveedor($proveedor,$direccion,$telefono,$contacto,$idProveedor){
   global $db;

   $sql  ="UPDATE proveedor SET ";
   $sql .="nom_proveedor = '{$proveedor}',direccion ='{$direccion}',telefono = '{$telefono}',";
   $sql .="contacto = '{$contacto}' WHERE idProveedor ='{$idProveedor}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}


function cuentaRegistros($aContar,$tabla,$campo,$valor){
  global $db;

  if(tableExists($tabla))
  {
    $sql = "SELECT COUNT($aContar) AS total FROM {$db->escape($tabla)} WHERE $campo='{$db->escape($valor)}' LIMIT 1";

    $result = $db->query($sql);
    return($db->fetch_assoc($result));
  }
}

/*--------------------------------------------------------------*/
/* Function for Finding all product name
/* JOIN with categorie  and media database table
/*--------------------------------------------------------------*/
function join_product_table(){
   global $db;

   $sql  = "SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.foto,p.fechaRegistro,c.name ";
   $sql .= "AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .= "FROM products p ";
   $sql .= "LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .= "LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .= "LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";
   $sql .= "AND p.idSubcategoria = sc.idSubCategoria ";   
   $sql .= "ORDER BY p.name ASC";

   return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding product name
/* JOIN with categorie and media database table
/*--------------------------------------------------------------*/
//Numérico
function join_product_table1($codigo,$categoria,$subcategoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.idSucursal,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
   $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
   $sql .="WHERE "; 
      if ($codigo != '') {
         if ($subcategoria == '') {
            if($categoria != '')
               $sql .= " p.Codigo = '$codigo' AND c.id = '$categoria' ";
            else
               $sql .= " p.Codigo = '$codigo' ";
         } else 
            $sql .= " p.Codigo = '$codigo' AND c.id = '$categoria' AND sc.idSubCategoria = '$subcategoria' ";
      } else {
         if ($subcategoria == '')
            $sql .= " c.id = '$categoria' ";
         else
            $sql .= " c.id = '$categoria' AND sc.idSubCategoria = '$subcategoria' ";
      }
   $sql .=" ORDER BY p.name ASC";

   return find_by_sql($sql);
}
// function join_product_table1($codigo,$categoria){
//    global $db;

//    $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.idSucursal,p.sale_price,p.foto,p.fechaRegistro,c.name ";
//    $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
//    $sql .="FROM products p ";
//    $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
//    $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
//    $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
//    $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
//    $sql .="WHERE p.Codigo = $codigo and c.id = $categoria ";
//    $sql .="ORDER BY p.name ASC";

//    return find_by_sql($sql);
// }

/*--------------------------------------------------------------*/
/* Function for Finding product name
/* JOIN with categorie and media database table
/*--------------------------------------------------------------*/
// Alfanumérico
function join_product_table2($codigo,$categoria,$subcategoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.idSucursal,p.buy_price,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
   $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
   $sql .="WHERE ";
      if ($codigo != '') {
         if ($subcategoria == '') {
            if($categoria != '')
               $sql .= " p.name like '%$codigo%' AND c.id = '$categoria' ";
            else
               $sql .= " p.name like '%$codigo%' ";
         } else 
            $sql .= " p.name like '%$codigo%' AND c.id = '$categoria' AND sc.idSubCategoria = '$subcategoria' ";
      } else {
         if ($subcategoria == '')
            $sql .= " c.id = '$categoria' ";
         else
            $sql .= " c.id = '$categoria' AND sc.idSubCategoria = '$subcategoria' ";
      }
   $sql .=" ORDER BY p.name ASC";
 
   return find_by_sql($sql);
}
// function join_product_table2($codigo,$categoria){
//    global $db;

//    $sql  ="SELECT p.id,p.name,p.quantity,p.idSucursal,p.buy_price,p.sale_price,p.foto,p.fechaRegistro,c.name ";
//    $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
//    $sql .="FROM products p ";
//    $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
//    $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
//    $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
//    $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
//    $sql .="WHERE p.name like '%$codigo%' AND c.id = $categoria ";
//    $sql .="ORDER BY p.name ASC";
 
//    return find_by_sql($sql);
// }

function join_select_categories($categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
   $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
   $sql .="WHERE c.id = '$categoria' ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function join_select_subcategories($categoria, $subcategoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
   $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
   $sql .="WHERE c.id = '$categoria' AND sc.idSubCategoria= '$subcategoria'";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

// Numérico
function join_product_table1a($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.idSucursal,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";
   $sql .="AND p.idSubcategoria = sc.idSubCategoria ";      
   $sql .="WHERE p.Codigo = $codigo ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

// Por nombre
function join_product_table2a($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.idSucursal,p.buy_price,p.sale_price,p.foto,p.fechaRegistro,c.name ";
   $sql .="AS categorie,s.nom_sucursal AS sucursal,sc.nombre ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN subcategorias sc ON sc.idCategoria = c.id ";      
   $sql .="AND p.idSubcategoria = sc.idSubCategoria ";
   $sql .="WHERE p.name like '%$codigo%' ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function totalesProductos(){
   global $db;

   $sql  ="SELECT SUM(quantity) as cantidadTotal,SUM(buy_price * quantity) as totalPrecio,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalesProductosCod($codigo){
   global $db;

   $sql  ="SELECT SUM(p.buy_price * p.quantity) AS totalPrecio,SUM(p.quantity) As cantidadTotal,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%'";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalesProductosCat($categoria){
   global $db;

   $sql  ="SELECT SUM(p.buy_price * p.quantity) AS totalPrecio,SUM(p.quantity) As cantidadTotal,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalesProductosCodCat($codigo,$categoria){
   global $db;

   $sql  ="SELECT SUM(p.buy_price * p.quantity) AS totalPrecio,SUM(p.quantity) As cantidadTotal,";
   $sql .="SUM(sale_price * quantity) as totalVenta FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = '$categoria' ";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function productosPDF(){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodCatPDF($codigo,$categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = '$categoria' ";
   $sql .="ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCatPDF($categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodPDF($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,p.fechaRegistro,";
   $sql .="s.nom_sucursal,s.idsucursal,p.Codigo,c.name as categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosExcel(){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodCatExcel($codigo,$categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' AND c.id = '$categoria' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCatExcel($categoria){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE c.id = '$categoria' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function productosCodExcel($codigo){
   global $db;

   $sql  ="SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.idSucursal,";
   $sql .="DATE_FORMAT(p.fechaRegistro,'%d-%m-%Y %r') AS date,s.nom_sucursal,s.idsucursal,p.Codigo,";
   $sql .="c.name AS categories ";
   $sql .="FROM products p ";
   $sql .="LEFT JOIN sucursal s ON s.idSucursal = p.idSucursal ";
   $sql .="LEFT JOIN categories c ON c.id = p.categorie_id ";
   $sql .="WHERE p.name like '%$codigo%' ORDER BY p.name ASC";

   return find_by_sql($sql);
}

function altaProducto($name,$cantidad,$pCompra,$pVenta,$categoria,$foto,$fechaReg,$codigo,$proveedor,$sucursal,$fecCad,$cantCaja,$precioCaja,$fechaMod,$subcategoria){
   global $db;

   $sql  = "INSERT INTO products (";
   $sql .="name,quantity,buy_price,sale_price,categorie_id,foto,fechaRegistro,Codigo,idProveedor,";
   $sql .="idSucursal,fecha_caducidad,cantidadCaja,precioCaja,fechaMod,idSubcategoria";
   $sql .=") VALUES (";
   $sql .="'{$name}','{$cantidad}','{$pCompra}','{$pVenta}','{$categoria}','{$foto}','{$fechaReg}',";
   $sql .="'{$codigo}','{$proveedor}','{$sucursal}','{$fecCad}','{$cantCaja}','{$precioCaja}',";
   $sql .="'{$fechaMod}','{$subcategoria}') ON DUPLICATE KEY UPDATE name='{$name}'";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaHistorico($movimiento,$idProducto,$cantIni,$cantFin,$comentario,$sucursal,$usuario,$vendedor,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO historico (idHistorico,id_movimiento,id_producto,qtyin,qtyfinal,comentario,";
   $sql .="idSucursal,usuario,vendedor,fechaMov,horaMov) ";
   $sql .="VALUES ('','{$movimiento}','{$idProducto}','{$cantIni}','{$cantFin}','{$comentario}',";
   $sql .="'{$sucursal}','{$usuario}','{$vendedor}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actStockProducto($cantidad,$fecha,$idProducto,$foto){
   global $db;

   $sql  ="UPDATE products SET quantity = '$cantidad',fechaMod = '$fecha'";

   if ($foto != "")
      $sql .=",foto = '$foto' WHERE id = '$idProducto'";
   else
      $sql .=" WHERE id = '$idProducto'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actProducto($nombre,$cantidad,$pCompra,$pVenta,$categoria,$subcategoria,$codigo,$sucursal,$proveedor,$fecCad,$cantCaja,$precioCaja,$fecha,$foto,$idproducto){
   global $db;

   $sql  ="UPDATE products SET ";
   $sql .="name ='{$nombre}',quantity ='{$cantidad}',buy_price = '{$pCompra}',sale_price = '{$pVenta}',";
   $sql .="categorie_id = '{$categoria}',Codigo = '{$codigo}',idSucursal= '{$sucursal}',";
   $sql .="idProveedor = '{$proveedor}',fecha_caducidad = '{$fecCad}',cantidadCaja = '{$cantCaja}',";
   $sql .="precioCaja = '{$precioCaja}',fechaMod = '{$fecha}',idSubcategoria = '{$subcategoria}'";
   
   if ($foto != "")
      $sql .=",foto = '{$foto}' WHERE id ='{$idproducto}'";
   else
      $sql .="WHERE id ='{$idproducto}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function buscaProductosCod($codigo,$usuario,$sucursal){
   global $db;

   $sql  ="SELECT p.id,p.name,p.sale_price,p.Codigo from products p,users u where p.Codigo = '$codigo' ";
   $sql .="or p.name like '%{$codigo}%' and p.quantity > 0 and u.id = '{$usuario}' ";
   $sql .="and p.idSucursal = '{$sucursal}' and u.idSucursal = '{$sucursal}' group by p.name";

   return find_by_sql($sql);  
}

function buscaProducto($usuario,$sucursal){
   global $db;

   $sql  ="SELECT p.id,p.name,p.sale_price,p.Codigo from products p,users u where p.quantity > 0 ";
   $sql .="and u.id = '{$usuario}' and p.idSucursal = '{$sucursal}' and u.idSucursal = '{$sucursal}' ";
   $sql .="LIMIT 0,3";   

   return find_by_sql($sql);     
}

function buscaProdsTempEntregas($usuario){
   global $db;

   $sql  ="SELECT a.cve_temporal,a.cantidad,a.precio,b.sale_price,b.quantity,b.name,b.cantidadCaja,";
   $sql .="b.precioCaja,b.id from tempentregas a,products b where a.product_id=b.id ";
   $sql .="and a.usuario='$usuario'";

   return find_by_sql($sql);     
}

function sumaCampo($aSumar,$tabla,$campo,$valor){
   global $db;

   $sql = "SELECT SUM($aSumar) as total FROM $tabla WHERE $campo = '$valor'";   

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function actCantidad($tabla,$campo,$multiplo,$precio,$clave){
   global $db;

   $sql  ="UPDATE $tabla SET $campo = '$multiplo',precio='$precio' ";
   $sql .="WHERE cve_temporal = '$clave'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function join_gastos_table(){
   global $db;

   $sql = "SELECT g.id, g.descripcion, g.monto, g.fecha, g.categoria, g.iva, g.total, p.nom_proveedor, ";
   $sql .= "p.idProveedor, tp.tipo_pago, tp.id_pago, c.name, sc.nombre, g.factura ";
   $sql .= "FROM gastos g LEFT JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "LEFT JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "LEFT JOIN categories c ON g.categoria = c.id ";
   $sql .= "LEFT JOIN subcategorias sc ON g.subCategoria = sc.idSubCategoria ";
   $sql .= "AND g.categoria = sc.idCategoria ORDER BY g.fecha DESC ";

   return find_by_sql($sql);
}
function join_gastos_table2($fechaIni,$fechaFin){
   global $db;

   $sql = "SELECT g.id, g.descripcion, g.monto, g.fecha, g.categoria, g.iva, g.total, p.nom_proveedor, ";
   $sql .= "p.idProveedor, tp.tipo_pago, tp.id_pago, c.name, sc.nombre, g.factura ";
   $sql .= "FROM gastos g LEFT JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "LEFT JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "LEFT JOIN categories c ON g.categoria = c.id ";
   $sql .= "LEFT JOIN subcategorias sc ON g.subCategoria = sc.idSubCategoria ";
   $sql .= "AND g.categoria = sc.idCategoria WHERE g.fecha BETWEEN '$fechaIni' AND '$fechaFin' ORDER BY g.fecha DESC ";

   return find_by_sql($sql);
}

function altaGasto($descripcion,$precioCompra,$fecha,$proveedor,$sucursal,$tipoPago,$categoria,$iva,$total,$subcategoria,$factura){
   global $db;

   $sql  = "INSERT INTO gastos (";
   $sql .=" id,descripcion,monto,fecha,idProveedor,idSucursal,tipo_pago,categoria,iva,total,subCategoria,factura";
   $sql .=") VALUES (";
   $sql .=" '','{$descripcion}','{$precioCompra}','{$fecha}','{$proveedor}','{$sucursal}',";
   $sql .="'{$tipoPago}','{$categoria}','{$iva}','{$total}','{$subcategoria}','{$factura}'";
   $sql .=")";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaHisEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$usuario,$vendedor,$fechaActual,$horaActual){
   global $db;

   $sql  ="INSERT INTO histefectivo (idHistEfectivo,id_movimiento,cantIni,cantFinal,idSucursal,usuario,";
   $sql .="vendedor,fechaMov,horaMov) VALUES ('','{$movimiento}','{$montoActual}','{$montoFinal}',";
   $sql .="'{$idSucursal}','{$usuario}','{$vendedor}','{$fechaActual}','{$horaActual}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actGasto($descripcion,$precioCompra,$proveedor,$categoria,$subcategoria,$tipoPago,$fecha,$iva,$total,$idGasto,$factura){
   global $db;

   $sql  ="UPDATE gastos SET ";
   $sql .="descripcion ='{$descripcion}',monto ='{$precioCompra}',idProveedor = '{$proveedor}',";
   $sql .="categoria = '{$categoria}',tipo_pago = '{$tipoPago}',fecha = '{$fecha}',";
   $sql .="iva = '{$iva}',total = '{$total}',subCategoria = '{$subcategoria}', factura = '{$factura}' ";
   $sql .="WHERE id ='{$idGasto}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function gastosMAP($prove,$fechaIni,$fechaFin){
   global $db;
  
   $sql  = "SELECT g.fecha,g.descripcion,g.total,p.nom_proveedor,tp.tipo_pago ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "WHERE g.idProveedor =$prove AND g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";
   
   return $db->query($sql);
}

function gastosMesAnio($fechaIni,$fechaFin){
   global $db;

   $sql  = "SELECT g.fecha,g.descripcion,g.total,p.nom_proveedor,tp.tipo_pago,c.name ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "INNER JOIN categories c ON g.categoria = c.id ";
   $sql .= "WHERE g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";

   return $db->query($sql);
}

function totalGastosMAP($prove,$fechaIni,$fechaFin){
   global $db;
  
   $sql  = "SELECT SUM(g.total) as total ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "WHERE g.idProveedor = $prove AND g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";
   
   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function totalGastosMesAnio($fechaIni,$fechaFin){
   global $db;

   $sql  = "SELECT SUM(g.total) as total ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "INNER JOIN categories c ON g.categoria = c.id ";
   $sql .= "WHERE g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function gastosMesAnioTotal($fechaIni,$fechaFin){
  global $db;

  $sql  = "SELECT SUM(g.total) as total ";
  $sql .= "FROM gastos g ";
  $sql .= "WHERE g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

  $query = $db->query($sql);
  if($result = $db->fetch_assoc($query))
     return $result;
  else
     return null;
}

function gastosMAC($categ,$fechaIni,$fechaFin){
   global $db;

   $sql  = " SELECT g.fecha,g.descripcion,g.total,p.nom_proveedor,tp.tipo_pago,c.name ";
   $sql .= "FROM gastos g ";
   $sql .= "INNER JOIN proveedor p ON g.idProveedor = p.idProveedor ";
   $sql .= "INNER JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "INNER JOIN categories c ON g.categoria = c.id ";
   $sql .= "WHERE c.id =$categ AND g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "ORDER BY g.fecha DESC";
  
   return $db->query($sql);
}

function gastosMACTotal($categ,$fechaIni,$fechaFin){
  global $db;
  $sql  = "SELECT SUM(g.total) as total ";
  $sql .= "FROM gastos g ";
  $sql .= "WHERE g.categoria =$categ AND g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

  $query = $db->query($sql);
  if($result = $db->fetch_assoc($query))
     return $result;
  else
     return null;
}

function buscaProdsTempVentas($usuario){
   global $db;

   $sql  ="SELECT t.cve_temporal,t.product_id,p.name,t.qty,t.precio,p.sale_price,p.quantity,p.name, ";
   $sql .="p.cantidadCaja,p.precioCaja,p.id from temporal t,products p ";
   $sql .="where product_id = id and usuario = '$usuario'";

   return $db->query($sql);
}

function altaTemporal($idProducto,$cantidad,$precio,$fecha,$usuario,$idSucursal){
   global $db;

   $sql  ="INSERT INTO temporal (cve_temporal,product_id,qty,precio,fecha,usuario,idSucursal) ";
   $sql .="VALUES ('','$idProducto','$cantidad','$precio','$fecha','$usuario','$idSucursal')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaCancelacion($idProducto,$idSucursal,$usuario,$fecha,$elimina){
   global $db;

   $sql  ="INSERT INTO cancelacion (idcancelacion,idproducto,idsucursal,usuario,date,mensaje) ";
   $sql .="VALUES ('','{$idProducto}','{$idSucursal}','{$usuario}','{$fecha}','{$elimina}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function borraProdTemporal($cveTemporal,$idSucursal){
   global $db;

   $sql ="DELETE FROM temporal WHERE cve_temporal = '$cveTemporal' and idSucursal = '$idSucursal'";

   $db->query($sql);
    
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaProductosVentas($usuario,$idSucursal){
   global $db;

   $sql  ="SELECT a.cve_temporal,a.product_id,SUM(a.qty) AS qty,SUM(a.precio) AS precio,a.fecha,";
   $sql .="a.usuario,a.idSucursal,b.quantity,b.name FROM temporal a,products b WHERE usuario = '$usuario' ";
   $sql .="and b.id = a.product_id and a.idSucursal = '$idSucursal' and b.idSucursal = '$idSucursal' ";
   $sql .="and qty > 0 GROUP BY a.product_id";

   return $db->query($sql);
}

function obtenPuntos($idCliente){
   global $db;

   $sql  ="SELECT SUM(a.price/100) AS venta ,b.nom_cliente FROM cliente b,sales a ";
   $sql .="WHERE a.idCliente = b.idCredencial and a.descuentos = '0' and b.IdCredencial = '$idCliente' ";
   $sql .="GROUP BY b.idCredencial";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function cuentaRegsTemporal($usuario,$idSucursal){
   global $db;

   $sql  ="SELECT COUNT(precio) AS numRegs FROM temporal WHERE usuario = '$usuario' ";
   $sql .="and idSucursal = '$idSucursal' and qty > 0";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function actProdsVentas($resta,$fecha,$idProducto,$idSucursal){
   global $db;

   $sql  ="UPDATE products SET quantity = '$resta',fechaMod = '$fecha' WHERE id = '$idProducto' ";
   $sql .="and idSucursal = '$idSucursal'";
   
   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaVenta($id,$idProducto,$cantidad,$precio,$fecha,$usuario,$idSucursal,$vendedor,$idCliente,$descuentos,$tipoPago,$idTicket,$idCredito,$categoria,$subcategoria){
   global $db;

   $sql  ="INSERT INTO sales(id,product_id,qty,price,date,usuario,idSucursal,vendedor,idCliente,";
   $sql .="descuentos,tipo_pago,id_ticket,idCredito,idCategoria,idSubcategoria) VALUES ('{$id}','{$idProducto}','{$cantidad}',";
   $sql .="'{$precio}','{$fecha}','{$usuario}','{$idSucursal}','{$vendedor}','{$idCliente}',";
   $sql .="'{$descuentos}','{$tipoPago}','{$idTicket}','{$idCredito}','{$categoria}','{$subcategoria}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaTicket($idTicket,$nomProducto,$precio,$cantidad,$totalDesc,$descPorc,$idVenta){
   global $db;

   $sql  = "INSERT INTO tickets(id,id_ticket,nomProducto,precio,cantidad,descPuntos,descPorc,idVenta) ";
   $sql .= "VALUES ('','{$idTicket}','{$nomProducto}','{$precio}','{$cantidad}','{$totalDesc}',";
   $sql .= "'{$descPorc}','{$idVenta}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actDescuentos($idCliente){
   global $db;

   $sql = "UPDATE sales SET descuentos = '1' WHERE idCliente = '$idCliente'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaPago($idTicket,$cantidad,$tipoPago,$fecha,$idSucursal,$credito){
   global $db;

   $sql  ="INSERT INTO pagos(id_pago,id_ticket,cantidad,id_tipo,fecha,id_sucursal,credito) ";
   $sql .="VALUES ('','{$idTicket}','{$cantidad}','{$tipoPago}','{$fecha}','{$idSucursal}','{$credito}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaFolio($idTicket){
   global $db;

   $sql = "INSERT INTO folio(id_folio,dato) VALUES ('','$idTicket')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaProdsTicket($usuario,$idSucursal){
   global $db;

   $sql  ="SELECT @i := @i + 1 as contador,t.cve_temporal,t.product_id,p.name,SUM(t.qty) AS qty,";
   $sql .="SUM(t.precio) AS precio,t.usuario,t.precio AS PU from temporal t,";
   $sql .="products p cross join (select @i := 0) p where t.product_id = p.id and t.usuario = '$usuario' ";
   $sql .="and t.idSucursal = '$idSucursal' and p.idSucursal = '$idSucursal' GROUP BY t.product_id ";
   $sql .="ORDER BY cve_temporal";

   return $db->query($sql);
}

function venta($fechaInicio,$fechaFinal){
   global $db;

   $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name,s.vendedor,s.id_ticket,s.tipo_pago,s.idCliente ";
   $sql .= "FROM sales s ";
   $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
   $sql .= "WHERE s.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' ";
   $sql .= "ORDER BY s.date DESC,s.id DESC";

   return find_by_sql($sql);
}

function ventas($ticket){
   global $db;
   
   $sql  ="SELECT s.id,s.qty,s.price,s.tipo_pago,p.name ";
   $sql .="FROM sales s,products p WHERE id_ticket = '$ticket' ";
   $sql .="AND s.product_id = p.id ";
   $sql .="ORDER BY id DESC";
   
   return find_by_sql($sql);
}

function tipoPago($ticket){
   global $db;

   $sql  ="SELECT p.cantidad,t.tipo_pago ";
   $sql .="FROM pagos p,tipo_pago t WHERE p.id_ticket = '$ticket' AND p.id_tipo = t.id_pago ";
   $sql .="ORDER BY p.id_tipo";
   
   return find_by_sql($sql);
}

function buscaClienteTicket($idTicket){
   global $db;

   $sql ="SELECT date,usuario,idCliente FROM sales WHERE id_ticket = '$idTicket' LIMIT 1";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function buscaProductosTicket($idTicket){
  global $db;

  $sql  ="SELECT @i := @i + 1 as contador,t.nomProducto,t.precio,t.cantidad,t.descPuntos,t.descPorc ";
  $sql .="FROM tickets t cross join (select @i := 0) t where t.id_ticket = '$idTicket'";

  return find_by_sql($sql);
}

function buscaPagosSucursal($idTicket,$idSucursal){
   global $db;

   $sql = "SELECT * FROM pagos WHERE id_ticket = '$idTicket' and id_sucursal = '$idSucursal'";
 
   return find_by_sql($sql); 
}

function tipoPagoTTP($ticket,$tipo){
  global $db;

  $query  ="SELECT p.cantidad,t.tipo_pago,p.id_pago,p.cantidad ";
  $query .="FROM pagos p,tipo_pago t WHERE p.id_ticket = '$ticket' AND p.id_tipo = '$tipo' ";
  $query .="AND t.id_pago = '$tipo' ";

  $sql = $db->query($query);

  if($result = $db->fetch_assoc($sql))
     return $result;
  else
     return null;
}

function actVentaPrecioFecha($precio,$fecha,$id){
   global $db;

   $sql = "UPDATE sales SET price = '{$precio}',date = '{$fecha}' WHERE id ='{$id}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actRegistroPorCampo($tabla,$aActualizar,$nuevoValor,$campo,$valor){
   global $db;

   $sql = "UPDATE $tabla SET $aActualizar = '$nuevoValor' WHERE $campo = '$valor'";   

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actCantFechaPagos($cantidad,$fecha,$idTicket,$idTipo){
   global $db;

   $sql  ="UPDATE pagos SET cantidad = '{$cantidad}',fecha = '{$fecha}' ";
   $sql .="WHERE id_ticket = '{$idTicket}' AND id_tipo = '{$idTipo}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function borraPagoTicketTipo($idTicket,$idTipo){
   global $db;

   $sql = "DELETE FROM pagos WHERE id_ticket = '$idTicket' AND id_tipo = '$idTipo'";   

   $db->query($sql);
    
   return ($db->affected_rows() === 1) ? true : false;
}

function actProdIdSucursal($cantidad,$fecha,$idProducto,$idSucursal){
   global $db;

   $sql  ="UPDATE products SET quantity = '$cantidad',fechaMod = '$fecha' WHERE id = '$idProducto' ";
   $sql .="and idSucursal = '$idSucursal'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaCuenta($nomCliente,$total,$idCliente,$idProducto,$cantidad,$totalVenta,$idCredito,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO cuenta(id,cliente,total,idCredencial,productId,cantidad,totalVenta,idCredito,";
   $sql .="fecha,hora) VALUES ('','{$nomCliente}','{$total}','{$idCliente}','{$idProducto}','{$cantidad}',";
   $sql .="'{$totalVenta}','{$idCredito}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function apartadosCliente(){
  global $db;

  $sql  ="SELECT SUM(total) AS monto,cliente,idCredencial,fecha ";
  $sql .="FROM cuenta WHERE total > 0 and pagado = 0 ";
  $sql .="GROUP BY idCredencial ";
  $sql .="ORDER BY cliente ASC";

  return $db->query($sql);
}

function sumApartadosXCliente($idCredencial){
  global $db;

  $sql  ="SELECT SUM(total) AS monto,cliente,idCredencial ";
  $sql .="FROM cuenta WHERE idCredencial = '$idCredencial' AND pagado = 0 ";
  $sql .="GROUP BY idCredencial ";

  $result = $db->query($sql);

  if($db->num_rows($result)){
    $apartado = $db->fetch_assoc($result);
    return $apartado;
  }
    return false;
}

function apartadosXCliente($idCredencial){
   global $db;

   $sql ="SELECT * FROM cuenta WHERE idCredencial = '$idCredencial' AND pagado = 0";

   return $db->query($sql);
}

function buscaProdsCredito($idSucursal,$idCredencial){
   global $db;

   $sql  ="SELECT @i := @i + 1 as contador, c.total,p.name from cuenta c,products p ";
   $sql .="cross join (select @i := 0) p where c.productId = p.id and p.idSucursal = '$idSucursal' ";
   $sql .="and c.idCredencial = '$idCredencial' and c.pagado = '0' and c.total > 0";

   return $db->query($sql);
}

function actCuenta($total,$pagado,$idCuenta){
   global $db;

   $sql ="UPDATE cuenta SET total = '$total',pagado = '$pagado' WHERE id = '$idCuenta'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaHisCredito($idCliente,$abono,$idSucursal,$nomCliente,$fecha,$hora,$pagado,$idTicket){
   global $db;

   $sql  ="INSERT INTO histcredito(idHistCredito,idCliente,pago,idSucursal,cliente,fechaPago,horaPago,";
   $sql .="pagado,id_ticket) VALUES ('','{$idCliente}','{$abono}','{$idSucursal}','{$nomCliente}',";
   $sql .="'{$fecha}','{$hora}','{$pagado}','{$idTicket}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function detApartadoXCliente($idCredencial){
   global $db;
  
   $sql  ="SELECT c.id,c.total,c.cantidad,c.totalVenta,p.name FROM cuenta c,products p ";
   $sql .="WHERE p.id = c.productId AND c.idCredencial = '$idCredencial' AND pagado = 0";
  
   return $db->query($sql);
}

function histCredito($idCliente){
   global $db;

   $sql ="SELECT * FROM histcredito WHERE idCliente = $idCliente AND pagado = '0'";
    
   return find_by_sql($sql);
}

function buscaPagoCredito($idTicket,$idSucursal){
   global $db;

   $sql = "SELECT * FROM pagos WHERE id_ticket = '$idTicket' and id_sucursal = '$idSucursal'";
 
   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function buscaRegsPorCampo($tabla,$campo,$valor){
   global $db;

   $sql = "SELECT * FROM $tabla WHERE $campo = $valor";

   $result = $db->query($sql);

   return $result;
}

function buscaCredito($idProducto,$idCredito,$idCliente){
   global $db;

   $sql  ="SELECT * FROM cuenta WHERE productId = '$idProducto' AND idCredito = '$idCredito' ";
   $sql .="AND idCredencial = '$idCliente'";

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date,$end_date){
   global $db;
  
   $start_date  = date("Y-m-d", strtotime($start_date));
   $end_date    = date("Y-m-d", strtotime($end_date));

   $sql  ="SELECT s.date, p.name,p.sale_price,p.buy_price,";
   $sql .="COUNT(s.product_id) AS total_records,";
   $sql .="SUM(s.qty) AS total_sales,";
   $sql .="SUM(p.sale_price * s.qty) AS total_saleing_price,";
   $sql .="SUM(p.buy_price * s.qty) AS total_buying_price ";
   $sql .="FROM sales s ";
   $sql .="LEFT JOIN products p ON s.product_id = p.id";
   $sql .=" WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
   $sql .=" GROUP BY DATE(s.date),p.name";
   $sql .=" ORDER BY DATE(s.date) DESC";
  
   return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates_suc($start_date,$end_date,$sucursal){
   global $db;
   
   $start_date  = date("Y-m-d", strtotime($start_date));
   $end_date    = date("Y-m-d", strtotime($end_date));
   
   $sql  ="SELECT s.date, p.name,p.sale_price,p.buy_price,";
   $sql .="COUNT(s.product_id) AS total_records,";
   $sql .="SUM(s.qty) AS total_sales,";
   $sql .="SUM(p.sale_price * s.qty) AS total_saleing_price,";
   $sql .="SUM(p.buy_price * s.qty) AS total_buying_price ";
   $sql .="FROM sales s ";
   $sql .="LEFT JOIN products p ON s.product_id = p.id";
   $sql .=" WHERE s.idSucursal='$sucursal' and s.date BETWEEN '{$start_date}' AND '{$end_date}'";
   $sql .=" GROUP BY DATE(s.date),p.name";
   $sql .=" ORDER BY DATE(s.date) DESC";
   
   return $db->query($sql);
}

// function ventasCatTotal($categ,$fechaIni,$fechaFin){
//    global $db;

//    $sql  ="SELECT SUM(s.price) as total,SUM(s.qty) as cantidad ";
//    $sql .="FROM sales s,products p,categories c ";
//    $sql .="WHERE p.categorie_id = $categ AND p.categorie_id = c.id AND s.product_id = p.id ";
//    $sql .="AND s.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

//    $query = $db->query($sql);
   
//    if($result = $db->fetch_assoc($query))
//       return $result;
//    else
//       return null;
// }

function ventasCatTotal($categ,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(s.price) as total,SUM(s.qty) as cantidad ";
   $sql .="FROM sales s,products p,categories c ";
   $sql .="WHERE p.categorie_id = $categ AND p.categorie_id = c.id AND s.product_id = p.id ";
   $sql .="AND s.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

   $query = $db->query($sql);
   
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function gastosCatTotal($categ,$fechaIni,$fechaFin){
  global $db;

  $query  ="SELECT SUM(total) AS total FROM gastos WHERE categoria = $categ ";
  $query .="AND fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}'";
  
  $sql = $db->query($query);

  if($result = $db->fetch_assoc($sql))
     return $result;
  else
     return null;
}

function monthlycatsuc($sucursal,$fechaIni,$fechaFin){
  global $db;

  $sql  ="SELECT a.name, ";
  $sql .="SUM(c.qty) as cantidad,";
  $sql .="SUM(c.price-b.buy_price * c.qty) AS ganancia, ";
  $sql .="SUM(b.sale_price * c.qty) AS precio_total  ";
  $sql .="FROM categories a, products b, sales c ";
  $sql .="WHERE b.categorie_id = a.id AND c.product_id= b.id ";
  $sql .="AND b.idSucursal = '$sucursal' AND c.idSucursal = '$sucursal' ";
  $sql .="AND c.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
  $sql .="GROUP BY (a.name)";

  return $db->query($sql);
}

// function monthlycat1($fechaIni, $fechaFin){
//    global $db;

//    $sql  = "SELECT a.name,d.nombre,a.id, ";
//    $sql .= "SUM(c.qty) as cantidad, ";
//    $sql .= "SUM(c.price-b.buy_price * c.qty) AS ganancia, ";
//    $sql .= "SUM(b.sale_price * c.qty) AS precio_total ";
//    $sql .= "FROM categories a, products b, sales c, subcategorias d ";
//    $sql .= "WHERE b.categorie_id = a.id AND c.product_id = b.id AND ";
//    $sql .= "c.idCategoria = d.idCategoria AND c.date ";
//    $sql .= "BETWEEN '{$fechaIni}' AND '{$fechaFin}' GROUP BY (a.name)";

//    return $db->query($sql);
// }

function monthlycat1($fechaIni, $fechaFin){
   global $db;

   $sql  = "SELECT c.name AS categoria, sc.nombre AS subCat, COALESCE(s.cantVentas,0) AS cantVentas, ";
   $sql .= "COALESCE(s.ventas, 0) AS ventas, COALESCE(g.gasto, 0) AS gasto, COALESCE(s.ventas, 0) - COALESCE(g.gasto, 0) AS ganancia ";
   $sql .= "FROM categories c LEFT JOIN subcategorias sc ON c.id = sc.idCategoria LEFT JOIN ( SELECT categoria, ";
   $sql .= "subCategoria, SUM(total) AS gasto FROM gastos WHERE fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .= "GROUP BY categoria, subCategoria ) g ON g.categoria = c.id AND ";
   $sql .= "(g.subCategoria = sc.idSubCategoria OR (g.subCategoria = 0 AND sc.idSubCategoria IS NULL)) LEFT JOIN ( ";
   $sql .= "SELECT idCategoria, idSubCategoria, COUNT(qty) AS cantVentas, SUM(price) AS ventas FROM sales WHERE ";
   $sql .= "date BETWEEN '{$fechaIni}' AND '{$fechaFin}' GROUP BY idCategoria, idSubCategoria ) s ON ";
   $sql .= "s.idCategoria = c.id AND (s.idSubCategoria = sc.idSubCategoria OR (s.idSubCategoria = 0 AND sc.idSubCategoria IS NULL)) ";
   $sql .= "HAVING (ventas OR gasto) <> 0";

   return $db->query($sql);
}

function monthlycateg1($fechaIni, $fechaFin, $categoria){
   global $db;

   $sql  = "SELECT c.name AS categoria, sc.nombre AS subCat, COALESCE(s.cantVentas,0) AS cantVentas, COALESCE(s.ventas, 0) AS ventas, ";
   $sql .= "COALESCE(g.gasto, 0) AS gasto, COALESCE(s.ventas, 0) - COALESCE(g.gasto, 0) AS ganancia FROM categories c ";
   $sql .= "LEFT JOIN subcategorias sc ON c.id = sc.idCategoria LEFT JOIN (SELECT categoria, subCategoria, SUM(total) AS gasto ";
   $sql .= "FROM gastos WHERE fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' AND categoria = '{$categoria}' GROUP BY ";
   $sql .= "categoria, subCategoria) g ON g.categoria = c.id AND (g.subCategoria = sc.idSubCategoria OR (g.subCategoria = 0 ";
   $sql .= "AND sc.idSubCategoria IS NULL)) LEFT JOIN (SELECT idCategoria, idSubCategoria, COUNT(qty) AS cantVentas, ";
   $sql .= "SUM(price) AS ventas FROM sales WHERE date BETWEEN '{$fechaIni}' AND '{$fechaFin}' AND idCategoria = '{$categoria}' ";
   $sql .= "GROUP BY idCategoria, idSubCategoria) s ON s.idCategoria = c.id AND (s.idSubCategoria = sc.idSubCategoria OR ";
   $sql .= "(s.idSubCategoria = 0 AND sc.idSubCategoria IS NULL)) HAVING (ventas OR gasto) <> 0";

   return $db->query($sql);
}

function monthlySubcateg1($fechaIni, $fechaFin, $categoria, $subCat){
   global $db;

   $sql  = "SELECT c.name AS categoria, sc.nombre AS subCat, COALESCE(s.cantVentas,0) AS cantVentas, COALESCE(s.ventas, 0) AS ventas, ";
   $sql .= "COALESCE(g.gasto, 0) AS gasto, COALESCE(s.ventas, 0) - COALESCE(g.gasto, 0) AS ganancia FROM categories c ";
   $sql .= "LEFT JOIN subcategorias sc ON c.id = sc.idCategoria LEFT JOIN (SELECT categoria, subCategoria, SUM(total) AS gasto ";
   $sql .= "FROM gastos WHERE fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' AND categoria = '{$categoria}' AND ";
   $sql .= "subCategoria = '{$subCat}' GROUP BY categoria, subCategoria) g ON g.categoria = c.id AND ";
   $sql .= "(g.subCategoria = sc.idSubCategoria OR (g.subCategoria = 0 AND sc.idSubCategoria IS NULL)) LEFT JOIN (SELECT ";
   $sql .= "idCategoria, idSubCategoria, COUNT(qty) AS cantVentas, SUM(price) AS ventas FROM sales WHERE ";
   $sql .= "date BETWEEN '{$fechaIni}' AND '{$fechaFin}' AND idCategoria = '{$categoria}' AND idSubCategoria = '{$subCat}' ";
   $sql .= "GROUP BY idCategoria, idSubCategoria) s ON s.idCategoria = c.id AND (s.idSubCategoria = sc.idSubCategoria OR ";
   $sql .= "(s.idSubCategoria = 0 AND sc.idSubCategoria IS NULL)) HAVING (ventas OR gasto) <> 0";

   return $db->query($sql);
}

function ventasPeriodoSuc($sucursal,$fechaIni,$fechaFin){
   global $db;

   $query  ="SELECT SUM(v.price) AS totalVentas,s.nom_sucursal FROM sales v,sucursal s ";
   $query .="WHERE v.idSucursal = '$sucursal' AND v.idSucursal = s.idSucursal ";
   $query .="AND v.date BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function gastosPeriodoSuc($sucursal,$fechaIni,$fechaFin){
   global $db;

   $query  ="SELECT SUM(g.total) AS total,s.nom_sucursal FROM gastos g,sucursal s ";
   $query .="WHERE g.idSucursal = '$sucursal' AND g.idSucursal = s.idSucursal ";
   $query .="AND g.fecha BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function ventasPeriodo($fechaIni,$fechaFin){
   global $db;

   $query  = "SELECT SUM(v.price) AS totalVentas,s.nom_sucursal FROM sales v,sucursal s ";
   $query .="WHERE v.idSucursal = s.idSucursal AND v.date BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function gastosPeriodo($fechaIni,$fechaFin){
   global $db;

   $query  ="SELECT SUM(g.total) AS total,s.nom_sucursal FROM gastos g,sucursal s ";
   $query .="WHERE g.idSucursal = s.idSucursal AND g.fecha BETWEEN '$fechaIni' AND '$fechaFin'";

   $sql = $db->query($query);

   if($result = $db->fetch_assoc($sql))
      return $result;
   else
      return null;
}

function ySalesSucFecha($sucursal,$fechaIni,$fechaFin){
  global $db;

  $sql  = "SELECT a.qty, a.price, a.date,c.nom_sucursal,";
  $sql .= "SUM(a.price) AS total_ventas ";
  $sql .= "FROM sales a, sucursal c ";
  $sql .= "WHERE a.idSucursal = '$sucursal' AND c.idsucursal='$sucursal' ";
  $sql .= "AND a.date BETWEEN '{$fechaIni}' AND '{$fechaFin}'";  
  $sql .= "GROUP BY month(a.date) ";
  $sql .= "ORDER BY a.date ASC";
  
  return $db->query($sql);
}

function ySalesFecha($fechaIni,$fechaFin){
  global $db;

  $sql = "SELECT a.qty, a.price, a.date,c.nom_sucursal, ";
  $sql .= "SUM(a.price) AS total_ventas ";
  $sql .= "FROM sales a,sucursal c ";
  $sql .= "WHERE a.idsucursal = c.idsucursal AND a.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
  $sql .= "GROUP BY month(a.date) ";
  $sql .= "ORDER BY a.date ASC";

  return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function dailySales($fecha){
   global $db;
 
   $fecha = date("Y/m/d", strtotime($fecha));

   $sql  ="SELECT b.name, a.qty, a.price, a.date, ";
   $sql .="SUM(a.qty) AS total_ventas, ";
   $sql .="SUM(a.price-b.buy_price * a.qty) AS ganancia, ";
   $sql .="SUM(a.price - 0 * a.qty) AS precio_total ";
   $sql .="FROM sales a, products b ";
   $sql .="WHERE a.product_id = b.id AND a.date='$fecha' ";
   $sql .="GROUP BY DATE(a.date),b.name";  
   
   return $db->query($sql);
}

/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function dailySalesSuc($fecha,$sucursal){
   global $db;
 
   $fecha = date("Y/m/d", strtotime($fecha));

   $sql  ="SELECT b.name, a.qty, a.price, a.date, ";
   $sql .="SUM(a.qty) AS total_ventas, ";
   $sql .="SUM(a.price-b.buy_price * a.qty) AS ganancia, ";
   $sql .="SUM(a.price - 0 * a.qty) AS precio_total ";
   $sql .="FROM sales a, products b ";
   $sql .="WHERE a.product_id = b.id AND a.date='$fecha' ";
   $sql .="AND a.idSucursal = '$sucursal' AND b.idSucursal = '$sucursal' ";
   $sql .="GROUP BY DATE(a.date),b.name";  
  
   return $db->query($sql);
}

function ventaDiaSuc($fecha,$idSucursal){
   global $db;

   $sql  ="SELECT SUM(price) as venta FROM sales WHERE date = '$fecha' and idSucursal = '$idSucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function ventaDia($fecha){
   global $db;

   $sql ="SELECT SUM(price) as venta FROM sales WHERE date='$fecha'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function gananciaDiaSuc($fecha,$idsucursal){
   global $db;

   $sql  ="SELECT SUM(b.price-(b.qty*a.buy_price)) as ganancia FROM products a,sales b ";
   $sql .="WHERE b.product_id = a.id and b.date = '$fecha' and a.idSucursal = '$idsucursal' ";
   $sql .="and b.idSucursal = '$idsucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function ganaciaDia($fecha){
   global $db;

   $sql  ="SELECT SUM(b.price-(b.qty*a.buy_price)) as ganancia FROM products a,sales b ";
   $sql .="WHERE b.product_id = a.id and b.date = '$fecha'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function corteVendedor($vendedor){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a, sucursal b ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.vendedor = '$vendedor' Group BY a.vendedor,a.date ";
   $sql .="ORDER BY a.date DESC";

   return find_by_sql($sql);
}

function corte(){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a, sucursal b ";
   $sql .="WHERE b.idSucursal = a.idSucursal Group BY a.vendedor,a.date ";
   $sql .="ORDER BY a.date DESC ";

   return find_by_sql($sql);
}

function corteDiaVendedor($vendedor,$fecha){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a, sucursal b ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.vendedor = '$vendedor' and a.date = '$fecha' ";
   $sql .="Group BY a.vendedor,a.date ORDER BY a.date DESC";

   return find_by_sql($sql);
}

function corteDia($fecha){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a, sucursal b ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.date = '$fecha' Group BY a.vendedor ";
   $sql .="ORDER BY a.vendedor";

   return find_by_sql($sql);
}

function cortePeriodoVen($encargado,$fechaInicio,$fechaFinal){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a, sucursal b ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.vendedor = '$encargado' ";
   $sql .="and a.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' Group BY a.vendedor ";
   $sql .="ORDER BY a.date DESC";

   return find_by_sql($sql);
}

function cortePeriodo($fechaInicio,$fechaFinal){
   global $db;

   $sql  ="SELECT SUM(a.price) AS venta,a.vendedor,a.date,b.nom_sucursal ";
   $sql .="FROM sales a, sucursal b ";
   $sql .="WHERE b.idSucursal = a.idSucursal and a.date BETWEEN '{$fechaInicio}' AND '{$fechaFinal}' ";
   $sql .="Group BY a.vendedor ORDER BY a.vendedor";

   return find_by_sql($sql);
}

function alertaProductos($idSucursal){
   global $db;

   $sql  ="SELECT a.name,a.quantity,b.nom_sucursal FROM products a,sucursal b ";
   $sql .="WHERE a.idSucursal = '$idSucursal' and b.idSucursal = '$idSucursal' and a.quantity <= '1' ";
   $sql .="order by a.quantity";

   return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/
function count_by_id($table){
   global $db;

   if(tableExists($table)){
      $sql ="SELECT COUNT(id) AS total FROM ".$db->escape($table);
      
      $result = $db->query($sql);
      
      return($db->fetch_assoc($result));
   }
}

function count_su_id($table){
   global $db;
  
   if(tableExists($table)){
      $sql ="SELECT COUNT(idSucursal) AS total FROM ".$db->escape($table);

      $result = $db->query($sql);

      return($db->fetch_assoc($result));
   }
}

function saldoEfectivoDia($fecha,$idSucursal,$movimiento){
   global $db;

   $sql  ="SELECT SUM(cantIni-cantFinal) as total FROM histefectivo WHERE id_movimiento = '$movimiento' ";
   $sql .="AND fechaMov = '$fecha' AND idsucursal = '$idSucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function pagoPeriodoPortipo($idSucursal,$tipoPago,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(cantidad) as total FROM pagos WHERE id_tipo = '$tipoPago' ";
   $sql .="and id_sucursal = '$idSucursal' and credito = '0' and fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}   

function gastoPeriodoPortipo($idSucursal,$tipoPago,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(total) AS total from gastos where tipo_pago = '$tipoPago' ";
   $sql .="and idSucursal = '$idSucursal' and fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}   

function mejoresClientes($idSucursal){
   global $db;

   $sql  ="SELECT b.nom_cliente,a.idCliente,SUM(a.price) AS venta,SUM(a.price/100) AS puntos ";
   $sql .="FROM sales a,cliente b WHERE a.IdCliente = b.IdCredencial and a.idSucursal = '$idSucursal' ";
   $sql .="GROUP BY a.idCliente ORDER BY venta DESC LIMIT 10";

   return find_by_sql($sql);
}

function nuevosClientes(){
   global $db;

   $sql  ="SELECT a.nom_cliente,a.idcredencial,a.correo FROM cliente a GROUP BY a.idcredencial ";
   $sql .="ORDER BY a.date DESC LIMIT 10";

   return find_by_sql($sql);
}

function altaMascota($nombre,$especie,$raza,$color,$alimento,$sexo,$estado,$fechaNac,$idCliente,$foto,$peso){
   global $db;

   $sql  ="INSERT INTO Mascotas(idMascotas,nombre,especie,raza,Color,alimento,sexo,estado,";
   $sql .="fecha_nacimiento,idCliente,foto,peso) VALUES ('','{$nombre}','{$especie}','{$raza}',";
   $sql .="'{$color}','{$alimento}','{$sexo}','{$estado}','{$fechaNac}','{$idCliente}',";
   $sql .="'{$foto}','{$peso}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function histEstancia(){
   global $db;

    $sql  ="SELECT c.nom_cliente,c.tel_cliente,a.idHistEstancia,a.estatus,a.responsable,a.hora,a.fecha,";
    $sql .="a.fechaSalida,b.nombre FROM histEstancia a,Mascotas b,cliente c ";
    $sql .="WHERE a.idMascota = b.idMascotas and c.idcredencial = b.idCliente order by fecha DESC";

    return find_by_sql($sql);
}   

function histEstanciaMasc($mascota){
   global $db;

   $sql  ="SELECT c.nom_cliente,c.tel_cliente,a.idHistEstancia,a.estatus,a.responsable,a.hora,a.fecha,";
   $sql .="a.fechaSalida,b.nombre FROM histEstancia a,Mascotas b,cliente c ";
   $sql .="WHERE a.idMascota = b.idMascotas and c.idcredencial = b.idCliente ";
   $sql .="AND b.nombre like '%$mascota%' order by fecha DESC";

   return find_by_sql($sql);
}   

function histEsteticaVendSuc($vendedor,$idSucursal){
   global $db;

   $sql  ="SELECT h.vendedor,h.ventaLV,h.ventaSD,h.comisionLV,h.comisionSD,h.fecha,h.hora,p.name ";
   $sql .="FROM histestetica h,products p,sucursal s,users u ";
   $sql .="WHERE h.idProducto = p.id AND h.idSucursal = '$idSucursal' ";
   $sql .="AND h.vendedor = '$vendedor' AND h.idUsuario = u.id ORDER BY h.idHistEstetica DESC";

   return find_by_sql($sql);
}

function histEsteticaSuc($idSucursal){
   global $db;

   $sql  ="SELECT h.vendedor,h.ventaLV,h.ventaSD,h.comisionLV,h.comisionSD,h.fecha,h.hora,p.name ";
   $sql .="FROM histestetica h,products p,sucursal s,users u ";
   $sql .="WHERE h.idProducto = p.id AND h.idSucursal = '$idSucursal' ";
   $sql .="AND h.idUsuario = u.id AND h.idUsuario = u.id ORDER BY h.idHistEstetica DESC";

   return find_by_sql($sql);
}

function histEstetica(){
   global $db;

   $sql  ="SELECT h.vendedor,h.ventaLV,h.ventaSD,h.comisionLV,h.comisionSD,h.fecha,h.hora,p.name ";
   $sql .="FROM histestetica h,products p,sucursal s,users u ";
   $sql .="WHERE h.idProducto = p.id AND h.idSucursal = s.idSucursal ";
   $sql .="AND h.idUsuario = u.id ORDER BY h.fecha DESC,h.hora DESC";

   return find_by_sql($sql);
}   

function histEsteticaVend($vendedor){
   global $db;

   $sql  ="SELECT h.vendedor,h.ventaLV,h.ventaSD,h.comisionLV,h.comisionSD,h.fecha,h.hora,p.name ";
   $sql .="FROM histestetica h,products p,sucursal s,users u ";
   $sql .="WHERE h.idProducto = p.id AND h.idSucursal = s.idSucursal ";
   $sql .="AND h.vendedor = '$vendedor' AND h.idUsuario = u.id ORDER BY h.idHistEstetica DESC";
    
   return find_by_sql($sql);
}

function mascotas(){
   global $db;

   $sql  ="SELECT a.idMascotas,a.nombre,b.nom_cliente FROM cliente b,Mascotas a ";
   $sql .="WHERE a.idCliente = b.idcredencial LIMIT 30";

   return find_by_sql($sql);
}

function buscaMascotaCliente($idCliente){
   global $db;

   $sql  ="SELECT a.idMascotas,a.nombre,b.nom_cliente FROM cliente b,Mascotas a ";
   $sql .="WHERE a.idCliente = b.idcredencial and a.idcliente = '$idCliente' LIMIT 30";

   return find_by_sql($sql);
}

function buscaMascotaNombre($nombre){
   global $db;

   $sql  ="SELECT a.idMascotas,a.nombre,b.nom_cliente FROM cliente b,Mascotas a ";
   $sql .="WHERE a.idCliente = b.idcredencial and a.nombre like '%$nombre%' LIMIT 30";

   return find_by_sql($sql);
}

function actMascota($nombre,$especie,$raza,$color,$alimento,$sexo,$estado,$fechaNac,$peso,$foto,$idMascota){
   global $db;

   $sql  ="UPDATE Mascotas SET nombre ='{$nombre}',especie = '{$especie}',raza = '{$raza}',";
   $sql .="Color = '{$color}',alimento = '{$alimento}',sexo = '{$sexo}',estado = '{$estado}',";
   $sql .="fecha_nacimiento = '{$fechaNac}',peso = '{$peso}'";

   if ($foto != "")
      $sql .=",foto = '{$foto}' WHERE idMascotas ='{$idMascota}'";
   else
      $sql .="WHERE idMascotas ='{$idMascota}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function buscaEstadoNombMasc($idMascota){
   global $db;

   $sql  ="SELECT a.estatus,b.nombre FROM estancia a,Mascotas b WHERE a.idMascota = $idMascota ";
   $sql .="AND b.idMascotas = $idMascota";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function buscaClienteMascota($idMascota){
   global $db;

   $sql  ="SELECT b.idcredencial,b.nom_cliente,b.dir_cliente,b.tel_cliente,b.correo,a.nombre, ";
   $sql .="a.especie,a.raza,a.Color,a.alimento,a.sexo,a.estado,a.fecha_nacimiento,a.foto ";
   $sql .="FROM cliente b,Mascotas a WHERE a.idCliente = b.idcredencial and a.idMascotas = $idMascota";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function buscaConsultas($idMascota){
  global $db;

  $sql  ="SELECT consulta,idconsulta,diagnostico,problema,peso,fecha FROM Consulta ";
  $sql .="WHERE idMascota = $idMascota order by fecha DESC";

  return find_by_sql($sql);
}

function buscaEsteticas($idMascota){
   global $db;

   $sql  ="SELECT idestetica,fecha,observaciones FROM estetica WHERE idMascota = $idMascota ";
   $sql .="ORDER BY fecha DESC LIMIT 10 ";

   return find_by_sql($sql);
}

function buscaVacunas($idMascota){
   global $db;

   $sql ="SELECT idvacuna,fecha,vacuna FROM vacuna WHERE idMascota = $idMascota ";
   $sql.="ORDER BY fecha DESC LIMIT 10";

   return find_by_sql($sql);
}

function buscaDesparasitaciones($idMascota){
   global $db;

   $sql  ="SELECT id_desparasitante,fecha,desparasitante,nota FROM desparasitacion ";
   $sql .="WHERE idMascota = $idMascota ORDER BY fecha DESC LIMIT 10";

   return $db->query($sql);
}

function buscaEstudios($idMascota){
   global $db;

   $sql ="SELECT * FROM files WHERE idMas = $idMascota order by fecha DESC LIMIT 10";

   return find_by_sql($sql);  
}

function buscaConsultasAsc($idMascota){
   global $db;

   $sql  ="SELECT consulta,diagnostico,problema,peso,temperatura,fecha FROM Consulta ";
   $sql .="WHERE idMascota = $idMascota order by fecha ASC";

   return $db->query($sql);
}

function buscaVacunasAsc($idMascota){
   global $db;

   $sql  ="SELECT fecha,vacuna,nota FROM vacuna WHERE idMascota = $idMascota order by fecha ASC LIMIT 10";

   return $db->query($sql);
}

function altaConsulta($receta,$diagnostico,$problema,$peso,$temperatura,$idMascota,$fecha,$costo,$usuario,$Nota){
   global $db;

   $sql  ="INSERT INTO Consulta (idconsulta,consulta,diagnostico,problema,peso,temperatura,idMascota,";
   $sql .="fecha,costo,responsable,nota) VALUES ('','{$receta}','{$diagnostico}','{$problema}','{$peso}',";
   $sql .="'{$temperatura}','{$idMascota}','{$fecha}','{$costo}','{$usuario}','{$Nota}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaEstetica($responsable,$observaciones,$costo,$hora,$fecha,$idMascota,$fechaSigEstetica){
   global $db;

   $sql  ="INSERT INTO estetica (idestetica,responsable,observaciones,costo,hora,fecha,idMascota,";
   $sql .="fechaSigEstetica) VALUES ('','{$responsable}','{$observaciones}','{$costo}','{$hora}',";
   $sql .="'{$fecha}','{$idMascota}','{$fechaSigEstetica}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaEstancia($idMascota,$estatus,$costo,$responsable,$hora,$fechaSalida,$fechaEntrada,$nota){
   global $db;

   $sql  ="INSERT INTO estancia (idEstancia,idMascota,estatus,costo,Encargado,Hora_salida,fecha_salida,";
   $sql .="fecha_entrada,nota) VALUES('','$idMascota','{$estatus}','{$costo}','{$responsable}','{$hora}',";
   $sql .="'{$fechaSalida}','{$fechaEntrada}','{$nota}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaHistEstancia($idMascota,$estatus,$responsable,$fecha,$hora,$fechaSalida){
   global $db;

   $sql  ="INSERT INTO histEstancia (idHistEstancia,idMascota,estatus,responsable,fecha,hora,fechaSalida";
   $sql .=") VALUES ('','{$idMascota}','{$estatus}','{$responsable}','{$fecha}','{$hora}','{$fechaSalida}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaVacuna($responsable,$observaciones,$costo,$fecha,$idMascota,$fechaCaducidad,$fechaVacuna,$nota){
   global $db;

   $sql  ="INSERT INTO vacuna (idvacuna,responsable,vacuna,costo,fecha,idMascota,fechaCaducidad,";
   $sql .="fechaSigVacuna,nota) VALUES ('','{$responsable}','{$observaciones}','{$costo}',";
   $sql .="'{$fecha}','{$idMascota}','{$fechaCaducidad}','{$fechaVacuna}','{$nota}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaDesparacitacion($responsable,$desparasitante,$costo,$fecha,$idMascota,$fechaCaducidad,$fechaSigDesp,$nota){
   global $db;

   $sql  ="INSERT INTO desparasitacion (id_desparasitante,responsable,desparasitante,costo,fecha,idMascota,";
   $sql .="fechaCaducidad,fechaSigDesp,nota) VALUES ('','{$responsable}','{$desparasitante}','{$costo}',";
   $sql .="'{$fecha}','{$idMascota}','{$fechaCaducidad}','{$fechaSigDesp}','{$nota}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaAplicacion($responsable,$solucion,$cantidad,$fechaActual,$idMascota,$fechaCaducidad,$fechaAplicacion,$nota){
   global $db;

   $sql  ="INSERT INTO aplicacion ( idAplicacion,responsable,solucion,cantidad,fecha,idMascota,";
   $sql .="fechaCaducidad,fechaSigAplicacion,nota) VALUES ('','{$responsable}','{$solucion}','{$cantidad}',";
   $sql .="'{$fechaActual}','{$idMascota}','{$fechaCaducidad}','{$fechaAplicacion}','{$nota}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaEstudio($nombre,$descripcion,$nombreArchivo,$idMascota,$fecha){
   global $db;

   $sql  ="INSERT INTO files(nombre,descripcion,url,idMas,fecha) VALUES ('{$nombre}','{$descripcion}',";
   $sql .="'{$nombreArchivo}','{$idMascota}','{$fecha}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaCita($responsable,$fecha,$hora){
   global $db;

   $sql  ="SELECT id FROM cita WHERE responsable = '$responsable' AND fecha_cita = '$fecha'";
   $sql .="AND hora = '$hora'";   

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function altaCita($idMascota,$responsable,$fechaCita,$horaCita,$nota,$fechaActual,$idSucursal){
   global $db;

   $sql  ="INSERT INTO cita (id,idMascota,responsable,fecha_cita,hora,nota,fecha_solicitud,idSucursal) ";
   $sql .="VALUES ('','{$idMascota}','{$responsable}','{$fechaCita}','{$horaCita}','{$nota}',";
   $sql .="'{$fechaActual}','{$idSucursal}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaCitaEvent($idMascota,$responsable,$fechaCita,$horaCita,$nota,$fechaActual,$idSucursal,$idEvent){
   global $db;

   $sql  ="INSERT INTO cita (id,idMascota,responsable,fecha_cita,hora,nota,fecha_solicitud,idSucursal,idEvent) ";
   $sql .="VALUES ('','{$idMascota}','{$responsable}','{$fechaCita}','{$horaCita}','{$nota}',";
   $sql .="'{$fechaActual}','{$idSucursal}','$idEvent')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function actVacuna($responsable,$vacuna,$fechaCad,$fechaSigVacuna,$nota,$idVacuna){
   global $db;

   $sql  ="UPDATE vacuna SET responsable = '{$responsable}',vacuna = '{$vacuna}',";
   $sql .="fechaCaducidad = '{$fechaCad}',fechaSigVacuna = '{$fechaSigVacuna}',nota ='{$nota}' ";
   $sql .="WHERE idvacuna = '{$idVacuna}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actEstetica($responsable,$nota,$hora,$idEstetica){
   global $db;

   $sql  ="UPDATE estetica SET responsable = '{$responsable}',observaciones = '{$nota}',";
   $sql .="hora ='{$hora}' WHERE idestetica = '{$idEstetica}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function actConsulta($receta,$problema,$temperatura,$peso,$diagnostico,$nota,$fecha,$idConsulta){
   global $db;

   $sql  ="UPDATE Consulta SET consulta = '{$receta}',problema = '{$problema}',";
   $sql .="temperatura = '{$temperatura}',peso = '{$peso}',diagnostico = '{$diagnostico}',";
   $sql .="nota = '{$nota}',fecha = '{$fecha}' WHERE idconsulta = '{$idConsulta}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function citasSucFecha($sucursal,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT m.nombre,c.responsable,c.fecha_cita,c.hora,c.nota,c.id,c.idEvent ";
   $sql .="FROM Mascotas m, cita c ";
   $sql .="WHERE m.idMascotas = c.idMascota AND c.idSucursal='$sucursal' ";
   $sql .="AND c.fecha_cita BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";  
   $sql .="ORDER BY c.fecha_cita ASC";
  
   return $db->query($sql);
}

function citasFecha($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT m.nombre,c.responsable,c.fecha_cita,c.hora,c.nota,c.id,c.idEvent ";
   $sql .="FROM Mascotas m, cita c ";
   $sql .="WHERE m.idMascotas = c.idMascota ";
   $sql .="AND c.fecha_cita BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";  
   $sql .="ORDER BY c.fecha_cita ASC";

   return $db->query($sql);
}

function actCita($responsable,$fechaCita,$nota,$hora,$fechaActual,$idCita,$idEvent){
   global $db;

   $sql = "UPDATE cita SET responsable = '{$responsable}',fecha_cita = '{$fechaCita}',nota = '{$nota}',";
   $sql.= "hora = '{$hora}',fecha_solicitud = '{$fechaActual}' ";
   
   if ($idEvent != '')
      $sql .= " ,idEvent = '{$idEvent}' ";
   
   $sql.= "WHERE id = '{$idCita}'";
   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function buscaEstancias(){
   global $db;

   $sql  ="SELECT c.idcredencial,c.nom_cliente,c.tel_cliente,a.idestancia,a.estatus,a.costo,a.Encargado,";
   $sql .="a.Hora_salida,a.fecha_salida,b.nombre FROM estancia a,Mascotas b,cliente c ";
   $sql .="where a.idMascota = b.idMascotas and c.idcredencial = b.idCliente order by fecha_salida DESC";

   return find_by_sql($sql);
}

function buscaSoluciones($nombre){
   global $db;
   
   $sql ="SELECT * FROM soluciones WHERE nombre like '%$nombre%' LIMIT 5";

   return find_by_sql($sql);
}

function actSolucion($nombre,$cantidad,$idSolucion){
   global $db;

   $sql ="UPDATE soluciones SET nombre = '{$nombre}',cantidad = '{$cantidad}' WHERE id = '{$idSolucion}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaSolucion($nombre,$cantidad){
   global $db;

   $sql  ="INSERT INTO soluciones (nombre,cantidad) VALUES ('{$nombre}','{$cantidad}') ";
   $sql .="ON DUPLICATE KEY UPDATE nombre = '{$nombre}'";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function desparasitantes($nombre){
   global $db;

   $sql ="SELECT * FROM desparasitantes WHERE nombre like '%$nombre%' LIMIT 5";

   return find_by_sql($sql);
}

function altaDesparasitante($nombre){
   global $db;

   $sql  ="INSERT INTO desparasitantes (nombre) VALUES ('{$nombre}') ";
   $sql .="ON DUPLICATE KEY UPDATE nombre = '{$nombre}'";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function vacunas($nombre){
   global $db;

   $sql ="SELECT * FROM vacunas WHERE nombre like '%$nombre%' LIMIT 5";

   return find_by_sql($sql);
}

function altaVacunas($nombre){
   global $db;

   $sql  ="INSERT INTO vacunas (nombre) VALUES ('{$nombre}') ";
   $sql .="ON DUPLICATE KEY UPDATE nombre = '{$nombre}'";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaVacunasXResp($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT responsable,count(responsable) as numero from vacuna where fecha BETWEEN '{$fechaIni}' ";
   $sql .="AND '{$fechaFin}' group by responsable order by numero desc";

   return find_by_sql($sql);
}

function buscaEsteticasXResp($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT responsable,count(responsable) as numero from estetica where fecha BETWEEN '{$fechaIni}' ";
   $sql .="AND '{$fechaFin}' group by responsable order by numero desc";

   return find_by_sql($sql);
}

function buscaConsultasXResp($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT responsable,count(responsable) as numero from Consulta where fecha BETWEEN '{$fechaIni}' ";
   $sql .="AND '{$fechaFin}' group by responsable order by numero desc";

   return find_by_sql($sql);
}

function buscaDesparasitacionesXResp($fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT responsable,count(responsable) as numero from desparasitacion where fecha ";
   $sql .="BETWEEN '{$fechaIni}' AND '{$fechaFin}' group by responsable order by numero desc";

   return find_by_sql($sql);
}

function actDatosProducto($nombre,$nuevoStock,$fechaAct,$categoria,$precCompra,$precVenta,$cantCaja,$precioCaja,$idProducto){
   global $db;

   $sql  ="UPDATE products SET name = '{$nombre}',quantity = '{$nuevoStock}',fechaMod = '{$fechaAct}',";
   $sql .="categorie_id = '{$categoria}',buy_price = '{$precCompra}',sale_price = '{$precVenta}', ";
   $sql .="cantidadCaja = '{$cantCaja}',precioCaja = '{$precioCaja}' ";
   $sql .="WHERE id ='{$idProducto}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function altaHistEstetica($idProducto,$idSucursal,$idUsuario,$vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$idCliente,$fecha,$hora,$idTicket){
   global $db;

   $sql  ="INSERT INTO histestetica(idHistEstetica,idProducto,idSucursal,idUsuario,vendedor,ventaLV,";
   $sql .="ventaSD,comisionLV,comisionSD,cliente,fecha,hora,id_ticket) VALUES ('','{$idProducto}','{$idSucursal}',";
   $sql .="'{$idUsuario}','{$vendedor}','{$ventaLV}','{$ventaSD}','{$comisionLV}','{$comisionSD}',";
   $sql .="'{$idCliente}','{$fecha}','{$hora}','{$idTicket}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function gastosMACPerTotal($fechaIni,$fechaFin){
  global $db;
  $sql  = "SELECT SUM(g.total) as total ";
  $sql .= "FROM gastos g ";
  $sql .= "WHERE g.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

  $query = $db->query($sql);
  if($result = $db->fetch_assoc($query))
     return $result;
  else
     return null;
}

function ventasCatPerTotal($fechaIni,$fechaFin){
  global $db;
  $sql  = "SELECT SUM(s.price) as total ";
  $sql .= "FROM sales s,products p,categories c ";
  $sql .= "WHERE p.categorie_id = c.id AND p.categorie_id = c.id AND s.product_id = p.id ";
  $sql .= "AND s.date BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

  $query = $db->query($sql);
  if($result = $db->fetch_assoc($query))
     return $result;
  else
     return null;
}

function ventasTipoPagoPerTipo($tipoPago,$fechaIni,$fechaFin){
  global $db;

  $sql  ="SELECT tp.tipo_pago,p.fecha,p.cantidad ";
  $sql .="FROM pagos p,tipo_pago tp ";
  $sql .="WHERE p.id_tipo = tp.id_pago AND p.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
  $sql .="AND p.id_tipo = '$tipoPago' ";

  return $db->query($sql);
}

function ventasTipoPagoPer($fechaIni,$fechaFin){
  global $db;

  $sql  ="SELECT tp.tipo_pago,p.fecha,p.cantidad ";
  $sql .="FROM pagos p,tipo_pago tp ";
  $sql .="WHERE p.id_tipo = tp.id_pago AND p.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";

  return $db->query($sql);
}

function totalVentasTipoPagoPer($tipoPago,$fechaIni,$fechaFin){
   global $db;

   $sql  ="SELECT SUM(p.cantidad) as total ";
   $sql .="FROM pagos p,tipo_pago tp ";
   $sql .="WHERE p.id_tipo = tp.id_pago AND p.fecha BETWEEN '{$fechaIni}' AND '{$fechaFin}' ";
   $sql .="AND p.id_tipo = '$tipoPago' ";

   $query = $db->query($sql);
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}


function resetCaja($fecha){
   global $db;

   $consCaja =  buscaRegistroMaximo("caja","id");
   $fechaCaja = $consCaja['fecha'];
   $idCaja =    $consCaja['id'];
 
   if ($fechaCaja < $fecha){
      $sqlCaja = "UPDATE caja SET monto = '0',fecha = '{$fecha}' WHERE id = '$idCaja'";  
      $result = $db->query($sqlCaja);
      return $result;
   } else
      return false;
}

function pagoCreditoDia($idSucursal,$fecha,$tipoPago){
   global $db;

   if ($tipoPago == "")
      $sql  ="SELECT SUM(cantidad) as total FROM pagos WHERE credito = '1' ";
   else
      $sql  ="SELECT SUM(cantidad) as total FROM pagos WHERE credito = '1' and id_tipo = '$tipoPago' ";
   
   $sql .="and id_sucursal = '$idSucursal' and fecha = '{$fecha}'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}   

function saldoCajaDia($fecha,$idSucursal,$movimiento){
   global $db;

   $sql  ="SELECT SUM(cantFinal-cantIni) as total FROM histefectivo WHERE id_movimiento = '$movimiento'";
   $sql .="AND fechaMov = '$fecha' AND idsucursal = '$idSucursal'";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function borraRegistrosPorCampo($tabla,$campo,$valor){
   global $db;

   if(tableExists($tabla)){

      $sql  = "DELETE FROM ".$db->escape($tabla);
      $sql .= " WHERE $campo = '$valor'";

      $result = $db->query($sql);
    
      return $result;
   }
}

function categorias(){
   global $db;

   $sql  ="SELECT * FROM categories order by name";

   return $db->query($sql);
}

function obtenSubCat($idCategoria){
   global $db;
   $sql = "SELECT * FROM subcategories WHERE idCategoria = {'$idCategoria'}";
   return $dq->query($sql);
}

function altaMovsEstetica($idProducto,$idSucursal,$idUsuario,$vendedor,$ventaLV,$ventaSD,$comisionLV,$comisionSD,$idCliente,$idTicket,$movimiento,$fecha,$hora){
   global $db;

   $sql  ="INSERT INTO movsestetica(idMovsEstetica,idProducto,idSucursal,idUsuario,vendedor,ventaLV,";
   $sql .="ventaSD,comisionLV,comisionSD,cliente,id_ticket,movimiento,fecha,hora) VALUES ('','{$idProducto}','{$idSucursal}',";
   $sql .="'{$idUsuario}','{$vendedor}','{$ventaLV}','{$ventaSD}','{$comisionLV}','{$comisionSD}',";
   $sql .="'{$idCliente}','{$idTicket}','{$movimiento}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function buscaProdHistEstetica($idProducto,$idTicket){
   global $db;

   $sql .="SELECT * FROM histestetica WHERE idProducto = $idProducto AND id_ticket = $idTicket";
   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query)) return $result;
   else return null;
}

function numTiposPagos($idTicket){
   global $db;

   $sql ="SELECT count(id_pago) AS numPagos,cantidad FROM pagos WHERE id_ticket = $idTicket";

   $query = $db->query($sql);
  
   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function sumaCampoTemp($aSumar,$tabla,$campo,$valor,$usuario){
   global $db;

   $sql = "SELECT SUM($aSumar) as total FROM $tabla WHERE $campo = '$valor' AND usuario = '$usuario'";   

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function categoria($nombre){
   global $db;
   $sql ="SELECT * FROM categories WHERE name like '%$nombre%' /*LIMIT 5*/";
   return find_by_sql($sql);
}

function buscaValorMaximo($tabla,$aBuscar,$campo,$valor){
   global $db;

   $sql = "SELECT MAX($aBuscar) as valorMax FROM $tabla WHERE $campo = '$valor'";   

   $query = $db->query($sql);

   if($result = $db->fetch_assoc($query))
      return $result;
   else
      return null;
}

function altaSubCategoria($idCategoria,$idSubcategoria,$nombre){
   global $db;

   $sql  = "INSERT INTO subcategorias (";
   $sql .="id,idCategoria,idSubCategoria,nombre";
   $sql .=") VALUES (";
   $sql .="'','{$idCategoria}','{$idSubcategoria}','{$nombre}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function altaHistConsulta($idUsuario,$idCliente,$idMascota,$idSucursal,$movimiento,$fecha,$hora){
   global $db;

   $sql  = "INSERT INTO histconsulta (";
   $sql .="idHistConsulta,idUsuario,idCliente,idMascota,idSucursal,movimiento,fecha,hora";
   $sql .=") VALUES (";
   $sql .="'','{$idUsuario}','{$idCliente}','{$idMascota}','{$idSucursal}','{$movimiento}','{$fecha}','{$hora}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function histConsUsuSuc($idUsuario,$idSucursal){
   global $db;

   $sql  ="SELECT u.username,c.nom_cliente,m.nombre,s.nom_sucursal,h.movimiento,h.fecha,h.hora ";
   $sql .="FROM users u, cliente c, Mascotas m, sucursal s, histconsulta h ";
   $sql .="WHERE h.idUsuario = $idUsuario and h.idSucursal = $idSucursal and h.idUsuario = u.id and h.idSucursal = s.idSucursal ";
   $sql .="and h.idCliente = c.idcredencial and h.idMascota = m.idMascotas ORDER BY h.fecha DESC";

   return find_by_sql($sql);
}

function histConsSuc($idSucursal){
   global $db;

   $sql  ="SELECT u.username,c.nom_cliente,m.nombre,s.nom_sucursal,h.movimiento,h.fecha,h.hora ";
   $sql .="FROM users u, cliente c, Mascotas m, sucursal s, histconsulta h ";
   $sql .="WHERE h.idSucursal = $idSucursal and h.idUsuario = u.id and h.idSucursal = s.idSucursal ";
   $sql .="and h.idCliente = c.idcredencial and h.idMascota = m.idMascotas ORDER BY h.fecha DESC";

   return find_by_sql($sql);
}

function histConsUsu($idUsuario){
   global $db;

   $sql  ="SELECT u.username,c.nom_cliente,m.nombre,s.nom_sucursal,h.movimiento,h.fecha,h.hora ";
   $sql .="FROM users u, cliente c, Mascotas m, sucursal s, histconsulta h ";
   $sql .="WHERE h.idUsuario = $idUsuario and h.idUsuario = u.id and h.idSucursal = s.idSucursal ";
   $sql .="and h.idCliente = c.idcredencial and h.idMascota = m.idMascotas ORDER BY h.fecha DESC";
    
   return find_by_sql($sql);
}

function histConsulta(){
   global $db;

   $sql  ="SELECT u.username,c.nom_cliente,m.nombre,s.nom_sucursal,h.movimiento,h.fecha,h.hora ";
   $sql .="FROM users u, cliente c, Mascotas m, sucursal s, histconsulta h ";
   $sql .="WHERE h.idUsuario = u.id and h.idSucursal = s.idSucursal ";
   $sql .="and h.idCliente = c.idcredencial and h.idMascota = m.idMascotas ORDER BY h.fecha DESC";

   return find_by_sql($sql);
}   

function actSubcategoria($nombre,$id){
   global $db;

   $sql = "UPDATE subcategorias SET nombre = '{$nombre}' WHERE id = '{$id}'";

   $db->query($sql);
   
   return($db->affected_rows() === 1 ? true : false);
}

function gastosFactura($factura,$fechaIni,$fechaFin,$regCat,$regSubCat){
   global $db;

   $sql =  "SELECT g.id, g.descripcion, g.monto, g.fecha, g.categoria, g.iva, g.total, p.nom_proveedor, p.idProveedor, ";
   $sql .= "tp.tipo_pago, tp.id_pago, c.name, sc.nombre, g.factura FROM gastos g LEFT JOIN tipo_pago tp ON g.tipo_pago = tp.id_pago ";
   $sql .= "LEFT JOIN proveedor p ON g.idProveedor = p.idProveedor LEFT JOIN categories c ON g.categoria = c.id ";
   $sql .= "LEFT JOIN subcategorias sc ON g.subCategoria = sc.idSubCategoria AND g.categoria = sc.idCategoria WHERE (";
   
   if ($factura != '') {
      if ($regCat != '' && $regSubCat != '') {
         $sql .= "g.factura = '$factura' AND ";
         if ($regCat != '' && $regSubCat != '')
            $sql .= "c.id = '$regCat' AND sc.idSubCategoria = '$regSubCat'";
         if ($regCat != '' && $regSubCat == '')
            $sql .= "c.id = '$regCat'";
      } else {
         $sql .= "g.factura = '$factura' ";
      }
   } else {
      if ($regCat != '' && $regSubCat != '')
         $sql .= "c.id = '$regCat' AND sc.idSubCategoria = '$regSubCat'";
      if ($regCat != '' && $regSubCat == '')
         $sql .= "c.id = '$regCat'";
   }

   $sql .= ") AND g.fecha BETWEEN '$fechaIni' AND '$fechaFin' ";
   $sql .= "ORDER BY g.fecha DESC";
   return find_by_sql($sql);
} 

function altaTempCatSubCat($categoria,$subcategoria,$cantidad,$venta,$gasto,$ganancia,$fechaInicial,$fechaFinal){
   global $db;

   $sql  = "INSERT INTO tempcatsubcat (categoria,subcategoria,cantidad,venta,gasto,ganancia,fechaInicial,fechaFinal";
   $sql .=") VALUES (";
   $sql .="'{$categoria}','{$subcategoria}','{$cantidad}','{$venta}','{$gasto}','{$ganancia}','{$fechaInicial}','{$fechaFinal}')";

   $db->query($sql);
 
   return ($db->affected_rows() === 1) ? true : false;
}

function borraTabla($tabla){
   global $db;

   if(tableExists($tabla)){

      $sql  = "DELETE FROM ".$db->escape($tabla);

      $db->query($sql);
    
      return ($db->affected_rows() === 1) ? true : false;
   }
}

function depurarBD(){
  global $db;
  global $session;

  $sucursal = "";
  $sqlProds = "SELECT id,fechaMod,quantity FROM products";
  $productos = $db->query($sqlProds);

  foreach ($productos as $producto):

     $fechaMod = date("Y-m-d", strtotime ($producto['fechaMod']));

     $dia = date("d", strtotime ($fechaMod));
     $mes = date("m", strtotime ($fechaMod));
     $anio = date("Y", strtotime ($fechaMod)); 

     $fechaUltmod = date("Y-m-d", mktime(0,0,0, $mes,$dia,$anio));

     $fechaIniPer = new DateTime($fechaUltmod);
     $fecha_actual = new DateTime(date('Y-m-d',time()));

     $difActual = date_diff($fecha_actual,$fechaIniPer);

     $meses = $difActual->m;
     $anios = $difActual->y;
     $dias = $difActual->d;

     if ($dias > 0 && $meses == "0" && $anios == "1"){
        if ($producto['quantity'] == "0.00"){
           $sqlBorrarProd = "DELETE FROM products WHERE id = '{$producto['id']}'";
           $db->query($sqlBorrarProd);
        }
     }
  endforeach;

  $sqlSuc = "SELECT idSucursal FROM sucursal WHERE idSucursal NOT IN (SELECT idSucursal FROM products)";
  $sucursales = $db->query($sqlSuc);
  
  foreach ($sucursales as $sucursal):

     $sqlBorrarSuc = "DELETE FROM sucursal WHERE idSucursal = '{$sucursal['idSucursal']}'";
     $db->query($sqlBorrarSuc);
  
  endforeach;

  return true;
}
function buscaSubCategorias($idCategoria, $nombre){
   global $db;

   $sql = "SELECT * FROM `subcategorias` WHERE idCategoria = '$idCategoria' ";
   if ($nombre != '')
      $sql.= "AND nombre like '%$nombre%'";

   return find_by_sql($sql);
}
?>
?>