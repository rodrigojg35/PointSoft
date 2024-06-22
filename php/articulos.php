<?php
    session_start();
    require 'conexion.php';

    if(isset($_POST['imprimir-productos'])){
        $result = $conn->query
        ("SELECT * FROM productos WHERE id_p > 1;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $id = $row['id_p'];
            $nom = $row['nom_p'];
            $cant = $row['cant_p'];
            $precio = $row['preciou_p'];
            $estado = $row['estado_p'];
            $preventa = $row['preventa_p'];
            $img = $row['img_p'];

            echo '<div class="producto">';

            if($img == "NULL" || $img === null){
                echo '<img style="box-shadow: none;" src="imagenes/Portada.png"  alt="Producto 1" width="240" height="240"><br>';
            }else{
                echo "<img src='imagenes/$img' alt='Producto 1' width='240' height='240'><br>";
            }

            echo "<div class='nombre-producto'>$nom</div>
                  <div class='precio'>$$precio</div>";

            if($preventa == 'S'){
                echo "<div class='existencia3'>Por Pedido</div>";
            }

            if($preventa == 'N'){
                if($estado == 'D'){
                    echo "<div class='existencia'>Disponible</div>";
                }

                if($estado == 'N'){
                    echo "<div class='existencia2'>No Disponible</div>";
                }
                
            }


                  
            echo "<button class='boton-agregar' onclick='verDetallesProducto(\"$id\")'>Ver detalles</button>
            <button class='boton-agregar' onclick='modificarArticulo(\"$id\")'>Modificar</button>
                  </div> ";

                  


        }
    }

    if( isset($_POST['abrirPanelProducto']) && isset($_POST['producto']) ){
        $id = $_POST['producto'];

        $result = $conn->query
        ("SELECT * FROM vista_productos_con_transacciones WHERE id_p = $id;");

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $id = $row['id_p'];
            $nom = $row['nom_p'];

            $cant = $row['cant_p'];
            $precio = $row['preciou_p'];
            $estado = $row['estado_p'];
            $preventa = $row['preventa_p'];
            $img = $row['img_p'];
            $transacciones = $row['total_transacciones'];
        }

        echo "<div class='modal-header' style='border-bottom: 1px solid #000;'>
            <h5 class='modal-title' id='exampleModalLabel'>Detalles del Producto |&nbsp;</h5>";

        
        
        if($transacciones > 0 ){

            if( $estado == "D" || $preventa == "S" ){
                if($preventa == "N"){
                    echo "<div onclick='setNoDisp(\"$id\")'><span style='color: red; cursor: pointer;'>Poner como No Disponible</span></div>";
                }

                if($preventa == "S"){
                    echo "<div onclick='setNoDispPrev(\"$id\")'><span style='color: red; cursor: pointer;'>Poner como No Disponible</span></div>";
                }
            }
            
            

        }else{
            
            echo "<div onclick='eliminarProducto(\"$id\")'><span style='color: red; cursor: pointer;'>Eliminar producto</span></div>";
            
        }
        
      
        
        echo "<button type='button' class='btn-close' data-dismiss='modal' aria-label='Close'></button>
            </div>

            <div class='modal-body' style='border-bottom: 1px solid #000;'>
            <div class='container'>
            <div class='row'>
                <!-- Imagen a la izquierda -->
                <div class='col-md-6' style='border-right: 1px solid #000; width: 47%' id='panelprod-img'>";

        if($img == "NULL" || $img === null){
            echo "<img  class='imagen-producto2' src='imagenes/portada.png' alt='Producto'>";
        }else{
            echo "<img src='imagenes/$img' alt='Producto' class='imagen-producto'>";
        }
        
        echo "</div>
                <!-- Información a la derecha -->
                <div class='col-md-6 producto-body'>
                <div class='nombre-producto-detalles' style='margin-bottom: 5px;'>$nom</div>
                <div class='sku' style='margin-bottom: 10px;'>SKU$id: SKU 5537</div>
                <ul style='list-style: none; padding: 0;'>
                    <div class='precio'><span style='color: #000;'>Precio:</span> <span style='color: #333;'>$$precio MXN</span></div>";

        if($estado == "D"){
            echo "<div class='precio' style='margin-bottom: 10px;'><span style='color: #000;'>Disponibilidad:</span> <span style='color:green;'>Disponible</span> </div>";
        }
            
        if($estado == "N"){
            echo "<div class='precio' style='margin-bottom: 10px;'><span style='color: #000;'>Disponibilidad:</span> <span style='color:red;'>No disponible</span> </div>";
        }
                    
        echo " <div class='precio' ><span style='color: #000;'>Cantidad:</span> <span style='color: #333;'>$cant unidades</span></div>
                    <div class='precio' style='margin-bottom: 10px;'><span style='color: #000;'>Añadido en:</span> <span style='color: #333;'>2024-05-22</span></div>";
        
        if($preventa == "S"){
            echo "<div class='precio' ><span style='color: #000;'>Por pedido:</span> <span style='color: green;'>Disponible</span></div>";
        }

        if($preventa == "N"){
            echo "<div class='precio' ><span style='color: #000;'>Por pedido:</span> <span style='color: red;'>No disponible</span></div>";
        }
        echo "</ul>
                
                
                </p>
                </div>
            </div>
            </div>
            </div>
            <div class='modal-footer'>
            <ul style='list-style: none; padding: 0;'>
                <div class='modal-desc' style='margin-bottom: 10px;'><span style='color: #000;'>Descripción:</div>

                <div class='descripcion-articulo-body' style='margin-bottom: 5px;'>
                Descripcion generica de producto sobre el cual se va a trabajar
                </div>
                <div class='descripcion-articulo-body'>
                Descripcion generica de producto sobre el cual se va a trabajar
                Descripcion generica de producto sobre el cual se va a trabajar. 
                Descripcion generica de producto sobre el cual se va a trabajar


                </div>
            </ul>
            </div>
        
        ";



    }
    
    if( isset($_FILES['imagen']['name']) && isset($_FILES['imagen']['type']) ){
        $nombre = $_POST['nombre'];
        $cantidad = $_POST['cantidad'];
        $descr = $_POST['descr'];
        $precio = $_POST['precio'];
        $preventa = $_POST['preventa'];     //  es booleano

        // Recibir archivo
        $nombreArchivo = $_FILES['imagen']['name'];
        $tipoArchivo = $_FILES['imagen']['type'];
        $tamanoArchivo = $_FILES['imagen']['size'];
        $nombreTemporal = $_FILES['imagen']['tmp_name'];
        $errorArchivo = $_FILES['imagen']['error'];

        // Aquí puedes manejar el archivo según tus necesidades
        // Por ejemplo, puedes moverlo a una carpeta específica
        if ($errorArchivo === UPLOAD_ERR_OK) {
            $rutaDestino = "../imagenes/" . $nombreArchivo;
            move_uploaded_file($nombreTemporal, $rutaDestino);
            // Aquí $rutaDestino contendrá la ruta completa del archivo guardado
            
            if($preventa == "true"){
                $preventa = 'S';
                $result = $conn->query
                ("INSERT INTO productos(nom_p, cant_p, preciou_p, estado_p, preventa_p, img_p, descr_p)
                VALUES('$nombre', $cantidad, $precio, 'N', '$preventa', '$nombreArchivo', '$descr');");
            }

            if($preventa == "false"){
                $preventa = 'N';
                $result = $conn->query
                ("INSERT INTO productos(nom_p, cant_p, preciou_p, estado_p, preventa_p, img_p, descr_p)
                VALUES('$nombre', $cantidad, $precio, 'D', '$preventa', '$nombreArchivo','$descr');");
            }


            if($result){
               echo 'yes'; 
            }

            


        } else {
            // Manejar el error al subir el archivo si es necesario
            echo "Error al subir el archivo: " . $errorArchivo;
        }


    }

    if( isset($_POST['subir-producto-noimg'])){
        $nombre = $_POST['nom'];
        $cantidad = $_POST['cant'];
        $descr = $_POST['descr'];
        $precio = $_POST['precio'];
        $preventa = $_POST['preventa'];     //  es booleano

        if($preventa == "true"){
            $preventa = 'S';
            $result = $conn->query
            ("INSERT INTO productos(nom_p, cant_p, preciou_p, estado_p, preventa_p, descr_p)
            VALUES('$nombre', $cantidad, $precio, 'N', '$preventa', '$descr');");
        }

        if($preventa == "false"){
            $preventa = 'N';
            $result = $conn->query
            ("INSERT INTO productos(nom_p, cant_p, preciou_p, estado_p, preventa_p, descr_p)
            VALUES('$nombre', $cantidad, $precio, 'D', '$preventa', '$descr');");
        }


        if($result){
           echo 'yes'; 
        }

    }

    if( isset($_POST['modificar-producto'])){
        $id = $_POST['id'];
        $nombre = $_POST['nom'];
        $cantidad = $_POST['cant'];
        $descr = $_POST['descr'];
        $precio = $_POST['precio'];
        $preventa = $_POST['preventa'];     //  es booleano

        if($preventa == "true"){
            $preventa = 'S';
            $result = $conn->query
            ("UPDATE productos 
            SET nom_p = '$nombre', cant_p = $cantidad, preciou_p = $precio, estado_p = 'N', preventa_p = '$preventa', descr_p = '$descr' 
            WHERE id_p = $id;");
        }

        if($preventa == "false"){
            $preventa = 'N';
            $result = $conn->query
            ("UPDATE productos 
            SET nom_p = '$nombre', cant_p = $cantidad, preciou_p = $precio, estado_p = 'D', preventa_p = '$preventa', descr_p = '$descr' 
            WHERE id_p = $id;");
        }

        if($result){
           echo 'yes'; 
        }

    }

    if( isset($_POST['eliminarProducto'])){
        $id = $_POST['id'];

        $result = $conn->query
            ("DELETE FROM productos WHERE id_p = $id;");
        
            if($result){
                echo 'yes';
            }

    }

    if( isset($_POST['ponerProductoNoDisp'])){
        $id = $_POST['id'];

        $result = $conn->query
            ("UPDATE productos SET estado_p = 'N' WHERE id_p = $id;");
        
            if($result){
                echo 'yes';
            }
    }

    if( isset($_POST['ponerProductoDisp'])){
        $id = $_POST['id'];

        $result = $conn->query
            ("UPDATE productos SET estado_p = 'D' WHERE id_p = $id;");
        
            if($result){
                echo 'yes';
            }
    }

    if( isset($_POST['ponerProductoNoDispPrev'])){
        $id = $_POST['id'];

        $result = $conn->query
            ("UPDATE productos SET preventa_p = 'N' WHERE id_p = $id;");
        
            if($result){
                echo 'yes';
            }
    }

    if( isset($_POST['ponerProductoDispFromPrev'])){
        $id = $_POST['id'];

        $result = $conn->query
            ("UPDATE productos SET estado_p = 'D', preventa_p = 'N' WHERE id_p = $id;");
        
            if($result){
                echo 'yes';
            }
    }
    
    if( isset($_POST['obtenerDatosProducto'])){
        $id = $_POST['id'];

        $result = $conn->query
            ("SELECT * FROM productos WHERE id_p = $id;");
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $id = $row['id_p'];
            $nom = $row['nom_p'];
            $cant = $row['cant_p'];
            $precio = $row['preciou_p'];
            $estado = $row['estado_p'];
            $preventa = $row['preventa_p'];
            $descr = $row['descr_p'];
            
            $datos = array('id' => $id, 'nombre' => $nom, 'cantidad' => $cant, 'precio' => $precio, 'estado' => $estado, 'preventa' => $preventa, 'descr' => $descr);
            echo json_encode($datos);
        }

    }
    

    
?>