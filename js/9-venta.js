var productos = document.getElementById("barra-elegir-producto");
const fechahora = document.getElementById("form-fechahora");
var miCheckbox = document.getElementById('checkbox-preventa');

var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));
var msjconfirm = document.getElementById('mensaje-confirmacion');

var panelconfirmarventa = new bootstrap.Modal(document.getElementById('confirmacion-venta'));
var panelComprobante = new bootstrap.Modal(document.getElementById('Modal-para-comprobante'));


var totalfinal_transaccion = 0;
var cant_productos_en_transaccion = 0;
var haypreventa = "no";

let listaDeProductos = [];
let listaPreventa = [];

imprimirProductos();

function imprimirProductos(){

    $.ajax({
        type: "POST",
        data: {"imprimir-productos" : "var"},
        url: "php/ventas.php",
        success: function(response) {
            productos.innerHTML = response;
            var select_producto = document.getElementById('barra-elegir-producto');

            select_producto.addEventListener('change', function() {
                var valor = select_producto.value;
                var separaridprecio = valor.split(',');
                var cantidad = separaridprecio[3];

                console.log(cantidad);

                var showcantidad = document.getElementById('show-cantidad');
                showcantidad.innerHTML = `<p style="margin-top: 5px; color: gray; margin-bottom: -5px;">Cantidad: ${cantidad}</p>`;

                var input = document.getElementById('input-cantidad');
                input.innerHTML = `<input type="hidden" id="cantidad-max" name="cantidad-max" value="${cantidad}">`;

            });

            $.ajax({
                type: "POST",
                data: {"imprimir-fecha-hora" : "var"},
                url: "php/ventas.php",
                success: function(response) {
                    fechahora.innerHTML = response;
                }
            });
        }
    });
}

miCheckbox.addEventListener('change', function() {

    var elemento_select_preventa = document.getElementById('elemento-select-preventa');

    if (miCheckbox.checked) {

    $.ajax({
        type: "POST",
        data: {"imprimir-preventas" : "var"},
        url: "php/ventas.php",
        success: function(response) {
            elemento_select_preventa.innerHTML = response;
        }
    });
        /*elemento_select_preventa.innerHTML = `<select id="select-preventa" style="width: 110%; height: 30px;">
                                                <option value="" disabled selected>Seleccionar preventa</option>
                                                <option value="2">Opción 2</option>
                                                <option value="3">Opción 3</option>
                                            </select><br><br>`;*/
    } else {
        elemento_select_preventa.innerHTML = `<select id="select-preventa" class="select-deshabilitado" style="width: 110%; height: 30px;">
                                                <option value="" disabled selected>Seleccionar pedido</option>
                                            </select><br><br>`;
    }
});

