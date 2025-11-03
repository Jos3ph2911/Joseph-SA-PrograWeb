<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");
include("../../modelos/vehiculo/index.php");

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

eliminarVehiculo($conexion, $_GET['id']);
header("Location: listar.php");
exit;
?>
