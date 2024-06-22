<?php
    session_start();
    require 'conexion.php';
    require "./code128.php";

    if( isset($_POST['imprimir-productos']) ){

        $result = $conn->query
        ("SELECT * FROM productos WHERE id_p > 1 AND preventa_p = 'S';");

        if ($result->rowCount() > 0) {
            echo "<label for='direccion'>Seleccionar Producto:</label>
            <select id='producto' name='producto' style='width: 80%;height: 30px;' required>
            <option value='' disabled selected>Selecciona un producto</option>";

            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $id = $row['id_p'];
                $nom = $row['nom_p'];
                $precio = $row['preciou_p'];
                $cant = $row['cant_p'];

                echo "<option value='$id,$precio,$nom,$cant'>-> $nom | $$precio</option>";
            }
        }else{
            echo "<label for='direccion'>Seleccionar Producto:</label>
            <select id='producto' name='producto' style='width: 80%;height: 30px;' required>
            <option value='' disabled selected>Por el momento no hay productos por pedido</option>";

        }
        
        
   
        echo "</select>";
    }

    if( isset($_POST['imprimir-fecha-hora']) ){
        date_default_timezone_set('America/Mexico_City');
        $hora = date("H:i"); 
        $fecha = date("d / m / Y");

        echo "<label for='direccion'>Fecha: <span style='color: blue; font-style: italic; font-weight: bold;'>$fecha</span> </label>  
            <label for='direccion'>Hora: <span style='color: blue; font-style: italic; font-weight: bold;'>$hora</span></label>";
    }
    
    if( isset($_POST['subir-datos-abd']) ){

        $id_prod = $_POST['id_prod'];
        $cant = $_POST['cant'];
        $preciou = $_POST['preciou'];
        $abono = $_POST['abono'];
        $tipopago = $_POST['tipopago'];
        $fecha = $_POST['fecha'];
        $nombre = $_POST['nombre'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $hra = $_POST['hra'];

        $emp = $_SESSION['user'];

        $query = "CALL InsertarPreventa($id_prod, $cant, $preciou, $abono, '$tipopago', '$fecha', $emp, 'L', '$nombre', $tel, '$email', '$hra');";
        $result = $conn->query($query);
        
        // GENERAR TICKET -------------------------------------------------------------

        $hora_solo_horas_minutos = substr($hra, 0, 5);
        $fecha_actual = date("Y-m-d");
        $tipopago = ($tipopago == 'C') ? 'Efectivo' : (($tipopago == 'T') ? 'Transferencia' : 'Otro');

        $query2 = "SELECT MAX(id_c) as id_c from caja;";
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $numcaja = $row['id_c'];
        }
        
        $query3 = "SELECT user_u from usuarios WHERE id_u = $emp;";
        $result3 = $conn->query($query3);

        while($row = $result3->fetch(PDO::FETCH_ASSOC)){
            $nomemp = $row['user_u'];
        }

        $query4 = "SELECT MAX(id_pr) as id_pr from preventas;";
        $result4 = $conn->query($query4);

        while($row = $result4->fetch(PDO::FETCH_ASSOC)){
            $numpr = $row['id_pr'];
        }

        $query5 = "SELECT nom_p from productos WHERE id_p = $id_prod;";
        $result5 = $conn->query($query5);

        while($row = $result5->fetch(PDO::FETCH_ASSOC)){
            $nomproducto = $row['nom_p'];
        }

        $pdf = new PDF_Code128('P','mm',array(80,230));  // segundo valor es tamaño vertical
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
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Tipo transación: PEDIDO"),0,'C',false);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: $fecha_actual $hora_solo_horas_minutos"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja Nro: #$numcaja"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Atendió: $nomemp"),0,'C',false);
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","No. Pedido: #$numpr"),0,'C',false);
        $pdf->SetFont('Arial','',9);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cliente: $nombre"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: $tel"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: $email"),0,'C',false);

        $pdf->Ln(2);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
        $pdf->Ln(3);

        # Tabla de productos #
        $pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","Cant."),0,0,'C');
        $pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","  "),0,0,'C');
        $pdf->Cell(19,5,iconv("UTF-8", "ISO-8859-1","Precio Unitario"),0,0,'C');
        
        $pdf->Cell(30,5,iconv("UTF-8", "ISO-8859-1","Total abonado"),0,0,'C');

        $pdf->Ln(3);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
        $pdf->Ln(4);



        /*----------  Detalles de la tabla  ----------*/
        $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","$nomproducto"),0,'C',false);
        $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","$cant"),0,0,'C');
        $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","$$preciou MXN"),0,0,'C');
        $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","$$abono MXN"),0,0,'C');
        /*
        $pdf->Ln(4);
        $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","Nombre de producto a vender2"),0,'C',false);
        $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1","7"),0,0,'C');
        $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","$500 MXN"),0,0,'C');
        
        $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","$3500 MXN"),0,0,'C');*/
        $pdf->Ln(5);
        /*----------  Fin Detalles de la tabla  ----------*/

        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(6);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Tipo de Pago"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$tipopago"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total abonado"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$abono MXN"),0,0,'C');
        
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
        $pdf->Ln(10);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');

        $pdf->Ln(9);

        # Codigo de barras #
        $pdf->Code128(5,$pdf->GetY(),"COD000001P00$numpr",70,20);
        $pdf->SetXY(0,$pdf->GetY()+21);
        $pdf->SetFont('Arial','',14);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","COD000001P00$numpr"),0,'C',false);
        
        # Nombre del archivo PDF #
         $pdf->Output("F","../comprobantes/preventas/preventa_num$numpr.pdf",true);
        // $pdf->Output("I","Ticket_Nro_1.pdf",true);

        // ----------------------------------------------------------------------------
        
        if($result){
            echo "yes";
        }else{
            echo $query;
        }

    }

    if( isset($_POST['generar-ticket-preventa']) ){
        $query2 = "SELECT img_pr FROM preventas where id_pr = (SELECT MAX(id_pr) FROM preventas LIMIT 1);";
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $ticket = $row['img_pr'];
        }

        echo "<a href='$ticket' style='font-size: 18px;' target='_blank'>Imprimir Comprobante</a>";
    }
?>