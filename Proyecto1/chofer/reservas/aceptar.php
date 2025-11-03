<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: reservas_recibidas.php");
    exit();
}

$id_reserva = intval($_GET['id']);

$sql = "UPDATE reservas SET estado = 'ACEPTADA' WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_reserva);
$stmt->execute();

header("Location: reservas_recibidas.php");
exit();
?>
