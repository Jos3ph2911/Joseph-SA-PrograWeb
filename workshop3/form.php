<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar de Usuario</title>
</head>
<body>

<h2>Registrar de Usuario</h2>

<form action="print.php" method="post">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" required>
    <br><br>

    <label>Apellidos:</label><br>
    <input type="text" name="apellidos" required>
    <br><br>



    <label>Nombre de usuario:</label><br>
    <input type="text" name="username" required>
    <br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required>
    <br><br>

    <label>Provincia:</label><br>
    <select name="provincia" required>
        <?php
        $conexion = new mysqli("localhost", "root", "trike123", "isw613_workshop2");

        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        $sql = "SELECT id, nombre FROM provincias";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<option value='" . $fila['id'] . "'>" . $fila['nombre'] . "</option>";
            }
        } else {
            echo "<option value=''>No hay provincias registradas</option>";
        }

        $conexion->close();
        ?>
    </select>
    <br><br>

    <button type="submit">Registrar</button>

</form>

</body>
</html>

