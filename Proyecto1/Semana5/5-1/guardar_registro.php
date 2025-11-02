<?php
include("db_conexion.php");

$id = $_POST['id'];
$fecha = $_POST['fecha'];
$cantidad = $_POST['cantidad'];
$monto_total = $_POST['monto_total'];
$gravado = $_POST['gravado'];

if ($id) {
    $sql = "UPDATE ventas SET fecha='$fecha', cantidad=$cantidad, monto_total=$monto_total, gravado='$gravado' WHERE id=$id";
} else {
    $sql = "INSERT INTO ventas (fecha, cantidad, monto_total, gravado) VALUES ('$fecha', $cantidad, $monto_total, '$gravado')";
}

if ($conexion->query($sql)) {
    header("Location: listar_registros.php");
    exit;
} else {
    echo "Error al guardar: " . $conexion->error;
}
?>
