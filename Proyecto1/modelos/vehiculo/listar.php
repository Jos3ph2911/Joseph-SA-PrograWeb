<?php
function listarVehiculosPorChofer($conexion, $id_usuario) {
    $sql = "SELECT * FROM vehiculos WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result();
}
?>
