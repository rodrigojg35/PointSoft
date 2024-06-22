const tabla = document.getElementById('tabla-usuarios');
var Modal = new bootstrap.Modal(document.getElementById('miModal'));
var avisoEmergente = new bootstrap.Modal(document.getElementById('mensajeEmergenteModal'));
var msjconfirm = document.getElementById('mensaje-confirmacion');
var ModalBorrar = new bootstrap.Modal(document.getElementById('miModal2'));

var hdreliminar = document.getElementById('header-paneleliminar');
var bodyeliminar = document.getElementById('body-paneleliminar');
var btneliminar = document.getElementById('btn-paneleliminar');

var form_usuario = new bootstrap.Modal(document.getElementById('añadir-usuario'));

imprimirPanelUsuarios();

var action = "inactivar";

function imprimirPanelUsuarios(){
       /*
    tabla.innerHTML = `<table id="example" class="stripe cell-border" style="width:100%; background-color: white; border: 1px solid black;">

    <thead>
        <tr>
            <th style="text-align: center;">No.Usuario</th>
            <th style="text-align: center;">Usuario</th>
            <th style="text-align: center;">Tipo</th>
            <th style="text-align: center;">Telefono</th>
            <th style="text-align: center;">Correo</th>
            <th style="text-align: center;">Estado</th>
            <th>   </th>
        </tr>
    </thead>
    <tbody>

    <tr>
            <td>1</td>
            <td>Rodrigo</td>
            <td>Empleado</td>
            <td>2293384024</td>
            <td>ro.jg01@hotmail.com</td>
            <td style="color: green;">Activo</td>
            <td>
                <a href="6-ver-clientes.php" style="text-decoration: none;color: red; text-decoration: none;"> X </a>
            </td>
    </tr>

    <tr>
            <td>2</td>
            <td>Pablo</td>
            <td>Empleado</td>
            <td>2293384024</td>
            <td>ro.jg01@hotmail.com</td>
            <td style="color: gray;">Inactivo</td>
            <td>
                <a href="6-ver-clientes.php" style="text-decoration: none;color: blue; text-decoration: none;">←</a>
            </td>
    </tr>

    <tr>
            <td>3</td>
            <td>Administrador</td>
            <td style="color: blue;">Admin</td>
            <td>2293384024</td>
            <td>ro.jg01@hotmail.com</td>
            <td style="color: green;">Activo</td>
            <td>
                <a href="6-ver-clientes.php" style="text-decoration: none;color: red; text-decoration: none;">X</a>
            </td>
    </tr>

     </tbody>
    </table>`;*/

    
    $.ajax({
        type: "POST",
        data: {"imprimirPanel" : "var"},
        url: "php/panel-usuarios.php",
        success: function(response) { 
            console.log(response);
            tabla.innerHTML = response;

            $(document).ready(function() {
                $('#example').DataTable( {
                    pageLength : 50,
                    "order": [[ 0, "asc" ]]
                } );
            } );
        }
    });
}

function trySubirUsuario(){
    var user = document.getElementById("usuario").value;
    var pass = document.getElementById("contrasena").value;
    var tel = document.getElementById("telefono").value;
    var email = document.getElementById("correo").value;

    if(user == '' || pass == '' || tel == '' || email == ''){
        msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Por favor llenar todos los campos</p>`;
        avisoEmergente.show();
        setTimeout(function() {
            avisoEmergente.hide();
        }, 1000);
        return; // Detener la ejecución de la función si hay campos vacíos
    }
    
    $.ajax({
        type: "POST",
        data: {"comprobar-usuario" : "var", "user" : user},
        url: "php/panel-usuarios.php",
        success: function(response) { 
            console.log(response);
            if(response == "yes"){    //  el usuario ya existe
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Ese usuario ya existe</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                }, 1000);
            }

            if(response == "no"){    //  el usuario no existe
                Modal.show();
            }
        }
    });

    
}

function crearUsuario(){
    var user = document.getElementById("usuario").value;
    var pass = document.getElementById("contrasena").value;
    var tel = document.getElementById("telefono").value;
    var email = document.getElementById("correo").value;

    $.ajax({
        type: "POST",
        data: {"subir-usuario" : "var", "user" : user, "pass" : pass, "tel" : tel, "email" : email},
        url: "php/panel-usuarios.php",
        success: function(response) { 
            console.log(response);

            if(response == "yes"){    //  el usuario ya existe
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha agregado al usuario con éxito</p>`;
                form_usuario.hide();
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirPanelUsuarios();
                    document.getElementById("usuario").value = "";
                    document.getElementById("contrasena").value = "";
                    document.getElementById("telefono").value = "";
                    document.getElementById("correo").value = "";
                }, 1000);
            }
            
        }
    });
}

