<?php
    session_start();
    require 'conexion.php';

    if(isset($_POST['imprimir-preventas'])){
        $mes = $_POST['mes'];

        $query = "SELECT MONTH(fecha_pr) as mes, DAY(fecha_pr) as dia, TIME_FORMAT(hra_pr, '%H:%i') AS hora_minutos, id_pr as id, caja_pr as caja, user_u as empleado,
        CASE 
            WHEN tipopago_pr = 'C' THEN 'Efectivo'
            WHEN tipopago_pr = 'T' THEN 'Transferencia'
            ELSE tipopago_pr
          END AS tipopago, abono_pr as total, img_pr as img
        FROM preventas, usuarios
        WHERE emp_pr = id_u AND id_pr != 1 AND MONTH(fecha_pr) = $mes
        ORDER BY DAY(fecha_pr) DESC, hra_pr DESC;";

        $result = $conn->query($query);

        echo "<table id='tabla-preventas' class='stripe cell-border' style='background-color: white; border: 1px solid black;'>
        <thead>
        <tr>
            <th style='text-align: center;'>Dia</th>
            <th style='text-align: center;'>Hora</th>
            <th style='text-align: center;'>#</th>
            <th style='text-align: center;'>Caja</th>
            <th style='text-align: center;'>Atendio</th>
            <th style='text-align: center;'>Tipo de Pago</th>
            <th style='text-align: center;'>Total</th>
            <th style='text-align: center;'>  </th>
    
        </tr>
        
        </thead>
    
        <tbody>";

        if ($result->rowCount() > 0) {

            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $dia = $row['dia'];
                $hora = $row['hora_minutos'];
                $id = $row['id'];
                $caja = $row['caja'];
                $empleado = $row['empleado'];
                $tipopago = $row['tipopago'];
                $total = $row['total'];
                $img = $row['img'];

                echo "<tr>
                <td style='text-align: center;'>$dia</td>
                <td style='text-align: center;'>$hora</td>
                <td style='text-align: center;'>$id</td>
                <td style='text-align: center;'>$caja</td>
                <td style='text-align: center;'>$empleado</td>
                <td style='text-align: center;'>$tipopago</td>
                <td style='text-align: center;'>$total</td>";

                if ($img !== null) {
                    echo "<td style='text-align: center;'>
                    <button type='button' class='boton-detalles' onclick='imprimirComprobantePreventa(\"$img\")'>
                    Descargar Comprobante
                    </button>
                </td>
                </tr>";
                }else{

                    echo "<td style='text-align: center;'>No hay comprobante
                        </td>
    
                        </tr>";
                }
                
            }

        }else{

        }

        echo "
        </tbody>
        </table>";
    }

    if(isset($_POST['imprimir-ventas'])){

        $mes = $_POST['mes'];

        $query = "SELECT MONTH(fecha_v) as mes, DAY(fecha_v) as dia, TIME_FORMAT(hra_v, '%H:%i') AS hora_minutos, id_v as id, caja_v as caja, user_u as empleado,
        CASE 
            WHEN tipopago_v = 'C' THEN 'Efectivo'
            WHEN tipopago_v = 'T' THEN 'Transferencia'
            ELSE tipopago_v
          END AS tipopago, total_v as total, img_v as img
        FROM ventas, usuarios
        WHERE emp_v = id_u AND MONTH(fecha_v) = $mes
        ORDER BY DAY(fecha_v) DESC, hra_v DESC;";

        $result = $conn->query($query);

        echo "<table id='tabla-ventas' class='stripe cell-border' style='background-color: white; border: 1px solid black;'>
            <thead>
            <tr>
                <th style='text-align: center;'>Dia</th>
                <th style='text-align: center;'>Hora</th>
                <th style='text-align: center;'>#</th>
                <th style='text-align: center;'>Caja</th>
                <th style='text-align: center;'>Atendio</th>
                <th style='text-align: center;'>Tipo de Pago</th>
                <th style='text-align: center;'>Total</th>
                <th style='text-align: center;'>  </th>
        
            </tr>
        
        
        </thead>
        
        <tbody>";


        if ($result->rowCount() > 0) {

            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $dia = $row['dia'];
                $hora = $row['hora_minutos'];
                $id = $row['id'];
                $caja = $row['caja'];
                $empleado = $row['empleado'];
                $tipopago = $row['tipopago'];
                $total = $row['total'];
                $img = $row['img'];

                echo "    <tr>
                <td style='text-align: center;'>$dia</td>
                <td style='text-align: center;'>$hora</td>
                <td style='text-align: center;'>$id</td>
                <td style='text-align: center;'>$caja</td>
                <td style='text-align: center;'>$empleado</td>
                <td style='text-align: center;'>$tipopago</td>
                <td style='text-align: center;'>$total</td>";

                if ($img !== null) {
                    echo "    <td style='text-align: center;'>
                    <button type='button' class='boton-detalles' onclick='imprimirComprobanteVenta(\"$img\")'>
                    Descargar Comprobante
                    </button>
                </td>
                </tr>";
                }else{
                    echo "<td style='text-align: center;'>No hay comprobante
                        </td>
    
                        </tr>";
                }
                
            }
        }
        
            
        
        
        
        echo "
        </tbody>
        </table>";
    }

    if(isset($_POST['imprimir-cajas'])){

        $mes = $_POST['mes'];
        $query = "SELECT MONTH(fecha_c) as mes, DAY(fecha_c) as dia, TIME_FORMAT(hrabrir_c, '%H:%i') AS hora_apertura, TIME_FORMAT(hrcierre_c, '%H:%i') AS hora_cierre, id_c as id, user_u as empleado,
        (totale_c + totalt_c) as total ,img_c as img
        FROM caja, usuarios
        WHERE emp_c = id_u AND MONTH(fecha_c) = $mes AND id_c != 1 AND hrcierre_c IS NOT NULL
        ORDER BY DAY(fecha_c) DESC, hrabrir_c DESC;";

        $result = $conn->query($query);

        echo "<table id='tabla-cajas' class='stripe cell-border' style='background-color: white; border: 1px solid black;'>
        <thead>
                <tr>
                    
                    <th style='text-align: center;'>Dia</th>
                    <th style='text-align: center;'>Hora apertura</th>
                    <th style='text-align: center;'>Hora cierre</th>
                    <th style='text-align: center;'>#</th>
                    <th style='text-align: center;'>Aperturó</th>
                    <th style='text-align: center;'>Total cierre</th>
                    <th style='text-align: center;'> </th>
    
                </tr>
            
    
        </thead>
    
            <tbody>";

            if ($result->rowCount() > 0) {

                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    $dia = $row['dia'];
                    $hora_apertura = $row['hora_apertura'];
                    $hora_cierre = $row['hora_cierre'];
                    $id = $row['id'];
                    $empleado = $row['empleado'];
                    $total = $row['total'];
                    $img = $row['img'];

                    echo "<tr>
                    
                    <td style='text-align: center;'>$dia</td>
                    <td style='text-align: center;'>$hora_apertura</td>
                    <td style='text-align: center;'>$hora_cierre</td>
                    <td style='text-align: center;'>$id</td>
                    <td style='text-align: center;'>$empleado</td>
                    <td style='text-align: center;'>$total</td>";

                    if ($img !== null) {

                        echo "<td style='text-align: center;'>
                        <button type='button' class='boton-detalles' onclick='imprimirComprobanteCaja(\"$img\")'>
                        Descargar Comprobante
                        </button>
                        </td>
    
                        </tr>";
                    }else{
                        echo "<td style='text-align: center;'>No hay comprobante
                        </td>
    
                        </tr>";
                    }

                }
            }
    
    
            echo "
            </tbody>
      </table>";
    }
?>