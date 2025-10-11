<?php
//Aqui se agrega el usuario a la base de datos
include("conexion.php");
session_start();
if ($_SESSION['rol'] != 'Administrador') {
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];

    $conexion->query("INSERT INTO usuarios (username, password, nombre, rol) VALUES ('$username', '$password', '$nombre', '$rol')");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
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
        <h2>Agregar Usuario</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required><br>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <input type="text" name="nombre" placeholder="Nombre completo" required><br>
            <select name="rol">
                <option value="Usuario">Usuario</option>
                <option value="Administrador">Administrador</option>
            </select><br><br>
            <button type="submit">Guardar</button>
        </form>
        <a href="index.php">Volver</a>
    </div>
</body>
</html>
