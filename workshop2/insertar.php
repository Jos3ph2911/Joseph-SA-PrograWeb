<?php
$conexion = new mysqli("localhost", "root", "", "isw613_workshop2");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre   = $_POST['primer_nombre'];
$apellido = $_POST['apellido'];
$correo   = $_POST['correo'];
$telefono = $_POST['telefono'];

$sql = "INSERT INTO usuarios (primer_nombre, apellido, correo, telefono)
        VALUES ('$nombre', '$apellido', '$correo', '$telefono')";

if ($conexion->query($sql) === TRUE) {
    echo "Registro guardado con éxito.<br>";
    echo '<a href="form.php">Volver al formulario</a> | ';
    echo '<a href="lista.php">Ver registros</a>';
} else {
    echo "Error: " . $sql . "<br>" . $conexion->error;
}

$conexion->close();
?>
