function cliente(){
   document.form1.action = "cliente.php";
   document.form1.submit();
}

function foco(){
   document.form1.Codigo.focus();
}

function his(){
   document.form1.action = "historico.php";
   document.form1.submit();
}

function mayusculas(e) {
   var ss = e.target.selectionStart;
   var se = e.target.selectionEnd;
   e.target.value = e.target.value.toUpperCase();
   e.target.selectionStart = ss;
   e.target.selectionEnd = se;
}

function histEfectivo(){
   document.form1.action = "histEfectivo.php";
   document.form1.submit();
}

function responsable(){
   document.form1.responsable.focus();
}

function cancelacion(){
   document.form1.action = "cancelaciones.php";
   document.form1.submit();
}

function sucursal(){
   document.form1.sucursal.focus();
}

function clienteApp(){
  document.form1.action = "clienteApp.php";
  document.form1.submit();
}

function focoCliente(){
  document.form1.cliente.focus();
}

function aplicaciones(){
  document.form1.action = "appsActivas.php";
  document.form1.submit();
}

function focoInstancia(){
  document.form1.instancia.focus();
}

function datosListas(){
  document.form1.tipo.value = document.form1.tipoAux.value;
}

function aplicPorVencer(){
  document.form1.action = "appsPorVencer.php";
  document.form1.submit();
}

function fechaRenovacion(){
   var fechaAux = document.form1.fechaAux.value;
   var periodo =  document.form1.periodo.value;

   var fechaRen = "";
   var anio =     "";
   var mes =      "";
   var dia =      "";

   anio = fechaAux.substring(0,4);
   mes =  fechaAux.substring(5,7);
   dia =  fechaAux.substring(8,10);

   if (periodo == "3" || periodo == "6") {
      mes = parseInt(mes) + parseInt(periodo);

      if (mes > 12) {
         mes = mes - 12;
         anio = parseInt(anio) + 1;
      }
   }

   if (periodo == "12") {
      anio = parseInt(anio) + 1;
      mes =  parseInt(mes);
   }

   if (mes < 10)
      mes = "0" + mes;

   var diasMes = new Date(anio, parseInt(mes), 0).getDate();

   if (parseInt(dia) == 30 || parseInt(dia) == 28 || parseInt(dia) == 29) 
      dia = diasMes;

   fechaRen = anio + "-" + mes + "-" + dia;

   document.form1.fechaRen.value = fechaRen;
}

function estatus(){
   document.form1.estatus.value = document.form1.estatusAux.value;
}

function aplicVencidas(){
   document.form1.action = "appsVencidas.php";
   document.form1.submit();
}

function aplicNoActivas(){
   document.form1.action = "appsVencidasNoAct.php";
   document.form1.submit();
}

function entrega(){
   document.form1.action = "entregas.php";
   document.form1.submit();
}

function focoCodigo(){
   horaInicial();
   document.form1.codigo.focus();  
}

function agregarEnt(){
   var str = "";

   for (i = 0; i<form1.elements.length; i++){
      if (form1.elements[i].checked){
         str = form1.elements[i].value;
         document.form1.idProd.value = str.substring(0,str.indexOf("|"));
         document.form1.precio.value = str.substring(str.indexOf("|")+1);

         break;
      }
   }
  
   document.form1.action = "tempEntregas.php";
   document.form1.submit();
}

function multiplica(){
   var multiplos = "";
   var str =       "";

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].name == "cantidad"){
         str = form1.elements[i].value;
         multiplos = multiplos + str +  "|";
      }
   }
  
   document.form1.multiplos.value = multiplos + "|";
   document.form1.action = "cantEntregas.php";
   document.form1.submit();
}

function registrar(){
   var cont = 0;

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].name == "cantidad"){
         cont++;
         break;
      }
   }

   if (cont > 0) {
      document.form1.action = "registrar.php";
      document.form1.submit();
   } else {
      alert("Debe agregar productos para registrar");
      return -1;
   }
}

