<?php
$conexion = new mysqli("localhost", "Joseph", "Jos123", "isw613_proyecto1");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

?>
