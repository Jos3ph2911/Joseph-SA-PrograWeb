<?php
//Aqui se edita la informacion del usuario, como nombre, contraseña y demas, solo los administradores tienen acceso
include("conexion.php");
session_start();
if ($_SESSION['rol'] != 'Administrador') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$resultado = $conexion->query("SELECT * FROM usuarios WHERE id=$id");
$fila = $resultado->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $password = $_POST['password'];

    $sql = "UPDATE usuarios SET nombre='$nombre', rol='$rol', estado='$estado'";
    if (!empty($password)) {
        $hash = md5($password);
        $sql .= ", password='$hash'";
    }
    $sql .= " WHERE id=$id";

    $conexion->query($sql);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-box {
            background-color: white;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"], select {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #4CAF50;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Editar Usuario</h2>
        <form method="POST">
            <input type="text" name="nombre" value="<?= htmlspecialchars($fila['nombre']) ?>" placeholder="Nombre completo" required><br>
            <input type="password" name="password" placeholder="Dejar vacío para no cambiar"><br>
            <select name="rol">
                <option value="Usuario" <?= $fila['rol'] == 'Usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="Administrador" <?= $fila['rol'] == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
            </select><br><br>
            <select name="estado">
                <option value="Activo" <?= $fila['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                <option value="Inactivo" <?= $fila['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select><br><br>
            <button type="submit">Actualizar</button>
        </form>
        <a href="index.php">Volver</a>
    </div>
</body>
</html>
