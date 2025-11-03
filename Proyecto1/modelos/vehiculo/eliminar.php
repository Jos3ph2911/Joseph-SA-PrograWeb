<?php
function eliminarVehiculo($conexion, $id) {
    $sql = "DELETE FROM vehiculos WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
