<?php
function crearVehiculo($conexion, $id_usuario, $placa, $color, $marca, $modelo, $anio, $capacidad, $foto) {
    $sql = "INSERT INTO vehiculos (id_usuario, placa, color, marca, modelo, anio, capacidad, foto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issssiss", $id_usuario, $placa, $color, $marca, $modelo, $anio, $capacidad, $foto);
    return $stmt->execute();
}
?>
