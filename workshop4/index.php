<?php
//Index donde se muestran los usuarios existentes en una tabla, donde se pueden modificar, inhabilitar o eliminar
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");
$resultado = $conexion->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .top-links {
            text-align: center;
            margin-bottom: 20px;
        }

        .top-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }

        .top-links a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a.button {
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        a.button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['rol']; ?>)</h2>

    <div class="top-links">
        <a href="logout.php" class="button">Cerrar sesión</a>
        <?php if ($_SESSION['rol'] == 'Administrador') { ?>
            <a href="agregar_usuario.php" class="button">Agregar Usuario</a>
        <?php } ?>
    </div>

    <table>
        <tr>
            <th>ID</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th><th>Acciones</th>
        </tr>
        <?php while ($fila = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila['id'] ?></td>
                <td><?= $fila['username'] ?></td>
                <td><?= $fila['nombre'] ?></td>
                <td><?= $fila['rol'] ?></td>
                <td><?= $fila['estado'] ?></td>
                <td>
                    <?php if ($_SESSION['rol'] == 'Administrador') { ?>
                        <a href="editar_usuario.php?id=<?= $fila['id'] ?>" class="button">Editar</a>
                        <a href="eliminar_usuario.php?id=<?= $fila['id'] ?>" class="button" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                        <a href="deshabilitar_usuario.php?id=<?= $fila['id'] ?>" class="button">Deshabilitar</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
