var panelarticulo = new bootstrap.Modal(document.getElementById('panel-articulo'));
var contenedorproductos = document.getElementById('contenedor-productos');
var panelVerProducto = document.getElementById('panel-ver-producto');
var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));
var msjconfirm = document.getElementById('mensaje-confirmacion');

var form_articulo = new bootstrap.Modal(document.getElementById('añadir-articulo'));
var form_articulo2 = new bootstrap.Modal(document.getElementById('modificar-articulo'));


imprimirProductos();

function imprimirProductos(){

    $.ajax({
        type: "POST",
        data: {"imprimir-productos" : "var"},
        url: "php/articulos.php",
        success: function(response) {
            contenedorproductos.innerHTML = response;
        }
    });

    /*
    contenedorproductos.innerHTML = `<div class="producto">
    <img src="imagenes/Estructura.jpg" alt="Producto 1" width="240" height="240"><br>
    <div class="nombre-producto">"Structure Deck Neos"</div>
    <div class="precio">$500</div>
    <div class="existencia">Disponible</div>
    <button class="boton-agregar" onclick="verDetallesProducto()">Ver artículo</button>
    </div>    

    <div class="producto">
    <img src="imagenes/Latas.jpg" alt="Producto 1" width="240" height="240"><br>
    <div class="nombre-producto">"Structure Deck Neos"</div>
    <div class="precio">$300</div>
    <div class="existencia2">Agotado</div>
    <button class="boton-agregar" onclick="verDetallesProducto()">Ver artículo</button>
    </div>

    <div class="producto">
    <img src="imagenes/cajas.jpg" alt="Producto 1" width="240" height="240"><br>
    <div class="nombre-producto">"Structure Deck Neos"</div>
    <div class="precio">$400</div>
    <div class="existencia">Disponible</div>
    <button class="boton-agregar" onclick="verDetallesProducto()">Ver artículo</button>
    </div>


    <div class="producto">
    <img src="imagenes/Estructura.jpg" alt="Producto 1" width="240" height="240"><br>
    <div class="nombre-producto">"Structure Deck Neos"</div>
    <div class="precio">$500</div>
    <div class="existencia">Disponible</div>
    <button class="boton-agregar" onclick="verDetallesProducto()">Ver artículo</button><br><br>
    </div>    

    <div class="producto">
    <img src="imagenes/Latas.jpg" alt="Producto 1" width="240" height="240"><br>
    <div class="nombre-producto">"Structure Deck Neos"</div>
    <div class="precio">$300</div>
    <div class="existencia2">Agotado</div>
    <button class="boton-agregar" onclick="verDetallesProducto()">Ver artículo</button><br><br>
    </div>

    <div class="producto">
    <img src="imagenes/cajas.jpg" alt="Producto 1" width="240" height="240"><br>
    <div class="nombre-producto">"Structure Deck Neos"</div>
    <div class="precio">$400</div>
    <div class="existencia">Disponible</div>
    <button class="boton-agregar" onclick="verDetallesProducto()">Ver artículo</button><br>
    </div>`; */
}

function añadirProducto(){
    form_articulo.show();
}

function modificarArticulo(id){

    //console.log("Se dio click al producto con id: "+id);
    
    $.ajax({
        type: "POST",
        data: {"obtenerDatosProducto" : "var", "id" : id},
        url: "php/articulos.php",
        success: function(response) {
            
            var datos = JSON.parse(response);
            var nombre = document.getElementById('modify-nombre');
            var descr = document.getElementById('modify-descr');
            var precio = document.getElementById('modify-precio');
            var cantidad = document.getElementById('modify-cantidad');
            var checkbox = document.getElementById('modify-checkbox');

            nombre.value = datos.nombre;
            descr.value = datos.descr;
            precio.value = datos.precio;
            cantidad.value = datos.cantidad;

            preventa = datos.preventa;
            if(preventa == "S"){
                checkbox.checked = true;
            }else{
                checkbox.checked = false;
            }

            var boton = document.getElementById('modify-boton');
            boton.innerHTML = `<input type="button" value="Actualizar información producto" onclick="tryModificarProducto(${id})">`;
            
            form_articulo2.show();  
            
        }
    }); 

    
}



