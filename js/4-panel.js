const titulo = document.getElementById("titulo-panel");
const paneles = document.getElementById("paneles");
var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));
var msjconfirm = document.getElementById('mensaje-confirmacion');
const btncaja = document.getElementById("boton-abrir-caja");
const datoscaja = document.getElementById("datos-caja");
var Modal = new bootstrap.Modal(document.getElementById('miModal'));

var panel;   // Cambiar de Panel    emp = empleado   admin = administrador
var cajabierta;

consultarinicioSesion();          //  se valida que panel es

function consultarinicioSesion(){
    console.log("Consultando sesion...")
    $.ajax({
        url: "php/consultarsesion.php",
        success: function(response) { 

            if(response == "no"){
                window.location="index.html";
            }else{
                panel = response;
                console.log(panel);

                if(panel == 'emp'){
                    titulo.innerHTML = '<h1>Panel Empleado</h1>';
                    refrescarPanel();
                }
                
                if(panel == 'admin'){
                    titulo.innerHTML = '<h1>Panel Administrador</h1>';
                    refrescarPanel();
                    
                }
            }

            
        }
    });

}

function imprimirPanelAdmin(){
    paneles.innerHTML += `<div class="botones-panel" onclick="abrirPanel('usuarios')">
                        <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-user"></i>
                        </div>
                        <h2>Usuarios</h2>
                        </div>
                        </div>`;
}


function tryAbrirCaja(){Modal.show();}

function abrirCaja(){

    
    $.ajax({
        type: "POST",
        data: {"abrir-nueva-caja" : "var"},
        url: "php/abrircaja.php",
        success: function(response) {
            console.log(response);
            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Ha abierto caja exitosamente</p>`;
            avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    avisoEmergente._element.addEventListener('hidden.bs.modal', function () {
                        refrescarPanel();
                    });
                }, 1000);
        }
    });

    
}

function refrescarPanel(){

    $.ajax({
        type: "POST",
        data: {"consultar-caja-abierta" : "var"},
        url: "php/abrircaja.php",
        success: function(response) {
            console.log("Esta devolviendo un: "+response);
            if(response == "yes"){

                btncaja.innerHTML = `<button class="boton-abrir-caja" aria-pressed="true"><i class="fas fa-cash-register"></i>Abierto</button>`;

                $.ajax({
                    type: "POST",
                    data: {"consultar-ult-caja2" : "var"},
                    url: "php/abrircaja.php",
                    success: function(response) {
                        btncaja.innerHTML += response;
                    }
                });

                paneles.innerHTML = `<div class="botones-panel" onclick="abrirPanel('preventas')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-clipboard-list"></i>
                        </div>
                        <h2>Generar Pedido</h2>
                    </div>
                    </div>

                    <div class="botones-panel" onclick="abrirPanel('ventas')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-chart-line"></i>
                        </div>
                        <h2>Generar Venta</h2>
                    </div>
                    </div>

                    <div class="botones-panel" onclick="abrirPanel('productos')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-cube"></i>
                        </div>
                        <h2>Productos</h2>
                    </div>
                    </div>

                    <div class="botones-panel" onclick="abrirPanel('caja')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-cash-register"></i>
                        </div>
                        <h2>Caja</h2>
                    </div>
                    </div>  

                    <div class="botones-panel" onclick="abrirPanel('historial')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-history"></i>
                        </div>
                        <h2>Historial</h2>
                     </div>
                     </div>`;
                
            }

            if(response == "no"){
                btncaja.innerHTML = `<button class="boton-abrir-caja boton-activado" aria-pressed="true" onclick="tryAbrirCaja()"><i class="fas fa-cash-register"></i> Abrir Caja</button>`;
                $.ajax({
                    type: "POST",
                    data: {"consultar-ult-caja" : "var"},
                    url: "php/abrircaja.php",
                    success: function(response) {
                        btncaja.innerHTML += `<div class="datos-caja" id="datos-caja">
                                        <p class="datos-de-caja">Ultima fecha de apertura: <br><span style="color: lightblue;">`+response+`</span></p>
                                        </div>`;
                    }
                });

                paneles.innerHTML = `<div class="botones-panel panel-desactivado" onclick="botonPanelDesactivado()">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-clipboard-list"></i>
                        </div>
                        <h2>Generar Pedido</h2>
                    </div>
                    </div>

                    <div class="botones-panel panel-desactivado" onclick="botonPanelDesactivado()">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-chart-line"></i>
                        </div>
                        <h2>Generar Venta</h2>
                    </div>
                    </div>

                    <div class="botones-panel" onclick="abrirPanel('productos')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-cube"></i>
                        </div>
                        <h2>Articulos</h2>
                    </div>
                    </div>

                    <div class="botones-panel panel-desactivado" onclick="botonPanelDesactivado()">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-cash-register"></i>
                        </div>
                        <h2>Caja</h2>
                    </div>
                    </div>  

                    <div class="botones-panel" onclick="abrirPanel('historial')">
                    <div class="box">
                        <div class="icon-box">
                            <i class="icon fas fa-history"></i>
                        </div>
                        <h2>Historial</h2>
                     </div>
                     </div>`;
            }

            if(panel == 'admin'){imprimirPanelAdmin();}
        }
    });
    
}

function abrirPanel(seleccion_panel){

    if(seleccion_panel == "preventas"){
        console.log("Se selecciono el panel de preventa");
        window.location = "5-preventa.html";
    }

    if(seleccion_panel == "ventas"){
        console.log("Se selecciono el panel de venta");
        window.location = "6-venta.html";
    }

    if(seleccion_panel == "productos"){
        console.log("Se selecciono el panel de productos");
        window.location = "7-articulos.html";
    }

    if(seleccion_panel == "caja"){
        console.log("Se selecciono el panel de caja");
        window.location = "8-caja.html";
    }

    if(seleccion_panel == "historial"){
        console.log("Se selecciono el panel de historial");
        window.location = "12-historial.html";
    }

    if(seleccion_panel == "usuarios"){
        console.log("Se selecciono el panel de usuarios");
        window.location = "9-usuarios.html";
    }
}

function botonPanelDesactivado(){
    msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Esta función no está disponible si la caja está cerrada</p>`;
            avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1000);
}

function cerrarSesion(){

    $.ajax({
        url: "php/cerrar-sesion.php",
        success: function(response) { 
            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Ha cerrado la sesion correctamente</p>`;
            avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    avisoEmergente._element.addEventListener('hidden.bs.modal', function () {
                        window.location = "index.html";
                    });
                }, 1000);
        }
    });

    
}
