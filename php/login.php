<?php
    session_start();
    require 'conexion.php';

    if(isset($_POST['imprimir_tipo'])){

        if($_POST['imprimir_tipo'] == "emp"){
            $result = $conn->query
            ("SELECT id_u as id, user_u as usuario FROM usuarios WHERE tipo_u = 'emp' AND estado_u = 'A';");
            
            if ($result->rowCount() > 0) {
                echo '<select>
                <option value="" disabled selected>Selecciona un usuario</option>';
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    $user = $row['usuario'];
                    $id = $row['id'];
                    echo "<option value='$id'> -> $user</option>";
                }
                echo '</select>';
            }else{
                echo '<select>
                        <option value="" disabled selected>No existen usuarios aún de este tipo</option>
                    </select>';
            }
        }

        if($_POST['imprimir_tipo'] == "admin"){
            $result = $conn->query
            ("SELECT id_u as id, user_u as usuario FROM usuarios WHERE tipo_u = 'admin';");
            
            if ($result->rowCount() > 0) {
                echo '<select>
                <option value="" disabled selected>Selecciona un usuario</option>';
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    $user = $row['usuario'];
                    $id = $row['id'];
                    echo "<option value='$id'> -> $user</option>";
                }
                echo '</select>';
            }else{
                echo '<select>
                        <option value="" disabled selected>No existen usuarios aún de este tipo</option>
                    </select>';
            }
        }
        
    }


    if(isset($_POST['tipo'])){
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $tipo = $_POST['tipo'];

        $result = $conn->query
        ("SELECT * FROM usuarios WHERE id_u = '$user' && pass_u = '$pass' && tipo_u = '$tipo';");

        if ($result->rowCount() > 0){

            $_SESSION['user'] = $user;
            $_SESSION['pass'] = $pass;
            $_SESSION['tipo'] = $tipo;

            echo "yes";

        }else{
            session_unset();

            session_destroy();
            echo "no";
        }

        
    }

    

    


    /*
    echo '<select>
    <option value="" disabled selected>Selecciona un usuariobbbbbbbbbbb</option>';
    
    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        $user = $row['usuario'];
        echo "<option value="5">$user</option>";
    }
    echo '</select>';
    */

    
?>