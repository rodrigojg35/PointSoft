<?php
    session_start();
    require 'conexion.php';
    require "./code128.php";

    if(isset($_POST['header-cierre'])){

        $result = $conn->query
        ("SELECT id_c, fecha_c, hrabrir_c, user_u FROM caja, usuarios WHERE emp_c = id_u AND hrcierre_c IS NULL LIMIT 1;");
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $id = $row['id_c'];
            $fecha = $row['fecha_c'];
            $hra = $row['hrabrir_c'];
            $usuario = $row['user_u'];
        }

        $fecha_formateada = date("d / m / Y", strtotime($fecha));
        $hora_formateada = date("H:i", strtotime($hra));

        echo "<p style='line-height: 20px;'>Tipo: <span style='color: red;'>Cierre</span><br>
        No. Caja: <span style='color: blue;'>#$id</span><br>
        Fecha y Hora: <span style='color: blue;'>$fecha_formateada ($hora_formateada)</span><br>
        Apertura por: <span style='color: blue;'>$usuario</span></p>
        <div class='linea'><hr class='custom-line'></div><br>";
    }

    
    if(isset($_POST['imprimir-transacciones'])){
        

        $result = $conn->query
        ("SELECT * FROM transaccionesDelDia;");

        if ($result->rowCount() > 0) {


            echo "<table>
                        <tr>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Usuario</th>
                        <th>Tipo pago</th>
                        <th>Total</th>
                        </tr>";

            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $id = $row['idtrans'];
                $tipo = $row['tipo'];
                $usuario = $row['usuario'];
                $hora = $row['hora'];
                $total = $row['Total'];
                $tipopago = $row['tipopago'];

                if($tipo == "Preventa"){
                    $tipo = "Pedido";
                }

                $hora_formateada = date("H:i", strtotime($hora));

                echo "<tr>
                        <td>$hora_formateada</td>
                        <td>$tipo</td>
                        <td>$usuario</td>
                        <td>$tipopago</td>
                        <td>$total</td>        
                    </tr>";

            }

            echo "</table>";

            
        }else{
            echo "No hubo transacciones realizadas";
        }
    }

    if(isset($_POST['canttransacciones-cierre'])){
        $result = $conn->query
        ("SELECT cantventas_c, totale_c, totalt_c from caja WHERE hrcierre_c IS NULL LIMIT 1;");
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $cant = $row['cantventas_c'];

        }

        echo "<p style='margin-top: 15px;'>Total de transacciones: $cant</p>";
    }
    

    if(isset($_POST['totales-cierre'])){

        $result = $conn->query
        ("SELECT cantventas_c, totale_c, totalt_c from caja WHERE hrcierre_c IS NULL LIMIT 1;");
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $totale = $row['totale_c'];
            $totalt = $row['totalt_c'];

        }

        echo "<p >Total en efectivo: <span style='color: green;'>$$totale</span></p>
        <p style='margin-top: -14px;'>Total en transferencia: <span style='color: green;'>$$totalt</span></p>";
        
    }

    if(isset($_POST['total-cierre'])){
        $result = $conn->query
        ("SELECT cantventas_c, totale_c, totalt_c from caja WHERE hrcierre_c IS NULL LIMIT 1;");
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $totale = $row['totale_c'];
            $totalt = $row['totalt_c'];

        }

        $totalfinal = $totale + $totalt;

        echo "<p style='font-size: 18px; font-weight: bold;'>Total de Cierre: <span style='color: green;'>$$totalfinal</span></p>";
    }

    if(isset($_POST['cerrar-caja'])){

        date_default_timezone_set('America/Mexico_City');
        $hora_actual = date("H:i");
        
        
        $result = $conn->query
        ("CALL cerrarCaja('$hora_actual');");

        if($result){
            echo "yes";
        } 
    }

    if(isset($_POST['generar-comprobante'])){

        // GENERAR TICKET -------------------------------------------------------------

        $fecha = date('Y-m-d'); // Formato: Año-Mes-Día
        $hora = date('H:i'); // Formato: Hora:Minutos
        $emp = $_SESSION['user'];

        $query3 = "SELECT user_u from usuarios WHERE id_u = $emp;";      // Obtener empleado
        $result3 = $conn->query($query3);

        while($row = $result3->fetch(PDO::FETCH_ASSOC)){
            $nomemp = $row['user_u'];
        }

        $query2 = "SELECT * from caja WHERE id_c = (SELECT MAX(id_c) from caja LIMIT 1);";  // obtener la caja
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $numcaja = $row['id_c'];
            $hrabrir = $row['hrabrir_c'];
            $hrcierre = $row['hrcierre_c'];
            $emp2 = $row['emp_c'];
            $canttransacciones = $row['cantventas_c'];
            $totale = $row['totale_c'];
            $totalt = $row['totalt_c'];
        }

        $query4 = "SELECT user_u from usuarios WHERE id_u = $emp2;";
        $result4 = $conn->query($query4);

        while($row = $result4->fetch(PDO::FETCH_ASSOC)){
            $nomemp2 = $row['user_u'];
        }

        $dt_abrir = new DateTime($hrabrir);
        $dt_cierre = new DateTime($hrcierre);

        // Obtener solo horas y minutos en formato de 12 horas
        $hrabrir_12h = $dt_abrir->format('h:i A'); // 'h' representa horas en formato de 12 horas
        $hrcierre_12h = $dt_cierre->format('h:i A');

        // Validar y convertir 00 a 12
        if ($dt_abrir->format('H') == '00') {
            $hrabrir_12h = '12' . $dt_abrir->format(':i A');
        }

        if ($dt_cierre->format('H') == '00') {
            $hrcierre_12h = '12' . $dt_cierre->format(':i A');
        }

        $tamañoticket = 220 + ($canttransacciones * 8);  // 230
        $pdf = new PDF_Code128('P','mm',array(80,$tamañoticket));  // segundo valor es tamaño vertical
        $pdf->SetMargins(4,10,4);
        $pdf->AddPage();
        
        # Encabezado y datos de la empresa #
        $pdf->SetFont('Arial','B',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->Image('logo-ticket2.png', 25, 8, -300);
        $pdf->Ln(30);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Ultra Toys TCG Y Collectionables"),0,'C',false);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Av. Progreso 579 Jardines de Mocambo"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","CP: 94299"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Tipo transación: CIERRE"),0,'C',false);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: $fecha"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Hora apertura: $hrabrir_12h"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Hora cierre: $hrcierre_12h"),0,'C',false);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Usuario apertura: $nomemp"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Usuario cierre: $nomemp2"),0,'C',false);

        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","No. Caja: #$numcaja"),0,'C',false);
        $pdf->SetFont('Arial','',9);
    

        $pdf->Ln(1);

        if($canttransacciones > 0){

            $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $pdf->Ln(3);

            # Tabla de productos #
            $pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","Hora"),0,0,'C');
            $pdf->Cell(12,5,iconv("UTF-8", "ISO-8859-1","Tipo"),0,0,'C');
            $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","Usuario"),0,0,'C');
            $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1","Pago"),0,0,'C');
            $pdf->Cell(17,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');

            $pdf->Ln(3);
            $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $pdf->Ln(6);

            /*----------  Detalles de la tabla  ----------*/

            $result = $conn->query
            ("SELECT * FROM transaccionesdeUltCaja;");

            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $id = $row['idtrans'];
                $tipo = $row['tipo'];
                $usuario = $row['usuario'];
                $hora = $row['hora'];
                $total = $row['Total'];
                $tipopago = $row['tipopago'];

                $hora_formateada = date("H:i", strtotime($hora));

                if($tipo == "Preventa"){
                    $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","[$hora_formateada]"),0,0,'C');
                    $pdf->Cell(15,1,iconv("UTF-8", "ISO-8859-1","Pedido"),0,0,'C');
                    $pdf->Cell(12,1,iconv("UTF-8", "ISO-8859-1","$usuario"),0,0,'C');
                    $pdf->Cell(22,1,iconv("UTF-8", "ISO-8859-1","$tipopago"),0,0,'C');
                    $pdf->Cell(15,1,iconv("UTF-8", "ISO-8859-1","$$total"),0,0,'C');

                }

                if($tipo == "Venta"){
                    $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","[$hora_formateada]"),0,0,'C');
                    $pdf->Cell(12,1,iconv("UTF-8", "ISO-8859-1","$tipo"),0,0,'C');
                    $pdf->Cell(18,1,iconv("UTF-8", "ISO-8859-1","$usuario"),0,0,'C');
                    $pdf->Cell(17,1,iconv("UTF-8", "ISO-8859-1","$tipopago"),0,0,'C');
                    $pdf->Cell(19,1,iconv("UTF-8", "ISO-8859-1","$$total"),0,0,'C');
                }

                $pdf->Ln(4);
            }

            $pdf->Ln(1);
            $pdf->Cell(41,4,iconv("UTF-8", "ISO-8859-1","Cantidad de transacciones: $canttransacciones"),0,0,'C');
            $pdf->Ln(4);
            
            /*----------  Fin Detalles de la tabla  ----------*/

        }else{
            $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',10);
            $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","No hay transacciones"),0,'C',false);
            $pdf->Ln(1);

        }

        
        
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(5);
        

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total en Efectivo:"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$totale MXN"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total en Transferencia:"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$totalt MXN"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(6);
        $totalx = $totale + $totalt;
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total de cierre:"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$totalx MXN"),0,0,'C');

        
        /*
        $pdf->Ln(5);
        
        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","TOTAL PAGADO"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$100.00 USD"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","CAMBIO"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$30.00 USD"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","USTED AHORRA"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$0.00 USD"),0,0,'C');
        */
        $pdf->Ln(14);

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por usar StoreSoft***"),'',0,'C');

        $pdf->Ln(9);

        # Codigo de barras #
        $pdf->Code128(5,$pdf->GetY(),"COD000001C00$numcaja",70,20);
        $pdf->SetXY(0,$pdf->GetY()+21);
        $pdf->SetFont('Arial','',14);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","COD000001C00$numcaja"),0,'C',false);
        
        # Nombre del archivo PDF #
         $pdf->Output("F","../comprobantes/caja/caja_num$numcaja.pdf",true);
        // $pdf->Output("I","Ticket_Nro_1.pdf",true);
        // ----------------------------------------------------------------------------




        
        $result = $conn->query
        ("SELECT img_c from caja WHERE id_c = (SELECT MAX(id_c) FROM caja LIMIT 1);");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $enlace = $row['img_c'];
        }


        if($result){
            echo "<a href='$enlace' style='font-size: 18px;' target='_blank'>Imprimir Comprobante</a>";
        } 
    }

    
?>