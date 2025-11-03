<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo chofer puede acceder
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

// Validar que se reciba el ID
if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit;
}

// --- Función local para eliminar el viaje ---
function eliminarViaje($conexion, $id_viaje, $id_chofer) {
    $sql = "DELETE FROM viajes WHERE id = ? AND id_chofer = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $id_viaje, $id_chofer);
    $stmt->execute();
}

// Ejecutar la eliminación
eliminarViaje($conexion, $_GET['id'], $_SESSION['id']);

// Redirigir de nuevo al listado
header("Location: listar.php");
exit;
?>