function trySelectPreventa(){

    var preventa = document.getElementById("select-preventa").value;

    if (preventa == "") {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">No se ha seleccionado pedido</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    var separaridprecio = preventa.split(',');

    var idpreventa = separaridprecio[0];
    var nomprod = separaridprecio[1];
    var cliente = separaridprecio[2];
    var abono = separaridprecio[3];
    var cantidad = separaridprecio[4];
    var preciou = separaridprecio[5];
    var idpruducto = separaridprecio[6];


    var total = cantidad * preciou;

    var divpreventa = document.getElementById("cargar-articulo-preventa");
    
    divpreventa.innerHTML = `<div style="margin-bottom: 5px;">
                            + [<span style="color: green;">$`+total+`</span>]&nbsp;
                            <span style="color: darkorange;"> Pedido: </span>
                            <span style="color: blue;"> `+cantidad+`</span>
                            <span style="font-style: italic; color: blue;">"`+nomprod+`"</span>
                            <span style="color: black;">| </span>
                            <span style="color: red; cursor: pointer;" onclick="quitarPreventaCuenta(\'`+abono+`\',\'`+total+`\')">ELIMINAR</span>
                        </div>
                        <div style="margin-bottom: 15px;">
                            - [<span style="color: red;">$`+abono+`</span>]&nbsp;Abono pedido
                            
                        </div>`;

    if(listaPreventa.length > 0){
        var totalanterior = parseFloat(listaPreventa[0].total);
        var abonoanterior = parseFloat(listaPreventa[0].abono);
        totalfinal_transaccion = totalfinal_transaccion - totalanterior;
        totalfinal_transaccion = totalfinal_transaccion + abonoanterior;
    }
    

    totalfinal_transaccion = totalfinal_transaccion + total;
    totalfinal_transaccion = totalfinal_transaccion - abono;

    listaPreventa.length = 0;
    agregarPreventa(idpreventa, nomprod, cantidad, preciou, total, abono, idpruducto);

    var botonpreventa = document.getElementById('boton-añadir-preventa');

    haypreventa = 'yes';

    imprimirTotal();
}

function trySelectProducto(){
    var producto = document.getElementById("barra-elegir-producto").value;
    var cant = parseFloat(document.getElementById("contenedor-cant-producto").value);
    var cantmax = document.getElementById("cantidad-max").value;

    if (producto == "") {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">No se ha seleccionado producto</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    if(cant > cantmax){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad excede el stock</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if(cant == "" || cant < 1 || isNaN(cant) || cant % 1 !== 0){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">El valor de la cantidad no es válido</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    var separaridprecio = producto.split(',');

    var id = separaridprecio[0];
    var precio = separaridprecio[1];
    var nomart = separaridprecio[2];

    let buscarArticulosiExiste = listaDeProductos.findIndex(producto => producto.id === id);

    var total = cant * precio;

    if (buscarArticulosiExiste !== -1) {
        console.log("Añadio el mismo articulo");
        
        if( listaDeProductos[buscarArticulosiExiste].cantidad + cant > cantmax){
            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad excede el stock</p>`;
            avisoEmergente.show();
            setTimeout(function() {
                avisoEmergente.hide();
            }, 1500);
            return; // Detener la ejecución de la función si hay campos vacíos
        }

        listaDeProductos[buscarArticulosiExiste].total = parseFloat(listaDeProductos[buscarArticulosiExiste].total) + total
        listaDeProductos[buscarArticulosiExiste].cantidad = parseFloat(listaDeProductos[buscarArticulosiExiste].cantidad) + cant

    }else{
        agregarProducto(id, nomart, cant, precio, total);
        cant_productos_en_transaccion = cant_productos_en_transaccion + 1;
    }

    

    totalfinal_transaccion = totalfinal_transaccion + total;
                /*
                if( (cant_productos_en_transaccion == 0)){
                    lista_producto.innerHTML = `<div style="margin-bottom: 10px;">
                                                    + [<span style="color: green;">$`+total+`</span>]&nbsp;
                                                    <span style="color: blue;"> `+cant+`</span>
                                                    <span style="font-style: italic; color: blue;">"`+nomart+`"</span>
                                                    <span style="color: black;">| </span>
                                                    <span style="color: red; cursor: pointer;" onclick="quitarProductoCuenta(\'`+id+`\',\'`+total+`\')">ELIMINAR</span>
                                                </div>`;
                }else{
                    lista_producto.innerHTML += `<div style="margin-bottom: 10px;">
                                                    + [<span style="color: green;">$`+total+`</span>]&nbsp;
                                                    <span style="color: blue;"> `+cant+`</span>
                                                    <span style="font-style: italic; color: blue;">"`+nomart+`"</span>
                                                    <span style="color: black;">| </span>
                                                    <span style="color: red; cursor: pointer;" onclick="quitarProductoCuenta(\'`+id+`\',\'`+total+`\')">ELIMINAR</span>
                                                </div>`;
                }
        */
    
    imprimirListaProductos();
    imprimirTotal();
}

function agregarPreventa(id, nombre, cantidad, preciou, total, abono, idpruducto) {
    let producto = { id, nombre, cantidad, preciou, total, abono, idpruducto};
    listaPreventa.push(producto);
}

function agregarProducto(id, nombre, cantidad, preciou, total) {
    let producto = { id, nombre, cantidad, preciou, total};
    listaDeProductos.push(producto);
}

function imprimirListaProductos(){

    var lista_producto = document.getElementById('lista-productos-elegidos');

    if( (cant_productos_en_transaccion == 0 ) ){
        lista_producto.innerHTML = `Aún no hay articulos<br>`; 
    }else{
        for(let i = 0; i < cant_productos_en_transaccion; i++){

            let itotal = listaDeProductos[i].total;
            let icant = listaDeProductos[i].cantidad;
            let inomart = listaDeProductos[i].nombre;
            let iid = listaDeProductos[i].id;


            if(i == 0){
                lista_producto.innerHTML = `<div style="margin-bottom: 10px;">
                                        + [<span style="color: green;">$`+itotal+`</span>]&nbsp;
                                        <span style="color: blue;"> `+icant+`</span>
                                        <span style="font-style: italic; color: blue;">"`+inomart+`"</span>
                                        <span style="color: black;">| </span>
                                        <span style="color: red; cursor: pointer;" onclick="quitarProductoCuenta(\'`+iid+`\',\'`+itotal+`\')">ELIMINAR</span>
                                    </div>`;
            }else{
                lista_producto.innerHTML += `<div style="margin-bottom: 10px;">
                                        + [<span style="color: green;">$`+itotal+`</span>]&nbsp;
                                        <span style="color: blue;"> `+icant+`</span>
                                        <span style="font-style: italic; color: blue;">"`+inomart+`"</span>
                                        <span style="color: black;">| </span>
                                        <span style="color: red; cursor: pointer;" onclick="quitarProductoCuenta(\'`+iid+`\',\'`+itotal+`\')">ELIMINAR</span>
                                    </div>`;
            }
        }
        
    }
}

function imprimirTotal(){
    var imprimir_total_transaccion = document.getElementById("imprimir-total-transaccion");
    imprimir_total_transaccion.innerHTML = `<div style="font-size: 20px;" id="imprimir-total-transaccion">Total: <span style="color: green; font-weight: bold;">$`+totalfinal_transaccion+` MXN</span></div>`;
}

function quitarPreventaCuenta(abono, total){

    var divpreventa = document.getElementById("cargar-articulo-preventa");
    divpreventa.innerHTML = `Sin pedido`;

    abono = parseFloat(abono);
    totalfinal_transaccion = totalfinal_transaccion + abono;
    totalfinal_transaccion = totalfinal_transaccion - total;

    listaPreventa.length = 0;

    console.log("el total de la transaccion queda en: "+totalfinal_transaccion);
    haypreventa = 'no';
    imprimirTotal();
}

function quitarProductoCuenta(id, total){

    cant_productos_en_transaccion--;

    let indiceAEliminar = listaDeProductos.findIndex(producto => producto.id === id);
    listaDeProductos.splice(indiceAEliminar, 1);

    total = parseFloat(total);
    console.log("Se va a restar: "+total);
    totalfinal_transaccion = totalfinal_transaccion - total;
    imprimirListaProductos();
    imprimirTotal();
}

function trySubirVenta(){
    if( cant_productos_en_transaccion == 0 && haypreventa == "no"){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Por favor añada almenos un artículo para la transacción</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1500);
                return; 
    }

    var tipo_pago = document.getElementById('tipo-pago-producto').value;
    var nombre_cliente = document.getElementById('nom').value;
    var tel = document.getElementById('tel').value;
    var email = document.getElementById('email').value;

    if(tipo_pago == '' || nombre_cliente == '' || tel == '' || email == ''){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">No ha llenado todos los valores</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1500);
                return; 
    }

    if( isNaN(tel) || tel % 1 !== 0 ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Numero de telefono invalido</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1500);
                return; 
    }

    if (tel.length !== 10) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">El numero de telefono debe ser de 10 digitos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    var printdatosconfirm = document.getElementById('print-datos-confirm');
    var labeltotal = document.getElementById('print-total');

    var pago = (tipo_pago == 'C') ? 'Efectivo' : (tipo_pago == 'T') ? 'Transferencia' : null;

    var fechaActual = new Date();
    var año = fechaActual.getFullYear();
    var mes = fechaActual.getMonth() + 1; // Los meses comienzan desde 0
    mes = (mes < 10 ? '0' : '') + mes;
    var dia = fechaActual.getDate();
    dia = (dia < 10 ? '0' : '') + dia;

    var fechaFormateada = año + '-' + (mes < 10 ? '0' : '') + mes + '-' + (dia < 10 ? '0' : '') + dia;
    var fecha = dia + " / " + mes + " / " + año;

    var horaActual = fechaActual.getHours();
    horaActual = (horaActual < 10 ? '0' : '') + horaActual;
    var minutos = fechaActual.getMinutes();
    minutos = (minutos < 10 ? '0' : '') + minutos;
    var segundos = fechaActual.getSeconds();
    segundos = (segundos < 10 ? '0' : '') + segundos;

    var hora = horaActual + ":" + minutos;
    var horaFormateada = horaActual + ":" + minutos + ":" + segundos;


    var cantidad_de_productos = cant_productos_en_transaccion + (haypreventa === "yes" ? 1 : 0);
    var existepreventa = haypreventa === "yes" ? "Si" : "No";

    printdatosconfirm.innerHTML = `<ul>
                                <li><label for="nombre">Cantidad de Productos: <span style="color: blue; font-style: italic; font-weight: bold;">`+cantidad_de_productos+`</span></label></li>

                                <li><label for="nombre">Pedido: <span style="color: blue;">`+existepreventa+`</span></label></li>
                    
                                <li><label for="nombre">Tipo pago: <span style="color: blue;">`+pago+`</span></label></li>
                        
                                <li><label for="nombre">Nombre de Cliente: <span style="color: blue;">`+nombre_cliente+`</span></label></li>
                
                                <li><label for="nombre">Tel de Cliente: <span style="color: blue;">`+tel+`</span></label></li>

                                <li><label for="nombre">Correo de Cliente: <span style="color: blue;">`+email+`</span></label></li>
                                
                                <li><label for="nombre">Fecha de la Transaccion: <span style="color: blue;">`+fecha+`</span></label></li>

                                <li><label for="nombre">Hora de la Transaccion: <span style="color: blue;">`+hora+`</span></label></li>
                            </ul>`;

    labeltotal.innerHTML = `&nbsp;&nbsp;<label for="nombre">Total: <span style="color: green; font-weight: bold;">$`+totalfinal_transaccion+` MXN</span></label>`;

    

    panelconfirmarventa.show();

    var boton_subir = document.getElementById('boton-subir-datos-preventa');
    
    boton_subir.innerHTML = `<input type="button" class="boton-generar-venta" value="Confirmar Venta" onclick="subirDatos
    (\'`+totalfinal_transaccion+`\',\'`+tipo_pago+`\',\'`+fechaFormateada+`\',\'`+nombre_cliente+`\',\'`+tel+`\',\'`+email+`\',\'`+horaFormateada+`\')">&nbsp;&nbsp;`;

    
}

function subirDatos(total,tipo_pago, fecha, nomcliente, tel, email, hra){

    var idpreventa = (haypreventa == 'no') ? 1 : listaPreventa[0].id;

    $.ajax({
        type: "POST",
        data: {"subir-datos-abd" : "var", 
        "total" : total, "tipo_pago" : tipo_pago, "fecha" : fecha,
        "nomcliente" : nomcliente, "tel" : tel, "email" : email, "hra" : hra, "idpreventa" : idpreventa},
        url: "php/ventas.php",
        success: function(response) {
            if(response == "yes"){

                if( (haypreventa == 'no')){

                    console.log("Entro a No hay preventa");

                    let datosaenviar = JSON.stringify({ listaDeProductos });

                    $.ajax({
                        type: "POST",
                        data: {"subir-lista-productos1" : "var", "datos" : datosaenviar, "cant-productos" : cant_productos_en_transaccion},
                        url: "php/ventas.php",
                        success: function(response2) {
                            
                            if(response2 == "yes"){
                                panelconfirmarventa.hide();
                                var enlace = document.getElementById('enlace-comprobante');

                                $.ajax({
                                    type: "POST",
                                    data: {"generar-ticket-venta" : "var" },
                                    url: "php/ventas.php",
                                    success: function(response3) {
                                        if(response == "yes"){
                                            enlace.innerHTML = response3;
                                            panelComprobante.show();
                
                                            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se han subido los datos correctamente</p>`;
                                            avisoEmergente.show();
                                            setTimeout(function() {
                                                avisoEmergente.hide();
                                            }, 1500);
                                            return; 
                                        }
                                        
                                    }
                                });          
                                
                                
                            }

                            console.log(response2);
                            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Error al subir los datos</p>`;
                            avisoEmergente.show();
                            setTimeout(function() {
                                avisoEmergente.hide();
                            }, 2500);

                            
                        }

                        
                    });

                }

                if( (haypreventa == 'yes') && cant_productos_en_transaccion > 0 ){

                    console.log("Entro a SI hay preventa y articulos mayor a 1");
                    let datosaenviar = JSON.stringify({ listaDeProductos });

                    $.ajax({
                        type: "POST",
                        data: {"subir-lista-productos2" : "var", "datos" : datosaenviar, "cant-productos" : cant_productos_en_transaccion,  "idprev" : listaPreventa[0].idpruducto,
                        "nomprev" : listaPreventa[0].nombre, "cantprev" : listaPreventa[0].cantidad, "precioprev" : listaPreventa[0].preciou, "abonoprev" : listaPreventa[0].abono},
                        url: "php/ventas.php",
                        success: function(response2) {
                            
                            if(response2 == "yes"){
                                panelconfirmarventa.hide();
                                var enlace = document.getElementById('enlace-comprobante');
        
                                $.ajax({
                                    type: "POST",
                                    data: {"generar-ticket-venta" : "var" },
                                    url: "php/ventas.php",
                                    success: function(response3) {
                                        if(response == "yes"){
                                            enlace.innerHTML = response3;
                                            panelComprobante.show();
                
                                            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se han subido los datos correctamente</p>`;
                                            avisoEmergente.show();
                                            setTimeout(function() {
                                                avisoEmergente.hide();
                                            }, 1500);
                                            return; 
                                        }
                                        
                                    }
                                });  
                            }

                            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Error al subir los datos</p>`;
                            avisoEmergente.show();
                            setTimeout(function() {
                                avisoEmergente.hide();
                            }, 2500);

                            console.log(response2);
                            
                        }

                        
                    });
                }

                if( (haypreventa == 'yes') && cant_productos_en_transaccion == 0 ){

                    $.ajax({
                        type: "POST",
                        data: {"subir-lista-productos3" : "var", "idprev" : listaPreventa[0].idpruducto,
                        "nomprev" : listaPreventa[0].nombre, "cantprev" : listaPreventa[0].cantidad, "precioprev" : listaPreventa[0].preciou, "abonoprev" : listaPreventa[0].abono},
                        url: "php/ventas.php",
                        success: function(response2) {
                            
                            if(response2 == "yes"){
                                panelconfirmarventa.hide();
                                var enlace = document.getElementById('enlace-comprobante');
        
                                $.ajax({
                                    type: "POST",
                                    data: {"generar-ticket-venta" : "var" },
                                    url: "php/ventas.php",
                                    success: function(response3) {
                                        if(response == "yes"){
                                            enlace.innerHTML = response3;
                                            panelComprobante.show();
                
                                            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se han subido los datos correctamente</p>`;
                                            avisoEmergente.show();
                                            setTimeout(function() {
                                                avisoEmergente.hide();
                                            }, 1500);
                                            return; 
                                        }
                                        
                                    }
                                });          
                            }

                            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Error al subir los datos</p>`;
                            avisoEmergente.show();
                            setTimeout(function() {
                                avisoEmergente.hide();
                            }, 2500);

                            console.log(response2);
                            
                        }

                        
                    });
                }

                


                
                
                
            }

            
            
        }
    });
}

