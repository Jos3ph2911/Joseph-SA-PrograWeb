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

// Obtener el id del viaje asociado para devolver el espacio
$sql = "SELECT id_viaje FROM reservas WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res) {
    $id_viaje = $res['id_viaje'];

    // Eliminar la reserva
    $conexion->query("DELETE FROM reservas WHERE id = $id_reserva");

    // Liberar espacio
    $conexion->query("UPDATE viajes SET espacios_disponibles = espacios_disponibles + 1 WHERE id = $id_viaje");
}

header("Location: reservas_recibidas.php");
exit();
?>
