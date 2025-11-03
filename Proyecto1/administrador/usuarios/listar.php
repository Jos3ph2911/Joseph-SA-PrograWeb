<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

// Cambiar estado (activar/desactivar)
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

// Consultar usuarios
$resultado = $conexion->query("SELECT id, nombre, apellido, correo, rol, estado FROM usuarios ORDER BY rol, nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 40px; }
        h2 { color: #007bff; }
        table {
            width: 100%; border-collapse: collapse; background: white;
            border-radius: 8px; box-shadow: 0 0 6px rgba(0,0,0,0.1); overflow: hidden;
        }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }
        .acciones a {
            padding: 6px 10px; border-radius: 5px; text-decoration: none; color: white;
            font-size: 0.9em; margin-right: 5px;
        }
        .activar { background: #28a745; }
        .activar:hover { background: #218838; }
        .desactivar { background: #dc3545; }
        .desactivar:hover { background: #b52b38; }
        .volver {
            display:inline-block; margin-top:20px; background:#6c757d;
            color:white; padding:8px 15px; border-radius:5px; text-decoration:none;
        }
        .volver:hover { background:#5a6268; }
        form {
            background:white; padding:15px; border-radius:8px;
            box-shadow:0 0 6px rgba(0,0,0,0.1); margin-bottom:20px;
        }
        input {
            padding:6px; margin:5px; border:1px solid #ccc; border-radius:5px;
        }
        button {
            padding:6px 12px; background:#007bff; color:white;
            border:none; border-radius:5px; cursor:pointer;
        }
        button:hover { background:#0056b3; }
    </style>
</head>
<body>

<h2>👥 Gestión de Usuarios</h2>
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

<a href="../panel.php" class="volver">← Volver al panel</a>

</body>
</html>
