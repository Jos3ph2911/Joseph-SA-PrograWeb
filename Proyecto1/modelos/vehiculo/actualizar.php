<?php
function actualizarVehiculo($conexion, $id, $placa, $color, $marca, $modelo, $anio, $capacidad, $foto = null) {
    if ($foto) {
        $sql = "UPDATE vehiculos SET placa=?, color=?, marca=?, modelo=?, anio=?, capacidad=?, foto=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssissi", $placa, $color, $marca, $modelo, $anio, $capacidad, $foto, $id);
    } else {
        $sql = "UPDATE vehiculos SET placa=?, color=?, marca=?, modelo=?, anio=?, capacidad=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssisi", $placa, $color, $marca, $modelo, $anio, $capacidad, $id);
    }
    return $stmt->execute();
}
?>
