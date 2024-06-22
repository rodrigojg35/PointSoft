<?php

session_start();
    
    if (isset($_SESSION['user'])) {

        echo $_SESSION['tipo'];

    } else {
        // La sesión no está iniciada, el usuario no está autenticado

        session_unset();

        session_destroy();

        echo "no";

        
    }


?>