<?php
    session_start();
    require 'conexion.php';

    if(isset($_SESSION['user']) && isset($_POST['abrir-nueva-caja'])){

        $idu = $_SESSION['user'];
        date_default_timezone_set('America/Mexico_City');
        $hora = date("H:i:s"); 
        $fecha = date("Y-m-d");
        
        $result = $conn->query
        ("INSERT INTO caja(fecha_c, hrabrir_c, cantventas_c, totale_c, totalt_c, emp_c) 
        VALUES ('$fecha', '$hora', 0, 0, 0, $idu);");

        echo "vino a enviar datos";

    }

    if(isset($_POST['consultar-caja-abierta'])){

        $result = $conn->query
        ("SELECT * from caja where hrcierre_c IS NULL;");

        if ($result->rowCount() > 0){
            echo "yes";
        }else{
            echo "no";
        }

    }

    if(isset($_POST['consultar-ult-caja2'])){

        $result = $conn->query
        ("SELECT id_c, fecha_c, hrabrir_c from caja where hrcierre_c IS NULL;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $id = $row['id_c'];
            $fecha = $row['fecha_c'];
            $hora = $row['hrabrir_c'];
        }

        $fecha = date('d / m / Y', strtotime($fecha));
        $hora = substr($hora,0,5);

        echo "<div class='datos-caja' id='datos-caja'>
                <p class='datos-de-caja'>No. de Apertura Caja: <span style='color: lightblue;'>#$id</span></p>
                <p class='datos-de-caja'>Fecha de Apertura: <span style='color: lightblue;'>$fecha</span></p>
                <p class='datos-de-caja'>Hora de Apertura: <span style='color: lightblue;''>$hora</span></p>
            </div>";

    }

    

    if(isset($_POST['consultar-ult-caja'])){
        /*echo 'vino a consultar caja: ';  */

        $result = $conn->query
        ("SELECT * FROM caja ORDER BY id_c DESC LIMIT 1;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $fecha = $row['fecha_c'];
        }

        $fecha = date('d / m / Y', strtotime($fecha));

        echo "$fecha";

    }


?>