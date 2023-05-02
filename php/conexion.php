<?php
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "tienda_videojuegos";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }
?>