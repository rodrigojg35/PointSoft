
var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));

imprimirDatosHeader();

function imprimirDatosHeader(){

    var header = document.getElementById('datos-header');

    $.ajax({
        type: "POST",
        data: {"header-cierre" : "var"},
        url: "php/cierre.php",
        success: function(response) {
            header.innerHTML = response;
            imprimirTransacciones();
        }
    });


}

function imprimirTransacciones(){
    
    var transacciones = document.getElementById('tabla-transacciones');

    $.ajax({
        type: "POST",
        data: {"imprimir-transacciones" : "var"},
        url: "php/cierre.php",
        success: function(response) {
            transacciones.innerHTML = response;
            imprimirTotales();
        }
    });

    var canttransacciones = document.getElementById('cant-transacciones');

    $.ajax({
        type: "POST",
        data: {"canttransacciones-cierre" : "var"},
        url: "php/cierre.php",
        success: function(response) {
            canttransacciones.innerHTML = response;
        }
    });

                                
}

function imprimirTotales(){

    var totales = document.getElementById('totales');
    var total = document.getElementById('total');

    $.ajax({
        type: "POST",
        data: {"totales-cierre" : "var"},
        url: "php/cierre.php",
        success: function(response) {
            totales.innerHTML = response;
            $.ajax({
                type: "POST",
                data: {"total-cierre" : "var"},
                url: "php/cierre.php",
                success: function(response) {
                    total.innerHTML = response;
                    cerrarCaja();
                }
            });
        }
    });


}

function regresarPanel(){
    location.replace('4-panel.html');
}

function cerrarCaja(){

    $.ajax({
        type: "POST",
        data: {"cerrar-caja" : "var"},
        url: "php/cierre.php",
        success: function(response) {

            if(response == "yes"){
                var msjconfirm = document.getElementById('mensaje-confirmacion');
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Caja cerrada con éxito</p>`;
                imprimirComprobante();

                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1000);
                return;
            }


            var msjconfirm = document.getElementById('mensaje-confirmacion');
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Error al cerrar la caja</p>`;

                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1000);   

            
                

        }
    });

    

}

function imprimirComprobante(){

    var enlace = document.getElementById('imprimir-comprobante');

    $.ajax({
        type: "POST",
        data: {"generar-comprobante" : "var"},
        url: "php/cierre.php",
        success: function(response3) {
            console.log("El enlace a imprimir es: "+response3);
            enlace.innerHTML = response3;

        }
    });
}