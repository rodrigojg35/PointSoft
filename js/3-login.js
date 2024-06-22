const titulo = document.getElementById("titulo-panel");
const select = document.getElementById("usuario");
var Modal = new bootstrap.Modal(document.getElementById('miModal'));
var Modal2 = new bootstrap.Modal(document.getElementById('miModal2'));
var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));
var msjconfirm = document.getElementById('mensaje-confirmacion');




imprimirUsuarios(panel_login);

function imprimirUsuarios(usuarios){

    if(usuarios == "emp"){
        $.ajax({
            type: "POST",
            data: {"imprimir_tipo" : "emp"},
            url: "php/login.php",
            success: function(response) { 
                select.innerHTML = response;
            }
        });
    }

    if(usuarios == "admin"){
        $.ajax({
            type: "POST",
            data: {"imprimir_tipo" : "admin"},
            url: "php/login.php",
            success: function(response) { 
                select.innerHTML = response;
            }
        });
    }
    

}

function tryIniciarSesion(){

    var user = document.getElementById("usuario").value;
    var pass = document.getElementById("contraseña").value;

    if (user === "" || pass === "") {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Por favor llenar todos los campos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1000);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    Modal.show();

}

function validarInicioSesion(tipo){

    var user = document.getElementById("usuario").value;
    var pass = document.getElementById("contraseña").value;

    $.ajax({
        type: "POST",
        data: {"user" : user, "pass" : pass, "tipo" : tipo},
        url: "php/login.php",
        success: function(response) { 

            if(response == "yes"){

                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Ha iniciado sesión correctamente</p>`;
                
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    avisoEmergente._element.addEventListener('hidden.bs.modal', function () {
                        window.location = "4-panel.html";
                    });
                }, 1000);
            }

            if(response == "no"){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La información es incorrecta</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1000);
            }
        }
    });

}


function volverAlIndex(){
    window.location="index.html";
}

function usarAjax(){

    $.ajax({
        type: "POST",
        data: {"nombre" : n, "apellido" : a, "telefono" : t},
        url: "php/login.php",
        success: function(response) { 
            titulo.innerHTML = '<h1>'+response+'</h1>';
        },
        error: function(jqXHR, textStatus, errorThrown){
            //if(textStatus === 'timeout'){alert('Failed from timeout');}   
            if (jqXHR.status === 0) {alert('Not connect: Verify Network: ' + textStatus);}
            else if (jqXHR.status == 404) {alert('Requested page not found [404]');} 
            else if (jqXHR.status == 500) {alert('Internal Server Error [500].');}
            else if (textStatus === 'parsererror') {alert('Requested JSON parse failed.');}
            else if (textStatus === 'timeout') {alert('Time out error.');} 
            else if (textStatus === 'abort') {alert('Ajax request aborted.');} 
            else {alert('Uncaught Error: ' + jqXHR.responseText);}
        }
    });
        
    
}