function horas(){
   var fechaCita;
   var fechaActual;
   var hoy = new Date();

   fechaCita =    document.form1.fecha.value;
   fechaActual =  formatoFecha(hoy, 'yyyy-mm-dd');

   if (fechaCita == fechaActual)
      horaInicial();
   else
      listaHoras(8,"00");
}

function horaInicial(){
   const hoy = new Date();
   hora_actual = hoy.getHours();
   var minsActs;
   var horaCalc;
   var mins = "";
   var fechaActual;

   if (hora_actual < 8){
      listaHoras(8,"00");
   }else{
      minsActs = hoy.getMinutes();
      horaCalc = hoy.getHours();

      if (minsActs < 59 && minsActs >= 45){
         horaCalc = horaCalc + 1;
         mins = "00";
      }

      if (minsActs < 45)
         mins = "45";

      if (minsActs < 30)
         mins = "30";

      if (minsActs < 15)
         mins = "15";

      listaHoras(horaCalc,mins);
   }
   
   fechaActual = formatoFecha(hoy,'yyyy-mm-dd');
   document.form1.fecha.value = fechaActual;
}

function listaHoras(horaCalc,mins){
   var array = [];

   var cont =     0;
   var longitud = 0;

   var horaAux =   "";
   var horaLista = "";
   
   var minsOrig = mins;
   var horaOrig = horaCalc;

   if (horaOrig < 10)
      horaOrig = "0" + horaOrig;
   else
      horaOrig = horaOrig;

   var select = document.getElementById("horasLista");

   longitud = select.length;

   if (longitud > 0){
      for (let i = select.options.length; i >= 0; i--) {
         select.remove(i);
      }
   }

   for (var h = horaCalc;h <= 19; h++){
      if (h < 10)
         horaAux = "0" + h;
      else
         horaAux = h;

      if (cont == 0){
         horaLista = "00:00";
         array.push(horaLista);
         cont++;
      }
/*      if (mins == "00"){
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         horaLista = horaAux + ":15";
         array.push(horaLista);
         horaLista = horaAux + ":30";
         array.push(horaLista);
         horaLista = horaAux + ":45";
         array.push(horaLista);
      }
      if (mins == "15"){
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         horaLista = horaAux + ":30";
         array.push(horaLista);
         horaLista = horaAux + ":45";
         array.push(horaLista);
         mins = "00";
      }
      if (mins == "30"){
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         horaLista = horaAux + ":45";
         array.push(horaLista);
         mins = "00";
      }
      if (mins == "45"){
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         mins = "00";
      }*/

      if (mins == "00"){
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);

         horaLista = horaAux + ":30";
         array.push(horaLista);
      } else {
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         mins = "00";
      }

   }
   addOptions("hora", array);
   document.form1.hora.value = horaOrig + ":" + minsOrig;
}

function addOptions(domElement, array) {
   var select = document.getElementsByName(domElement)[0];
   var option;

   for (value in array) {
      option = document.createElement("option");
      option.text = array[value];
      select.add(option);
   }
}

function formatoFecha(fecha, formato) {
   var mes = fecha.getMonth() + 1;
   var dia = fecha.getDate();
   
   if (mes < 10)
      mes = "0" + mes;
    
   if (dia < 10)
      dia = "0" + dia;

   const map = {
      dd: dia,
      mm: mes,
      yyyy: fecha.getFullYear()
   }

   return formato.replace(/dd|mm|yyyy/gi, matched => map[matched]);
}

