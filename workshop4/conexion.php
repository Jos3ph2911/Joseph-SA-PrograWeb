<?php
//conexion de la base de datos
$conexion = new mysqli("localhost", "Jos2911", "12345", "isw613_workshop4");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
