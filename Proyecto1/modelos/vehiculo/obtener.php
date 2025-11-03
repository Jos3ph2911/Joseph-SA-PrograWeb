<?php
function obtenerVehiculoPorId($conexion, $id) {
    $sql = "SELECT * FROM vehiculos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>
