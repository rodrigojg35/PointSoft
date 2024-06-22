<?php
    session_start();
    require 'conexion.php';
    require "./code128.php";

    if( isset($_POST['imprimir-preventas']) ){
        $result = $conn->query
        ("SELECT * FROM listado_preventas_select;");

        if ($result->rowCount() > 0) {

            echo "<select id='select-preventa' style='width: 90%; height: 30px;'>
                    <option value='' disabled selected>Seleccionar pedido</option>";

            while($row = $result->fetch(PDO::FETCH_ASSOC)){

                $id = $row['id_pr'];
                $idproducto = $row['prod_pr'];
                $nomprod = $row['nom_p'];
                $abono = $row['abono_pr'];
                $nomcliente = $row['nombre_pr'];
                $cantidad = $row['cant_pr'];
                $preciou = $row['preciou_pr'];

                echo "<option value='$id,$nomprod,$nomcliente,$abono,$cantidad,$preciou,$idproducto'>-> #$id | $nomprod | </option>";
                
            }

            echo "</select><br><br>";

        }else{
            echo "<select id='select-preventa' style='width: 110%; height: 30px;'>
                    <option value='' disabled selected>No hay pedidos disponibles</option>
                </select><br><br>";
        }
    }
    
    if( isset($_POST['imprimir-productos']) ){

        $result = $conn->query
        ("SELECT * from productos WHERE id_p > 1 AND estado_p = 'D';");

        if ($result->rowCount() > 0) {
            echo "<label for='direccion'>Seleccionar Producto:</label>
            <select id='producto' name='producto' style='width: 80%;height: 30px;' required>
            <option value='' disabled selected>Selecciona un producto</option>";

            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                $id = $row['id_p'];
                $nom = $row['nom_p'];
                $precio = $row['preciou_p'];
                $cant = $row['cant_p'];

                echo "<option value='$id,$precio,$nom,$cant'>-> $nom</option>";
            }
        }else{
            echo "<label for='direccion'>Seleccionar Producto:</label>
            <select id='producto' name='producto' style='width: 80%;height: 30px;' required>
            <option value='' disabled selected>Por el momento no hay productos disponibles</option>";

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

        $total = $_POST['total'];
        $tipopago = $_POST['tipo_pago'];
        $fecha = $_POST['fecha'];
        $nomcliente = $_POST['nomcliente'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $hra = $_POST['hra'];
        $prev = $_POST['idpreventa'];

        $emp = $_SESSION['user'];

        $query = "CALL InsertarVenta($total, '$tipopago', '$fecha', $emp, $prev,'$nomcliente', $tel, '$email', '$hra');";

        $result = $conn->query($query);

        if($result){
            echo "yes";
        }else{
            echo $query;
        }
    }

    if( isset($_POST['subir-lista-productos1']) ){

        $cantproductos = $_POST['cant-productos'];
        // Recibir la cadena JSON y decodificarla a un array
        $dataReceived = json_decode($_POST['datos'], true);

        // Acceder al array
        $listaDeProductos = $dataReceived['listaDeProductos'];

        // GENERAR TICKET -------------------------------------------------------------

        $query2 = "SELECT * FROM ventas WHERE id_v = (SELECT id_v FROM ventas ORDER BY id_v DESC LIMIT 1);";
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $id_v = $row['id_v'];
            $caja_v = $row['caja_v'];
            $emp_v = $row['emp_v'];
            $fecha_v = $row['fecha_v'];
            $hra_v = $row['hra_v'];
            $hora_v = substr($hra_v, 0, 5);

            $nombre = $row['nombre_v'];
            $tel = $row['tel_v'];
            $email = $row['email_v'];

            $tipopago = $row['tipopago_v'];
            $tipopago = ($tipopago == 'C') ? 'Efectivo' : (($tipopago == 'T') ? 'Transferencia' : 'Otro');

            $total_v = $row['total_v'];
        }

        $query3 = "SELECT user_u FROM usuarios WHERE id_u = $emp_v;";
        $result3 = $conn->query($query3);

        while($row = $result3->fetch(PDO::FETCH_ASSOC)){
            $nomemp = $row['user_u'];
        }

        $tamañoticket = 220 + ($cantproductos * 8);  // 230
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
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Tipo transación: VENTA"),0,'C',false);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: $fecha_v $hora_v"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja Nro: #$caja_v"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Atendió: $nomemp"),0,'C',false);
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","No. Venta: #$id_v"),0,'C',false);
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
        
        $pdf->Cell(25,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');

        $pdf->Ln(3);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
        $pdf->Ln(4);



        /*----------  Detalles de la tabla  ----------*/
        


        // Iterar sobre cada objeto en el array
        foreach ($listaDeProductos as $producto) {
            // Acceder a cada propiedad del objeto
            $id = $producto['id'];
            $nombre = $producto['nombre'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['preciou'];

            $query = "INSERT INTO lista_venta(prod_lv, nom_lv, cant_lv, preciou_lv)
            VALUES($id,'$nombre', $cantidad, $precio);";

            $result = $conn->query($query);

            $totalart = $cantidad * $precio;
            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","$nombre"),0,'C',false);
            $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","$cantidad"),0,0,'C');
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","$$precio MXN"),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","$$totalart MXN"),0,0,'C');

            $pdf->Ln(4);
            
        }
        
        /*----------  Fin Detalles de la tabla  ----------*/
        $pdf->Ln(2);
        $pdf->Cell(35,4,iconv("UTF-8", "ISO-8859-1","Cantidad de artículos: $cantproductos"),0,0,'C');
        $pdf->Ln(4);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(6);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Tipo de Pago"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$tipopago"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$total_v MXN"),0,0,'C');
        
        $pdf->Ln(10);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');

        $pdf->Ln(9);

        # Codigo de barras #
        $pdf->Code128(5,$pdf->GetY(),"COD000001V00$id_v",70,20);
        $pdf->SetXY(0,$pdf->GetY()+21);
        $pdf->SetFont('Arial','',14);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","COD000001V00$id_v"),0,'C',false);
        
        # Nombre del archivo PDF #
            $pdf->Output("F","../comprobantes/ventas/venta_num$id_v.pdf",true);
        // $pdf->Output("I","Ticket_Nro_1.pdf",true);

        // ----------------------------------------------------------------------------

        if($result){
            echo "yes";
        }else{
            echo $result;
        }
    }

    if( isset($_POST['subir-lista-productos2']) ){

        $idprev = $_POST['idprev'];
        $nomprev = $_POST['nomprev'];
        $cantprev = $_POST['cantprev'];
        $precioprev = $_POST['precioprev'];
        $abonoprev = $_POST['abonoprev'];

        $cantproductos = $_POST['cant-productos'];

        $queryprev = "INSERT INTO lista_venta(prod_lv, nom_lv, cant_lv, preciou_lv)
            VALUES($idprev,'$nomprev', $cantprev, $precioprev);";

        $resultprev = $conn->query($queryprev);

        // Recibir la cadena JSON y decodificarla a un array
        $dataReceived = json_decode($_POST['datos'], true);

        // Acceder al array
        $listaDeProductos = $dataReceived['listaDeProductos'];

        // GENERAR TICKET -------------------------------------------------------------

        $query2 = "SELECT * FROM ventas WHERE id_v = (SELECT id_v FROM ventas ORDER BY id_v DESC LIMIT 1);";
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $id_v = $row['id_v'];
            $caja_v = $row['caja_v'];
            $emp_v = $row['emp_v'];
            $fecha_v = $row['fecha_v'];
            $hra_v = $row['hra_v'];
            $hora_v = substr($hra_v, 0, 5);

            $nombre = $row['nombre_v'];
            $tel = $row['tel_v'];
            $email = $row['email_v'];

            $tipopago = $row['tipopago_v'];
            $tipopago = ($tipopago == 'C') ? 'Efectivo' : (($tipopago == 'T') ? 'Transferencia' : 'Otro');

            $total_v = $row['total_v'];
        }

        $query3 = "SELECT user_u FROM usuarios WHERE id_u = $emp_v;";
        $result3 = $conn->query($query3);

        while($row = $result3->fetch(PDO::FETCH_ASSOC)){
            $nomemp = $row['user_u'];
        }

        $tamañoticket = 240 + ($cantproductos * 8);  // 230
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
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Tipo transación: VENTA"),0,'C',false);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: $fecha_v $hora_v"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja Nro: #$caja_v"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Atendió: $nomemp"),0,'C',false);
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","No. Venta: #$id_v"),0,'C',false);
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
        
        $pdf->Cell(25,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');

        $pdf->Ln(3);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
        $pdf->Ln(4);

        $totalprev = $cantprev * $precioprev;


            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","$nomprev"),0,'C',false);
            $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","$cantprev"),0,0,'C');
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","$$precioprev MXN"),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","$$totalprev MXN"),0,0,'C');
            
            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","  "),0,'C',false);
            $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","  "),0,0,'C');
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1","  "),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","Abono"),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","-$$abonoprev MXN"),0,0,'C');
            $pdf->Ln(5);


        /*----------  Detalles de la tabla  ----------*/

        // Iterar sobre cada objeto en el array
        foreach ($listaDeProductos as $producto) {
            // Acceder a cada propiedad del objeto
            $id = $producto['id'];
            $nombre = $producto['nombre'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['preciou'];

            $query = "INSERT INTO lista_venta(prod_lv, nom_lv, cant_lv, preciou_lv)
            VALUES($id,'$nombre', $cantidad, $precio);";

            $result = $conn->query($query);

            $totalart = $cantidad * $precio;
            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","$nombre"),0,'C',false);
            $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","$cantidad"),0,0,'C');
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","$$precio MXN"),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","$$totalart MXN"),0,0,'C');

            $pdf->Ln(4);
            
        }

        /*----------  Fin Detalles de la tabla  ----------*/

        $cantproductos = $cantproductos + 1; // contando el de la preventa
        $pdf->Ln(2);
        $pdf->Cell(35,4,iconv("UTF-8", "ISO-8859-1","Cantidad de artículos: $cantproductos"),0,0,'C');
        $pdf->Ln(4);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(6);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Tipo de Pago"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$tipopago"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$total_v MXN"),0,0,'C');
        
        $pdf->Ln(10);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');

        $pdf->Ln(9);

        # Codigo de barras #
        $pdf->Code128(5,$pdf->GetY(),"COD000001V00$id_v",70,20);
        $pdf->SetXY(0,$pdf->GetY()+21);
        $pdf->SetFont('Arial','',14);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","COD000001V00$id_v"),0,'C',false);
        
        # Nombre del archivo PDF #
            $pdf->Output("F","../comprobantes/ventas/venta_num$id_v.pdf",true);
        // $pdf->Output("I","Ticket_Nro_1.pdf",true);

        // ----------------------------------------------------------------------------



        if($result){
            echo "yes";
        }else{
            echo "hubo error en la consulta";
        }
        
    }

    if( isset($_POST['subir-lista-productos3']) ){

        $idprev = $_POST['idprev'];
        $nomprev = $_POST['nomprev'];
        $cantprev = $_POST['cantprev'];
        $precioprev = $_POST['precioprev'];
        $abonoprev = $_POST['abonoprev'];

        $queryprev = "INSERT INTO lista_venta(prod_lv, nom_lv, cant_lv, preciou_lv)
            VALUES($idprev,'$nomprev', $cantprev, $precioprev);";

        $resultprev = $conn->query($queryprev);

        // GENERAR TICKET -------------------------------------------------------------

        $query2 = "SELECT * FROM ventas WHERE id_v = (SELECT id_v FROM ventas ORDER BY id_v DESC LIMIT 1);";
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $id_v = $row['id_v'];
            $caja_v = $row['caja_v'];
            $emp_v = $row['emp_v'];
            $fecha_v = $row['fecha_v'];
            $hra_v = $row['hra_v'];
            $hora_v = substr($hra_v, 0, 5);

            $nombre = $row['nombre_v'];
            $tel = $row['tel_v'];
            $email = $row['email_v'];

            $tipopago = $row['tipopago_v'];
            $tipopago = ($tipopago == 'C') ? 'Efectivo' : (($tipopago == 'T') ? 'Transferencia' : 'Otro');

            $total_v = $row['total_v'];
        }

        $query3 = "SELECT user_u FROM usuarios WHERE id_u = $emp_v;";
        $result3 = $conn->query($query3);

        while($row = $result3->fetch(PDO::FETCH_ASSOC)){
            $nomemp = $row['user_u'];
        }

        $tamañoticket = 240;
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
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Tipo transación: VENTA"),0,'C',false);

        $pdf->Ln(1);
        $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
        $pdf->Ln(5);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: $fecha_v $hora_v"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja Nro: #$caja_v"),0,'C',false);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Atendió: $nomemp"),0,'C',false);
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","No. Venta: #$id_v"),0,'C',false);
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
        
        $pdf->Cell(25,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');

        $pdf->Ln(3);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
        $pdf->Ln(4);



        /*----------  Detalles de la tabla  ----------*/

            $totalprev = $cantprev * $precioprev;


            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","$nomprev"),0,'C',false);
            $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","$cantprev"),0,0,'C');
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","$$precioprev MXN"),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","$$totalprev MXN"),0,0,'C');
            
            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","  "),0,'C',false);
            $pdf->Cell(10,1,iconv("UTF-8", "ISO-8859-1","  "),0,0,'C');
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1","  "),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1","Abono"),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1","-$$abonoprev MXN"),0,0,'C');

            $pdf->Ln(4);
        /*----------  Fin Detalles de la tabla  ----------*/
        $pdf->Ln(2);
        $pdf->Cell(35,4,iconv("UTF-8", "ISO-8859-1","Cantidad de artículos: 1"),0,0,'C');
        $pdf->Ln(4);
        $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(6);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Tipo de Pago"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$tipopago"),0,0,'C');

        $pdf->Ln(5);

        $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');
        $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$$total_v MXN"),0,0,'C');
        
        $pdf->Ln(10);

        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');

        $pdf->Ln(9);

        # Codigo de barras #
        $pdf->Code128(5,$pdf->GetY(),"COD000001V00$id_v",70,20);
        $pdf->SetXY(0,$pdf->GetY()+21);
        $pdf->SetFont('Arial','',14);
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","COD000001V00$id_v"),0,'C',false);
        
        # Nombre del archivo PDF #
            $pdf->Output("F","../comprobantes/ventas/venta_num$id_v.pdf",true);
        // $pdf->Output("I","Ticket_Nro_1.pdf",true);

        // ----------------------------------------------------------------------------

        if($resultprev){
            echo "yes";
        }else{
            echo "hubo error en la consulta";
        }
        
    }

    if( isset($_POST['generar-ticket-venta']) ){
        $query2 = "SELECT img_v FROM ventas where id_v = (SELECT MAX(id_v) FROM ventas LIMIT 1);";
        $result2 = $conn->query($query2);

        while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $ticket = $row['img_v'];
        }

        echo "<a href='$ticket' style='font-size: 18px;' target='_blank'>Imprimir Comprobante</a>";
    }
    

 
?>