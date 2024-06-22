<?php
    session_start();
    require 'conexion.php';

    if(isset($_POST['reproducir-transacciones'])){

        echo "<table id='tabla-transacciones' class='stripe cell-border' style='width:100%; background-color: white; border: 1px solid black;'>
        <thead>
                <tr>
                    <th style='text-align: center;'>No. Transaccion</th>
                    <th style='text-align: center;'>Tipo de Transaccion</th>
                    <th style='text-align: center;'>Usuario</th>
                    <th style='text-align: center;'>Hora</th>
                    <th style='text-align: center;'>Total</th>
                    <th style='text-align: center;'>Tipo de Pago</th>
                    <th style='text-align: center;'>Ver Detalles</th>
    
                </tr>
            
    
            </thead>
            <tbody>";

        $result = $conn->query
        ("SELECT * FROM transaccionesDelDia;");

        if ($result->rowCount() > 0) {
            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $id = $row['idtrans'];
                $tipo = $row['tipo'];
                $usuario = $row['usuario'];
                $hora = $row['hora'];
                $total = $row['Total'];
                $tipopago = $row['tipopago'];

                $nomtipo = $tipo;

                if ($nomtipo == "Preventa") {
                    $nomtipo = "Pedido";
                }
                
                // Descomponer la cadena en horas, minutos y segundos
                list($horas, $minutos, $segundos) = explode(":", $hora);

                // Reemplazar "00" con "24" si es la media noche
                if ($horas == "00") {
                    $horas = "24";
                }

                $hora_formato_24 = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
                $hora_formato_24_sinsegundos = sprintf("%02d:%02d", $horas, $minutos);

                echo "<tr>
                        <td>#$id</td>
                        <td>$nomtipo</td>";

                if($usuario == "admin"){
                    echo "<td style='color: orange; font-weight: bold;'><i class='icon fas fa-user'></i>&nbsp;&nbsp;Admin</td>";
                }else{
                    echo "<td style='color: blue;'><i class='icon fas fa-user'></i>&nbsp;&nbsp;$usuario</td>";
                }
                

                echo "<td>$hora_formato_24_sinsegundos</td>
                        <td>$$total</td>
                        <td>$tipopago</td>
                        <td>
                            <button type='button' class='boton-detalles' onclick='abrirDetalles(\"$id\",\"$tipo\")'>
                            Detalles
                            </button>
                        </td>
                    
                        
                    </tr>";

            }
        }

        echo "</tbody>
        </table>";
    }


    if(isset($_POST['reproducir-totales'])){
        $result = $conn->query
        ("SELECT * FROM caja WHERE hrcierre_c IS NULL LIMIT 1;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $totale = $row['totale_c'];
            $totalt = $row['totalt_c'];
            $total = $totale + $totalt;

            echo "<div class='cuadro'><h2>Total Efectivo: <span style='color: green;'>$$totale</span></h2></div>+
                <div class='cuadro'><h2>Total Transferencia: <span style='color: green;'>$$totalt</span></h2></div>=
                <div class='cuadroTotal'><h2 style='font-weight: bold;'>Total: <span style='color: green; font-weight: bold;'>$$total</span></h2></div>";
        }
    }

    if(isset($_POST['verdatos-preventa'])){
        

        $id = $_POST['id'];

        $result = $conn->query
        ("SELECT prod_pr, cant_pr, nom_p FROM preventas,productos WHERE prod_pr = id_p AND id_pr = $id;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){

            $nom = $row['nom_p'];
            $cant = $row['cant_pr'];

            echo "<div>- $cant $nom </div>";
        }
    }

    if(isset($_POST['verdatos-venta'])){

        $id = $_POST['id'];

        $result = $conn->query
        ("SELECT nom_lv, cant_lv FROM lista_venta WHERE venta_lv = $id;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){

            $nom = $row['nom_lv'];
            $cant = $row['cant_lv'];

            echo "<div>- $cant $nom </div>";
        }
    }
?>