function VentanaBorrar(id){

        console.log("el id a borrar es: "+id);
        hdreliminar.innerHTML = `<h5 class="modal-title">¿Esta seguro de <span style="font-weight: bold;">eliminar</span> el usuario?</h5> 
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>`;
        bodyeliminar.innerHTML = `<p>El usuario quedará eliminado del sistema</p>`;
        btneliminar.innerHTML = `<button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close" onclick="borrarUsuario('`+id+`')">Eliminar</button>`;
        ModalBorrar.show();  

}

function VentanaInactivar(id){

        console.log("el id a inactivar es: "+id);
        hdreliminar.innerHTML = `<h5 class="modal-title">¿Esta seguro de <span style="font-weight: bold;">inactivar</span> el usuario?</h5> 
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>`;
        bodyeliminar.innerHTML = `<p>El usuario no se puede eliminar debido a que ya tiene transacciones realizadas</p>`;
        btneliminar.innerHTML = `<button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="inactivarUsuario('`+id+`')">Inactivar</button>`;
        ModalBorrar.show();
  
}

function abrirVentanaVolverActivar(id){

    console.log("el id a reactivar es: "+id);
    hdreliminar.innerHTML = `<h5 class="modal-title">¿Esta seguro de <span style="font-weight: bold;">reactivar</span> el usuario?</h5> 
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>`;
    bodyeliminar.innerHTML = `<p>El usuario volverá a encontrarse activo para su uso.</p>`;
    btneliminar.innerHTML = `<button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="reactivarUsuario('`+id+`')">Reactivar</button>`;
    ModalBorrar.show();
    
}

function borrarUsuario(id){
    $.ajax({
        type: "POST",
        data: {"borrarUsuario" : "var", "id" : id},
        url: "php/panel-usuarios.php",
        success: function(response) { 
            console.log(response);

            if(response == "yes"){   
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha eliminado al usuario con éxito</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirPanelUsuarios();
                }, 1000);
            }
            
        }
    });
}

function inactivarUsuario(id){
    $.ajax({
        type: "POST",
        data: {"inactivarUsuario" : "var", "id" : id},
        url: "php/panel-usuarios.php",
        success: function(response) { 
            console.log(response);

            if(response == "yes"){   
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha inactivado al usuario con éxito</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirPanelUsuarios();
                }, 1000);
            }
            
        }
    });
}

function reactivarUsuario(id){
    $.ajax({
        type: "POST",
        data: {"reactivarUsuario" : "var", "id" : id},
        url: "php/panel-usuarios.php",
        success: function(response) { 
            console.log(response);

            if(response == "yes"){   
                msjconfirm.innerHTML = `<p id="mensajeEmergenteTexto">Se ha reactivado al usuario con éxito</p>`;
                avisoEmergente.show();
                setTimeout(function() {
                    avisoEmergente.hide();
                    imprimirPanelUsuarios();
                }, 1000);
            }
            
        }
    });
}

function abrirFormUsuario(){
    form_usuario.show();
}
