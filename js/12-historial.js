var mespreventa = document.getElementById('mes-preventa');

mespreventa.addEventListener('change', function() {
    var nuevomes = mespreventa.value;
    console.log("Se cambió el mes de preventa a:" + nuevomes);
    imprimirTablaPreventas(nuevomes);
  });

var mesventa = document.getElementById('mes-venta');

  mesventa.addEventListener('change', function() {
    var nuevomes = mesventa.value;
    console.log("Se cambió el mes de venta a:" + nuevomes);
    imprimirTablaVentas(nuevomes);
  });

var mescaja = document.getElementById('mes-caja');

mescaja.addEventListener('change', function() {
var nuevomes = mescaja.value;
console.log("Se cambió el mes de caja a:" + nuevomes);
imprimirTablaCajas(nuevomes);
});


imprimirTablaPreventas("06");
imprimirTablaVentas("06");
imprimirTablaCajas("06");

function imprimirTablaPreventas(mes){

    var tablapreventas = document.getElementById('div-tablapreventa');

    $.ajax({
        type: "POST",
        data: {"imprimir-preventas" : "var", "mes" : mes},
        url: "php/historial.php",
        success: function(response) {
            tablapreventas.innerHTML = response;

            $('#tabla-preventas').DataTable( {
                "dom": 'rtip',
                searching: false,
                "pageLength": 5,
                "language": {
                    "emptyTable": "No hay preventas de este mes"
                  },
                "order": [[ 0, "desc" ]]
            } );
            
        }
    });

    

    
}

function imprimirTablaVentas(mes){
    var tablaventas = document.getElementById('div-tablaventa');

    $.ajax({
        type: "POST",
        data: {"imprimir-ventas" : "var", "mes" : mes},
        url: "php/historial.php",
        success: function(response) {
            tablaventas.innerHTML = response;

            $('#tabla-ventas').DataTable( {
                "dom": 'rtip',
                searching: false,
                "pageLength": 5,
                "language": {
                    "emptyTable": "No hay ventas de este mes"
                  },
                "order": [[ 0, "desc" ]]
            } );
            
        }
    });

    
}

function imprimirTablaCajas(mes){
    var tablacajas = document.getElementById('div-tablacaja');


    $.ajax({
        type: "POST",
        data: {"imprimir-cajas" : "var", "mes" : mes},
        url: "php/historial.php",
        success: function(response) {
            tablacajas.innerHTML = response;

            $('#tabla-cajas').DataTable( {
                "dom": 'rtip',
                searching: false,
                "pageLength": 5,
                "language": {
                    "emptyTable": "No hay cierres de caja de este mes"
                  },
                "order": [[ 0, "desc" ]]
            } );
            
        }
    });

    
    
}


function imprimirComprobantePreventa(url){
    console.log(url);
    window.open(url, '_blank');
}

function imprimirComprobanteVenta(url){
    console.log(url);
    window.open(url, '_blank');
}

function imprimirComprobanteCaja(url){
    console.log(url);
    window.open(url, '_blank');
}
  
