<?php
// if para validar que los campos no esten vacios
if (empty($_POST['nombre']) || empty($_POST['apellidos']) || empty($_POST['provincia']) || empty($_POST['username']) || empty($_POST['password'])) {
    die("Error: Todos los campos son obligatorios.");
}

$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$provincia = $_POST['provincia'];
$username = $_POST['username'];
$password = $_POST['password'];

// Conexión de la base de datos
$conexion = new mysqli("localhost",
 "root", 
 "trike123", //contraseña
 "isw613_workshop2");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Insertar a la base de datos
try {
    $sql = "INSERT INTO usuariosWorkshop3 (nombre, apellidos, provincia_id, username, password)
            VALUES ('$nombre', '$apellidos', '$provincia', '$username', '$password')";
    
    if ($conexion->query($sql) === TRUE) {
        header("Location: login.php?user=$username");
        exit();
    } else {
        echo "Error al registrar: " . $conexion->error;
    }
} catch (Exception $e) {
    echo "Ocurrió un error: " . $e->getMessage();
}

$conexion->close();
?>