function horasEdicion(){
   var hora =    "";  
   var horaFin = "";
   var horaAux = "";
   
   var fechaCita;
   var fechaActual;

   var hoy =  new Date();
   minsActs = hoy.getMinutes();
   horaCalc = hoy.getHours();

   if (minsActs < 59 && minsActs >= 45){
      horaCalc = horaCalc + 1;
      mins = "00";
   }

   if (minsActs < 45)
      mins = "45";

   if (minsActs < 30)
      mins = "30";

   if (minsActs < 15)
      mins = "15";

   if (horaCalc < 10)
      horaFin = "0" + horaCalc;
   else
      horaFin = horaCalc;

   fechaActual =  formatoFecha(hoy, 'yyyy-mm-dd');
   fechaEntrega = document.form1.fecha.value;

   hora = document.form1.horaAux.value;

   if (hora != "")
      hora = hora.substring(0,5);

   horaAux = horaFin + ":" + mins;

   if (fechaEntrega == fechaActual){
      horaInicial();
      if (hora < horaAux)
         hora = horaAux;
   }else{
      listaHoras(8,"00");
   }

   document.form1.hora.value = hora;   
}

function garantias(){
   document.form1.action = "garantias.php";
   document.form1.submit();
}

function prodsConGarantia(){
   document.form1.action = "prodsConGarantia.php";
   document.form1.submit();
}

function calculoIva(){
   var calcIva = document.form1.porcIva.value/100;

   if (document.form1.precioCompra.value == "")
      document.form1.precioCompra.value = 0;

   if (document.form1.aplicaIva.checked){
      document.form1.iva.value = (document.form1.precioCompra.value * calcIva).toFixed(2);
      document.form1.total.value = (document.form1.precioCompra.value * (calcIva + 1)).toFixed(2);
   }else{
      document.form1.iva.value = 0;

      if (document.form1.precioCompra.value != 0)
         document.form1.total.value = document.form1.precioCompra.value;
      else
         document.form1.precioCompra.value = "";
   }
}

function asignar(){
   document.form1.aplicaIva.checked = false;
   document.form1.iva.value = 0;

   if (document.form1.precioCompra.value != "")
      document.form1.total.value = document.form1.precioCompra.value;  
   else
      document.form1.total.value = 0;
}

function gastosMensuales(){
   document.form1.action = "monthly_sales_gastos.php";
   document.form1.submit();
}

function gastosCategoria(){
   document.form1.action = "monthly_sales_gastos_categoria.php";
   document.form1.submit();
}

if (window.history.replaceState) { // verificamos disponibilidad
   window.history.replaceState(null, null, window.location.href);
}

function descuento(){
   var cont=0;  
  
   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].name == "elimina"){
         cont++;
         break;
      }
   }

   if (cont > 0) {
      document.form1.action = "descuento.php";
      document.form1.submit();
   } else {
      alert("Debe agregar productos para vender");
      return -1;
   }
}

function apartado(){
   var cont=0;  

   if (document.form1.idCliente.value == ""){
      alert ("Debe proporcionar el id Credencial");
      return -1;
   }

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].name == "elimina"){
         cont++;
         break;
      }
   }

   if (cont > 0) {
      document.form1.action = "../credito/apartado.php";
      document.form1.submit();
   } else {
      alert("Debe agregar productos para dar crédito");
      return -1;
   }
}

function valor(){
   var str="";
   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].checked){
         str = form1.elements[i].value;
         document.form1.idProd.value = str.substring(0,str.indexOf("|"));
         document.form1.precio.value = str.substring(str.indexOf("|")+1);
         break;
      }
   }
   document.form1.codigo.focus();  
}

function agregar(){
   var str="";
   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].checked){
         str = form1.elements[i].value;
         document.form1.idProd.value = str.substring(0,str.indexOf("|"));
         document.form1.precio.value = str.substring(str.indexOf("|")+1);
         break;
      }
   }
  
   document.form1.action = "temporal.php";
   document.form1.submit();
}

function eliminar(){
   var existe = 0;
   var str = "";

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].name == "elimina"){
         existe = 1;
         if (form1.elements[i].checked){
            str = form1.elements[i].value;           
            break;
         }
      }
   }

   if (existe == 0) {
      alert ("No existen productos para eliminar");
      return -1;
   }

   if (str == "") {
      alert ("Debe seleccionar un producto para eliminar");
      return -1;
   }

   if (existe == 1 && str != ""){
      document.form1.cveTemp.value = str;
      document.form1.action = "elimina.php";
      document.form1.submit();
   }
}

