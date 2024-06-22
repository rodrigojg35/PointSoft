<?php
    try{
        $conn = new PDO("mysql:host=localhost;dbname=SAO;",'root','Bolt2010');
    } catch (PDOException $e) {
        die('Falló la Conexión: ' .$e->getMessage());
    }
?>