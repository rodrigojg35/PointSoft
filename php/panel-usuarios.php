<?php
    session_start();
    require 'conexion.php';

    if(isset($_POST['imprimirPanel'])){
        $result = $conn->query
        ("SELECT * FROM vista_usuarios_con_transacciones;");
        
        echo '<table id="example" class="stripe cell-border tabla-usuarios" style="width:100%; background-color: white; border: 1px solid black;">
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
        <tbody>';
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $id = $row['id_u'];
            $user = $row['user_u'];
            $tipo = $row['tipo_u'];
            $tel = $row['tel_u'];
            $email = $row['email_u'];
            $estado = $row['estado_u'];
            $transacciones = $row['total_transacciones'];

            if($tipo == "emp"){
                echo "<tr>
                <td>$id</td>
                <td><i class='icon fas fa-user'></i>&nbsp;&nbsp;$user</td>
                <td>Empleado</td>";
            }

            if($tipo == "admin"){
                echo "<tr>
                <td>$id</td>
                <td style='color: blue;'><i class='icon fas fa-user'></i>&nbsp;&nbsp;$user</td>
                <td style='color: blue;'>Admin</td>";
            }
            
            echo "  <td>$tel</td>
                    <td>$email</td>";

            if($tipo == "emp"){
                if($estado == "A"){

                    if($transacciones > 0){
                        echo "<td style='color: green;'>Activo</td>
                            <td onclick='VentanaInactivar(\"$id\")' style='text-decoration: none;color: brown; text-decoration: none; font-weight: bold; cursor: pointer;'>
                            Inactivar
                            </td>
                        </tr>";
                    }else{
                        echo "<td style='color: green;'>Activo</td>
                            <td onclick='VentanaBorrar(\"$id\")' style='text-decoration: none;color: red; text-decoration: none; font-weight: bold; cursor: pointer;'>
                            Eliminar
                            </td>
                        </tr>";
                    }

                    
                }

                if($estado == "I"){
                    echo "<td style='color: gray;'>Inactivo</td>
                            <td onclick='abrirVentanaVolverActivar(\"$id\")' style='text-decoration: none;color: blue; text-decoration: none; font-weight: bold; cursor: pointer;'>
                            Reactivar
                            </td>
                        </tr>";
                }
            }else{
                echo "<td style='color: green;'>Activo</td>
                    <td>
                        
                    </td>
                </tr>";
            }
        }
        echo '</tbody>
        </table>';
    }

    if( isset($_POST['comprobar-usuario']) && isset($_POST['user']) ){
        $user = $_POST['user'];

        $result = $conn->query
        ("SELECT * FROM usuarios WHERE user_u = '$user';");

        if ($result->rowCount() > 0) {
            echo "yes";  // usuario ya existe
        }else{
            echo "no";  // usuario no existe aún
        }

        
    }

    if( isset($_POST['subir-usuario'])){
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $tipo = "emp";
        $estado = "A";

        $result = $conn->query
        ("INSERT INTO usuarios(user_u, pass_u, tipo_u, tel_u, email_u, estado_u) 
        VALUES('$user','$pass','$tipo',$tel,'$email','$estado');");

        if($result){
            echo "yes";
        }else{
            echo "no";
        }

    }

    if( isset($_POST['borrarUsuario'])){
        $id = $_POST['id'];

        $result = $conn->query
        ("DELETE FROM usuarios WHERE id_u = $id;");

        if($result){
            echo "yes";
        }else{
            echo "no";
        }
    }

    if( isset($_POST['inactivarUsuario'])){
        $id = $_POST['id'];

        $result = $conn->query
        ("UPDATE usuarios SET estado_u = 'I' WHERE id_u = $id");

        if($result){
            echo "yes";
        }else{
            echo "no";
        }
    }

    if( isset($_POST['reactivarUsuario'])){
        $id = $_POST['id'];

        $result = $conn->query
        ("UPDATE usuarios SET estado_u = 'A' WHERE id_u = $id");

        if($result){
            echo "yes";
        }else{
            echo "no";
        }
    }
    
?>