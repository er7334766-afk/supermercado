<?php

    $host ="localhost";
    $port = "3307"; // cambiar el puerto si es necesario yo utilizo el 3307 
    $db = "p_supermercado";
    $user = "root";
    $pass = "";

    try{ // se intenta conectar a la base de datos, si no se puede conectar

        $conexion = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",$user,$pass);
        $conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
        $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        
    }catch(PDOException $e){ // si no se puede conectar, se muestra un mensaje de error

        die("ERROR DE CONEXION: ". $e ->getMessage());
    }


?>