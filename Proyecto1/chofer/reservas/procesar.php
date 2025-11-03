<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo chofer
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['accion'])) {
    header("Location: reservas_recibidas.php");
    exit();
}

$id_reserva = intval($_GET['id']);
$accion = $_GET['accion'];

// Obtener viaje vinculado
$sql = "SELECT id_viaje FROM reservas WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
    header("Location: reservas_recibidas.php");
    exit();
}

$id_viaje = $res['id_viaje'];

if ($accion === 'aceptar') {
    // Cambiar estado a ACEPTADA
    $conexion->query("UPDATE reservas SET estado = 'ACEPTADA' WHERE id = $id_reserva");
} elseif ($accion === 'rechazar') {
    // Cambiar estado a RECHAZADA y devolver espacio
    $conexion->query("UPDATE reservas SET estado = 'RECHAZADA' WHERE id = $id_reserva");
    $conexion->query("UPDATE viajes SET espacios_disponibles = espacios_disponibles + 1 WHERE id = $id_viaje");
}

header("Location: reservas_recibidas.php");
exit();
?>
