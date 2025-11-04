<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Verificar acceso solo para administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

// Cambiar estado de usuario
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];

    if ($accion === 'activar') {
        $conexion->query("UPDATE usuarios SET estado='ACTIVA' WHERE id=$id");
    } elseif ($accion === 'desactivar') {
        $conexion->query("UPDATE usuarios SET estado='INACTIVA' WHERE id=$id");
    }

    header("Location: listar.php");
    exit();
}

// Crear nuevo administrador
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    if ($nombre && $apellido && $correo && $contrasena) {
        $hash = password_hash($contrasena, PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (nombre, apellido, cedula, fecha_nacimiento, correo, telefono, contrasena, rol, estado)
                VALUES (?, ?, '000000000', '1990-01-01', ?, '', ?, 'administrador', 'ACTIVA')";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido, $correo, $hash);
        $stmt->execute();
    }

    header("Location: listar.php");
    exit();
}

// Consultar todos los usuarios
$resultado = $conexion->query("SELECT id, nombre, apellido, correo, rol, estado FROM usuarios ORDER BY rol, nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Aventones</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.4; /* evita recortes de descendentes */
        }

        /* Barra superior */
        header {
            background: #007bff;
            color: white;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        header img {
            height: 180px;
            width: auto;
            object-fit: contain;
            border: none;
        }
        .cerrar-sesion {
            background: #dc3545;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .cerrar-sesion:hover { background: #c82333; }

        /* Contenido principal */
        main {
            flex: 1;
            padding: 30px 40px;
        }
        h2 {
            color: #007bff;
            margin-bottom: 10px;
        }
        p {
            color: #333;
            margin-bottom: 20px;
        }

        /* Formulario nuevo admin */
        form {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        form h3 {
            margin-top: 0;
            color: #333;
        }
        input {
            padding: 6px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: #0056b3; }

        /* Tabla de usuarios */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        th, td {
            padding: 11px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            line-height: 1.5;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover { background: #f1f1f1; }

        /* Acciones */
        .acciones a {
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
            margin-right: 5px;
        }
        .activar { background: #28a745; }
        .activar:hover { background: #218838; }
        .desactivar { background: #dc3545; }
        .desactivar:hover { background: #b52b38; }

        /* Botón volver */
        .volver {
            display: inline-block;
            margin-top: 25px;
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .volver:hover { background: #5a6268; }

        /* Pie de página */
        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<header>
    <img src="../../logo/logo.png" alt="Logo Aventones">
    <a href="../panel.php" class="volver">← Volver al panel</a>
</header>

<main>
    <h2>Gestión de Usuarios</h2>
    <p>Administre las cuentas registradas en el sistema.</p>

    <!-- Formulario para crear nuevos administradores -->
    <form method="POST">
        <h3>➕ Crear nuevo administrador</h3>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">Crear</button>
    </form>

    <!-- Tabla de usuarios -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($u = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['nombre'] . " " . $u['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td><?php echo ucfirst($u['rol']); ?></td>
                        <td><?php echo htmlspecialchars($u['estado']); ?></td>
                        <td>
                            <?php if ($u['estado'] === 'ACTIVA'): ?>
                                <a href="?accion=desactivar&id=<?php echo $u['id']; ?>" class="desactivar" onclick="return confirm('¿Desea desactivar este usuario?');">Desactivar</a>
                            <?php else: ?>
                                <a href="?accion=activar&id=<?php echo $u['id']; ?>" class="activar" onclick="return confirm('¿Desea activar este usuario?');">Activar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
