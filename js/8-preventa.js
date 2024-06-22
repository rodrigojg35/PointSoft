const productos = document.getElementById("form-productos");
const fechahora = document.getElementById("form-fechahora");
var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));
var msjconfirm = document.getElementById('mensaje-confirmacion');

var modal_comprobante = new bootstrap.Modal(document.getElementById('Modal-para-comprobante'));


var precio_productoform = document.getElementById('form-precio-producto');





var confirmpreventa = new bootstrap.Modal(document.getElementById('confirmacion-preventa'));

imprimirProductos();



function imprimirProductos(){

    $.ajax({
        type: "POST",
        data: {"imprimir-productos" : "var"},
        url: "php/preventas.php",
        success: function(response) {
            productos.innerHTML = response;
            var select_producto = document.getElementById('producto');

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
                url: "php/preventas.php",
                success: function(response) {
                    fechahora.innerHTML = response;
                }
            });
        }
    });
}

function tryGenerarPreventa(){
    var idyprecio = document.getElementById("producto").value;
    var cant = document.getElementById("cantidad").value;
    var tipo_pago = document.getElementById("tipo-pago").value;
    var abono = document.getElementById("abono").value;
    var cliente = document.getElementById("nom").value;
    var tel = document.getElementById("tel").value;
    var email = document.getElementById("email").value;
    var cantmax = document.getElementById("cantidad-max").value;
    console.log(cant);

    if( idyprecio == '' || cant == '' || tipo_pago == '' || abono == '', cliente == '' || tel == '' || email == ''){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Por favor llenar todos los campos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if ( isNaN(cant) || isNaN(abono) || isNaN(tel) ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha ingresado un tipo de dato incorrecto. Favor de Revisar</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if( parseInt(cant) > parseInt(cantmax)){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad sobrepasa el stock</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if( cant < 0 || abono < 0){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">No se aceptan valores negativos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if( cant == 0 ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad minima para preordenar es 1</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if( abono < 100 ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">El abono mínimo es de $100</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if (cant % 1 !== 0 || tel % 1 !== 0) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Ha ingresado un valor incorrecto</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    

    if (tel.length !== 10) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">El numero de telefono debe ser de 10 digitos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }
    


    var separaridprecio = idyprecio.split(',');

    var id = separaridprecio[0];
    var precio = separaridprecio[1];
    var nomart = separaridprecio[2];
    
    var total = precio * cant;

    if( abono > total){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">El abono es mayor que el costo total !</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    // A partir de aqui, los datos fueron validados y solo falta confirmar
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

    var printdatosconfirm = document.getElementById('print-datos-confirm');
    var labeltotal = document.getElementById('print-total');

    printdatosconfirm.innerHTML = `<ul>
                                <li><label for="nombre">Producto: <span style="color: blue; font-style: italic; font-weight: bold;">"`+nomart+`"</span></label></li>

                                <li><label for="nombre">Precio unitario: <span style="color: blue;">$`+precio+` MXN</span></label></li>
                
                                <li><label for="nombre">Cantidad: <span style="color: blue;">`+cant+`</span></label></li>
                    
                                <li><label for="nombre">Tipo pago: <span style="color: blue;">`+pago+`</span></label></li>
        
                                <li><label for="nombre">Cantidad a abonar: <span style="color: blue;">$`+abono+` MXN</span></label></li>
                        
                                <li><label for="nombre">Nombre de Cliente: <span style="color: blue;">`+cliente+`</span></label></li>
                
                                <li><label for="nombre">Tel de Cliente: <span style="color: blue;">`+tel+`</span></label></li>

                                <li><label for="nombre">Correo de Cliente: <span style="color: blue;">`+email+`</span></label></li>
                                
                                <li><label for="nombre">Fecha de la Transaccion: <span style="color: blue;">`+fecha+`</span></label></li>

                                <li><label for="nombre">Hora de la Transaccion: <span style="color: blue;">`+hora+`</span></label></li>
                            </ul>`;

    labeltotal.innerHTML = `&nbsp;&nbsp;<label for="nombre">Total: <span style="color: green; font-weight: bold;">$`+abono+` MXN</span></label>`;

    confirmpreventa.show();

    var boton_subir = document.getElementById('boton-subir-datos-preventa');
    
    boton_subir.innerHTML = `<input type="button" value="Confirmar Pedido" onclick="subirDatos
    (\'`+id+`\',\'`+cant+`\',\'`+precio+`\',\'`+abono+`\',\'`+tipo_pago+`\',\'`+fechaFormateada+`\',\'`+cliente+`\',\'`+tel+`\',\'`+email+`\',\'`+horaFormateada+`\')">&nbsp;&nbsp;`;

}

function subirDatos(id_prod, cant, preciou, abono, tipopago, fecha, nombre, tel, email, hra){

    $.ajax({
        type: "POST",
        data: {"subir-datos-abd" : "var", 
        "id_prod" : id_prod, "cant" : cant, "preciou" : preciou, "abono" : abono, "tipopago" : tipopago,
        "fecha" : fecha, "nombre" : nombre, "tel" : tel, "email" : email, "hra" : hra},
        url: "php/preventas.php",
        success: function(response) {
            if(response == "yes"){
                confirmpreventa.hide();
                var enlace = document.getElementById('enlace-comprobante');

                $.ajax({
                    type: "POST",
                    data: {"generar-ticket-preventa" : "var" },
                    url: "php/preventas.php",
                    success: function(response2) {
                        if(response == "yes"){
                            enlace.innerHTML = response2;
                            modal_comprobante.show();

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

            console.log(response);
            
            
        }
    });
}

