<?php
include("db_conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conexion->query("DELETE FROM ventas WHERE id=$id");
}

header("Location: listar_registros.php");
exit;
?>
