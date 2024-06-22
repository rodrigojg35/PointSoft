imprimirTransacciones();
imprimirTotales();

var confirmarcerrarcaja = new bootstrap.Modal(document.getElementById('confirmar-cerrarcaja'));
var modaldetalles = new bootstrap.Modal(document.getElementById('Modal-detalles'));



function imprimirTransacciones(){

    var transacciones = document.getElementById('div-transacciones');

    $.ajax({
        type: "POST",
        data: {"reproducir-transacciones" : "var"},
        url: "php/caja.php",
        success: function(response) {
            transacciones.innerHTML = response;
            $(document).ready(function() {
                $('#tabla-transacciones').DataTable( {
                    "dom": 'rtip',
                    "bPaginate": false,
                    paging: false,
                    searching: false,
                    "bInfo": false,
                    "scrollY": "255px",
                    "scrollCollapse": true,
                    "language": {
                        "emptyTable": "Aún no se han realizado transacciones"
                      },
                    "order": [[ 3, "desc" ]]
                } );
            } );
        }
    });

   

    

}

function imprimirTotales(){

    var totales = document.getElementById('totales');

    $.ajax({
        type: "POST",
        data: {"reproducir-totales" : "var"},
        url: "php/caja.php",
        success: function(response) {
            totales.innerHTML = response;
        }
    });



}

function abrirDetalles(id, tipo){

    var titulomodal = document.getElementById('titulo-modaldetalles');
    var bodymodal = document.getElementById('body-modaldetalles');

    if(tipo == 'Preventa'){
        titulomodal.innerHTML = `<h5 class="modal-title" id="exampleModalLabel">Detalles de la Preventa #`+id+`:</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>`;
        
        $.ajax({
            type: "POST",
            data: {"verdatos-preventa" : "var", "id" : id},
            url: "php/caja.php",
            success: function(response) {
                bodymodal.innerHTML = response;
                modaldetalles.show();
            }
        });

    }

    if(tipo == 'Venta'){
        titulomodal.innerHTML = `<h5 class="modal-title" id="exampleModalLabel">Lista de artículos de la Venta #`+id+`:</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>`;
        
        $.ajax({
            type: "POST",
            data: {"verdatos-venta" : "var", "id" : id},
            url: "php/caja.php",
            success: function(response) {
                bodymodal.innerHTML = response;
                modaldetalles.show();
            }
        });
        
    }
    
}

function tryCerrarCaja(){
    confirmarcerrarcaja.show();
}

function cancelarCerrarCaja(){
    confirmarcerrarcaja.hide();
}

function cerrarCaja(){

     location.replace('11-cierrerealizado.html');
  

    
}




