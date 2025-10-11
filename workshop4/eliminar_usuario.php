<?php
include("conexion.php");
session_start();
if ($_SESSION['rol'] != 'Administrador') {
    header("Location: index.php");
}
//Aqui se elimina el usuario en la base de datos
$id = $_GET['id'];
$conexion->query("DELETE FROM usuarios WHERE id=$id");
header("Location: index.php");
?>
