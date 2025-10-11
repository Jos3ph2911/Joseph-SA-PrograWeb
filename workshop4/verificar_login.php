<?php
include("conexion.php");
session_start();
//Metodo para verificar que los datos de inicio de sesion sea correcto 
$username = $_POST['username'];
$password = md5($_POST['password']);

$query = $conexion->query("SELECT * FROM usuarios WHERE username='$username' AND password='$password' AND estado='Activo'");
if ($query->num_rows > 0) {
    $usuario = $query->fetch_assoc();
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['rol'] = $usuario['rol'];
    header("Location: index.php");
} else {
    echo "Usuario o contraseña incorrectos, o usuario inactivo.<br>";
    echo "<a href='login.php'>Volver</a>";
}
?>