function multiplica(){
   var str="";
   var multiplos="";

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].name == "cantidad"){
         str = form1.elements[i].value;
         multiplos = multiplos + str +  "|";
      }
   }

   document.form1.multiplos.value = multiplos + "|";
   document.form1.action = "cantidad.php";
   document.form1.submit();
}

function aplicar() {
   var tarjeta =        0;
   var efectivo =       0;
   var deposito =       0;
   var sumaTotal =      0;
   var transferencia =  0;

   if (document.form1.efectivo.value != "")
      efectivo = parseFloat(document.form1.efectivo.value);

   if (document.form1.transferencia.value != "")
      transferencia = parseFloat(document.form1.transferencia.value);

   if (document.form1.deposito.value != "")
      deposito = parseFloat(document.form1.deposito.value);

   if (document.form1.tarjeta.value != "")
      tarjeta = parseFloat(document.form1.tarjeta.value);

   sumaTotal = (efectivo + transferencia + deposito + tarjeta).toFixed(2);

   if (document.form1.hayDescuento.value == "0"){
      if (sumaTotal < document.form1.total.value){
         alert("La suma de cantidades es menor al total de compra");
         return -1;
      }

      if (sumaTotal > document.form1.total.value){
         alert("La suma de cantidades es mayor al total de compra");
         return -1;
      }
   }

   if (document.form1.hayDescuento.value == "1"){
      if (sumaTotal < document.form1.totalConDesc.value){
         alert("La suma de cantidades es menor al total con descuento");
         return -1;
      }
      if (sumaTotal > document.form1.totalConDesc.value){
         alert("La suma de cantidades es mayor al total con descuento");
         return -1;
      }
   }

   if (document.form1.efectivo.value == "" && document.form1.transferencia.value == "" && document.form1.deposito.value == "" && document.form1.tarjeta.value == ""){
      alert ("Debe proporcionar las cantidades a cobrar");
      return -1;
   }

   if (document.form1.vendedor.value == ""){
      alert("Debe seleccionar a un vendedor.");
      return -1;
   }

   document.form1.action = "ventas.php";
   document.form1.submit();
}

function suma() {
   var tarjeta =       0;
   var efectivo =      0;
   var deposito =      0;
   var sumaTotal =     0;
   var totTarjeta =    0;
   var transferencia = 0;

   if (document.form1.efectivo.value != "")
      efectivo = parseFloat(document.form1.efectivo.value);

   if (document.form1.transferencia.value != "")
      transferencia = parseFloat(document.form1.transferencia.value);

   if (document.form1.deposito.value != "")
      deposito = parseFloat(document.form1.deposito.value);

   if (document.form1.tarjeta.value != ""){
      tarjeta = parseFloat(document.form1.tarjeta.value);
      totTarjeta = tarjeta * .05;
   } else {
      totTarjeta = "";
   }

   sumaTotal = efectivo + transferencia + deposito + tarjeta;
   
   document.form1.sumaTotal.value =  sumaTotal.toFixed(2);
   document.form1.totTarjeta.value = totTarjeta;   
}

function regresaVentas(){
   document.form1.action = "add_sale.php";
   document.form1.submit();
}

function vuelto(){
   var pago =     0;
   var cambio =   0;
   var efectivo = 0;

   if (document.form1.pago.value != ""){
      pago = parseFloat(document.form1.pago.value);
      efectivo = parseFloat(document.form1.efectivo.value);

      cambio = pago - efectivo;
   } else {
      cambio = "";
   }
   
   document.form1.cambio.value = cambio;
}

function ventaEncargado(){
   document.form1.action = "sales.php";
   document.form1.submit();
}

function focoEncargado(){
   document.form1.encargado.focus();
}

function focoPrecio(){
   document.form1.precio.focus();
}

