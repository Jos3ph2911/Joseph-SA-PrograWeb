<?php
include("conexion.php");
session_start();
if ($_SESSION['rol'] != 'Administrador') {
    header("Location: index.php");
}
//Aqui se cambia entre estado activo o inactivo
$id = $_GET['id'];
$conexion->query("UPDATE usuarios SET estado='Inactivo' WHERE id=$id");
header("Location: index.php");
?>