function trySubirProducto(){
    var nom = document.getElementById('form-nombre').value;
    var descr = document.getElementById('form-descr').value;
    var cant = document.getElementById('form-cantidad').value;
    var precio = document.getElementById('form-precio').value;
    var preventa = document.getElementById('checkbox');

    preventa = preventa.checked;

    if(nom == "" || cant == "" || precio == ""){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Por favor llenar todos los campos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1000);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if ( isNaN(precio) || isNaN(cant) ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha ingresado un tipo de dato incorrecto. Favor de Revisar</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if ( precio < 0 || cant < 0 ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Favor de ingresar solo datos positivos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if (cant % 1 !== 0) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad debe ser un valor entero !</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    if (cant == 0) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad no puede ser 0 !</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    var inputImagen = document.getElementById('form-imagen');

    if (inputImagen.files.length > 0) {
        var nombreArchivo = inputImagen.files[0].name;

        if( nombreArchivo.endsWith('.jpg') || nombreArchivo.endsWith('.JPG') || nombreArchivo.endsWith('.png') || nombreArchivo.endsWith('.PNG') ){
            console.log("Archivo valido");

            var formData = new FormData();
            formData.append('nombre', nom);
            formData.append('descr', descr);
            formData.append('cantidad', cant);
            formData.append('precio', precio);
            formData.append('preventa', preventa);
            formData.append('imagen', inputImagen.files[0]);

            $.ajax({
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                url: "php/articulos.php",
                success: function(response) {
                    console.log(response);
                    if(response == "yes"){
                        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha añadido el producto con exito</p>`;
                        avisoEmergente.show();
                        setTimeout(function() {
                            avisoEmergente.hide();
                            document.getElementById('form-nombre').value = ''
                            document.getElementById('form-cantidad').value = 1;
                            document.getElementById('form-precio').value = '';
                            document.getElementById('checkbox').checked = false;
                            document.getElementById('form-imagen').value = '';
                            imprimirProductos();
                        }, 1500); 
                    }
                }
            });




        }else{
            msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Favor de ingresar solo imagenes .jpg o .png</p>`;
            avisoEmergente.show();
            setTimeout(function() {
                avisoEmergente.hide();
            }, 1500);
            return; 
        }

      }else{
        console.log("No archivo");
        $.ajax({
            type: "POST",
            data: {"subir-producto-noimg" : "var", "nom" : nom, "descr" : descr, "cant" : cant, "precio" : precio, "preventa" : preventa},
            url: "php/articulos.php",
            success: function(response) {
                if(response == "yes"){
                    msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha añadido el producto con exito</p>`;
                    avisoEmergente.show();
                    setTimeout(function() {
                        avisoEmergente.hide();
                        document.getElementById('form-nombre').value = '';
                        document.getElementById('form-descr').value = '';
                        document.getElementById('form-cantidad').value = 1;
                        document.getElementById('form-precio').value = '';
                        document.getElementById('checkbox').checked = false;
                        imprimirProductos();
                    }, 1500); 
                }
            }
        });
      }

}

function tryModificarProducto(id){
    var nom = document.getElementById('modify-nombre').value;
    var descr = document.getElementById('modify-descr').value;
    var cant = document.getElementById('modify-cantidad').value;
    var precio = document.getElementById('modify-precio').value;
    var preventa = document.getElementById('modify-checkbox');

    preventa = preventa.checked;

    if(nom == "" || cant == "" || precio == ""){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Por favor llenar todos los campos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1000);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if ( isNaN(precio) || isNaN(cant) ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha ingresado un tipo de dato incorrecto. Favor de Revisar</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if ( precio < 0 || cant < 0 ){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Favor de ingresar solo datos positivos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos
    }

    if (cant % 1 !== 0) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad debe ser un valor entero !</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }

    if (cant == 0) {
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">La cantidad no puede ser 0 !</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1500);
        return; // Detener la ejecución de la función si hay campos vacíos   
    }
    
    $.ajax({
        type: "POST",
        data: {"modificar-producto" : "var", "id" : id, "nom" : nom, "descr" : descr, "cant" : cant, "precio" : precio, "preventa" : preventa},
        url: "php/articulos.php",
        success: function(response) {
            if(response == "yes"){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha modificado el producto con exito</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();

                    imprimirProductos();
                }, 1500); 
            }
        }
    });
    

}


function verDetallesProducto(id){

    console.log("Se dio click al producto con id: "+id);

    $.ajax({
        type: "POST",
        data: {"abrirPanelProducto" : "var", "producto" : id},
        url: "php/articulos.php",
        success: function(response) {
            panelVerProducto.innerHTML = response;
            panelarticulo.show();
        }
    });

    
}

function eliminarProducto(id){

    $.ajax({
        type: "POST",
        data: {"eliminarProducto" : "var", "id" : id},
        url: "php/articulos.php",
        success: function(response) {
            if(response == 'yes'){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha eliminado el producto con exito</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    panelarticulo.hide();
                    imprimirProductos();
                }, 1000); 
            }
            
            
        }
    });
}

function setNoDisp(id){

    
    $.ajax({
        type: "POST",
        data: {"ponerProductoNoDisp" : "var", "id" : id},
        url: "php/articulos.php",
        success: function(response) {
            if(response == 'yes'){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha puesto el producto como No Disponible con exito</p>`;
                avisoEmergente.show();
                panelarticulo.hide();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirProductos();
                }, 1000); 
            }
            
            
        }
    }); 
}

function setNoDispPrev(id){

    
    $.ajax({
        type: "POST",
        data: {"ponerProductoNoDispPrev" : "var", "id" : id},
        url: "php/articulos.php",
        success: function(response) {
            if(response == 'yes'){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha puesto el producto como No Disponible con exito</p>`;
                avisoEmergente.show();
                panelarticulo.hide();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirProductos();
                }, 1000); 
            }
            
            
        }
    }); 
}

function setDisp(id){

    
    $.ajax({
        type: "POST",
        data: {"ponerProductoDisp" : "var", "id" : id},
        url: "php/articulos.php",
        success: function(response) {
            if(response == 'yes'){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha puesto el producto como Disponible con exito</p>`;
                avisoEmergente.show();
                panelarticulo.hide();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirProductos();
                }, 1000); 
            }
            
            
        }
    }); 
}

function setDispFromPrev(id){

    
    $.ajax({
        type: "POST",
        data: {"ponerProductoDispFromPrev" : "var", "id" : id},
        url: "php/articulos.php",
        success: function(response) {
            if(response == 'yes'){
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha puesto el producto como Disponible con exito</p>`;
                avisoEmergente.show();
                panelarticulo.hide();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirProductos();
                }, 1000); 
            }
            
            
        }
    }); 
}