function sumaEdicion(){
   var tarjeta =       0;
   var efectivo =      0;
   var deposito =      0;
   var sumaTotal =     0;
   var transferencia = 0;

   if (document.form1.efectivo.value != "")
      efectivo = parseFloat(document.form1.efectivo.value);

   if (document.form1.transferencia.value != "")
      transferencia = parseFloat(document.form1.transferencia.value);

   if (document.form1.deposito.value != "")
      deposito = parseFloat(document.form1.deposito.value);

   if (document.form1.tarjeta.value != "")
      tarjeta = parseFloat(document.form1.tarjeta.value);
  
   sumaTotal = efectivo + transferencia + deposito + tarjeta;   
   document.form1.totalPago.value = sumaTotal;
}

function sumaTotal(){
   var precioMod =  0;
   var totalOrig =  0;
   var sumaTotal =  0;
   var precioOrig = 0;

   if (document.form1.precio.value != ""){
      precioMod =  parseFloat(document.form1.precio.value);
      totalOrig =  parseFloat(document.form1.totalOrig.value);
      precioOrig = parseFloat(document.form1.precioOrig.value);
   }

   sumaTotal = totalOrig - precioOrig + precioMod;

   document.form1.totalVenta.value = sumaTotal;
}

function ventasMens(){
   document.form1.action = "monthly_sales_categoria.php";
   document.form1.submit();
}

function focoCategoria(){
   document.form1.categoria.focus();
}

function ventasAnual(){
   document.form1.action = "ventas_anuales_categoria.php";
   document.form1.submit();
}

function focoSucursal(){
   document.form1.sucursal.focus();
}

function ventasMensuales(){
   document.form1.action = "ventas_anuales.php";
   document.form1.submit();
}

function barMensual(){
   document.form1.action = "../graficas/bar_anual.php";
   document.form1.submit();
}

function ventasDiarias(){
   document.form1.action = "ventas-mensuales.php";
   document.form1.submit();
}

function barDiaria(){
   document.form1.action = "../graficas/bar.php";
   document.form1.submit();
}

function ventasDelDia(){
   document.form1.action = "ventas_diarias.php";
   document.form1.submit();
}

function corteDia(){
   document.form1.action = "cortes_dia.php";
   document.form1.submit();
}

function corteQuincena(){
   document.form1.action = "cortes_quincena.php";
   document.form1.submit();
}

function producto(){
   document.form1.action = "simple_product.php";
   document.form1.submit();
}

function corteEncargado(){
   document.form1.action = "cortes_semana.php";
   document.form1.submit();
}

function histEstancia(){
   document.form1.action = "histEstancia.php";
   document.form1.submit();
}

function focoMascota(){
   document.form1.mascota.focus();
}

function histEstetica(){
   document.form1.action = "histEstetica.php";
   document.form1.submit();
}

function focoResponsable(){
   document.form1.responsable.focus();
}

function valorMascota(){
   var str = "";

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].checked){
         str = form1.elements[i].value;
         document.form1.idMascotas.value = str;
         break;
      }
   }

   document.form1.idMascota.focus();  
}

function datosMascota(){
   var str="";

   for (i=0;i<form1.elements.length;i++){
      if (form1.elements[i].checked){
         str = form1.elements[i].value;
         document.form1.idMascotas.value = str;
         break;
      }
   }

   document.form1.action = "history.php";
   document.form1.submit();
}

function regresaHistory(){
   document.form1.action = "history.php";
   document.form1.submit();
}

function horasCita(){
   var fechaCita;
   var fechaActual;
   var hoy = new Date();

   fechaCita =   document.form1.fecha.value;
   fechaActual = formatoFecha(hoy, 'yyyy-mm-dd');

   if (fechaCita == fechaActual)
      horaInicialCita();
   else
      listaHorasCita(9,"00");
}

function horaInicialCita(){
   const hoy = new Date();
   hora_actual = hoy.getHours();
   var minsActs;
   var horaCalc;
   var mins = "";
   var fechaActual;

   if (hora_actual < 9){
      listaHorasCita(9,"00");
   }else{
      minsActs = hoy.getMinutes();
      horaCalc = hoy.getHours();

      if (minsActs < 30)
         mins = "30";

      if (minsActs < 59 && minsActs >= 30){
         horaCalc = horaCalc + 1;
         mins = "00";
      }
      listaHorasCita(horaCalc,mins);
   }
   fechaActual = formatoFecha(hoy,'yyyy-mm-dd');
   document.form1.fecha.value = fechaActual;
}

