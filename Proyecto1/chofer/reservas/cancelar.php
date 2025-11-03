<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo chofer
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: reservas_recibidas.php");
    exit();
}

$id_reserva = intval($_GET['id']);

// Buscar el viaje asociado a la reserva
$sql = "SELECT id_viaje FROM reservas WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res) {
    $id_viaje = $res['id_viaje'];

    // Eliminar completamente la reserva
    $delete = $conexion->prepare("DELETE FROM reservas WHERE id = ?");
    $delete->bind_param("i", $id_reserva);
    $delete->execute();

    // Devolver el espacio al viaje
    $update = $conexion->prepare("UPDATE viajes SET espacios_disponibles = espacios_disponibles + 1 WHERE id = ?");
    $update->bind_param("i", $id_viaje);
    $update->execute();
}

header("Location: reservas_recibidas.php");
exit();
?>