function listaHorasCita(horaCalc,mins){
   var horaAux =   "";
   var horaLista = "";
   
   var array = [];
   
   var longitud = 0;

   var select = document.getElementById("horasLista");

   longitud = select.length;

   if (longitud > 0){
      for (let i = select.options.length; i >= 0; i--) {
         select.remove(i);
      }
   }

   for (var h = horaCalc;h <= 19; h++){
      if (h < 10)
         horaAux = "0" + h;
      else
         horaAux = h;

      if (mins == "00"){
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         horaLista = horaAux + ":30";
         array.push(horaLista);
      }else{
         horaLista = horaAux + ":" + mins;
         array.push(horaLista);
         mins = "00";
      }
   }
   addOptions("hora", array);
}

function valorAntEstetica(){
   document.form1.hora_ent.value=document.form1.hora.value;
}

function citasMens(){
   document.form1.action = "citas-mensuales.php";
   document.form1.submit();
}

function horasEdicionCita(){
   var hora =    "";  
   var horaFin = "";
   var horaAux = "";
   
   var fechaCita;
   var fechaActual;
   
   var hoy =  new Date();
   minsActs = hoy.getMinutes();
   horaCalc = hoy.getHours();

   if (minsActs < 30)
      mins = "30";

   if (minsActs < 59 && minsActs >= 30){
      horaCalc = horaCalc + 1;
      mins = "00";
   }

   if (horaCalc < 10)
      horaFin = "0" + horaCalc;
   else
      horaFin = horaCalc;

   fechaCita =   document.form1.fecha.value;
   fechaActual = formatoFecha(hoy, 'yyyy-mm-dd');

   hora = document.form1.horaAux.value;

   if (hora != "")
      hora = hora.substring(0,5);

   horaAux = horaFin + ":" + mins;

   if (fechaCita == fechaActual){
      horaInicialEdicionCita();
      if (hora < horaAux)
         hora = horaAux;
   }else{
      listaHoras(9,"00");
   }

   document.form1.hora.value = hora;   
}

function horaInicialEdicionCita(){
   const hoy =   new Date();
   hora_actual = hoy.getHours();

   var minsActs;
   var horaCalc;
   var fechaActual;

   var mins =    "";
   var hora =    "";
   var horaFin = "";

   if (hora_actual < 9){
      listaHoras(9,"00");
   }else{
      minsActs = hoy.getMinutes();
      horaCalc = hoy.getHours();

      if (minsActs < 30)
         mins = "30";

      if (minsActs < 59 && minsActs >= 30){
         horaCalc = horaCalc + 1;
         mins = "00";
      }
      listaHoras(horaCalc,mins);
   }

   fechaActual = formatoFecha(hoy, 'yyyy-mm-dd');
   document.form1.fecha.value = fechaActual;
}

function soluciones(){
   document.form1.action = "solucion.php";
   document.form1.submit();
}

function focoNombre(){
   document.form1.nombre.focus();
}

function desparasitantes(){
   document.form1.action = "desparasitante.php";
   document.form1.submit();
}

function vacunas(){
   document.form1.action = "vacunas.php";
   document.form1.submit();
}

function focoCodProd(){
   document.form1.Codigo.focus();
}

function ventasGastosMens(){
   document.form1.action = "monthly_sales_gastos.php";
   document.form1.submit();
}

function ventasGastosCatMens(){
   document.form1.action = "monthly_sales_gastos_categoria.php";
   document.form1.submit();
}

function ventasMensTipoPago(){
   document.form1.action = "ventas_mens_tipoPago.php";
   document.form1.submit();
}

function focoTipoPago(){
   document.form1.tipoPago.focus();
}
