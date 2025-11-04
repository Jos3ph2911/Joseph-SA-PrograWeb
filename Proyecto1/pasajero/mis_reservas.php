<?php
session_start();
include("../config/conexion.php");
include("../includes/autenticar.php");

// Verificar acceso solo para pasajeros
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'pasajero') {
    header("Location: ../inicio_sesion.php");
    exit();
}

$id_pasajero = $_SESSION['id'];

// Procesar cancelación de reserva
if (isset($_GET['cancelar'])) {
    $id_reserva = intval($_GET['cancelar']);

    // Validar que la reserva pertenezca al pasajero y esté pendiente o aceptada
    $consulta = $conexion->prepare("
        SELECT r.id_viaje 
        FROM reservas r
        WHERE r.id = ? AND r.id_pasajero = ? AND r.estado IN ('PENDIENTE', 'ACEPTADA')
    ");
    $consulta->bind_param("ii", $id_reserva, $id_pasajero);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado->num_rows > 0) {
        $reserva = $resultado->fetch_assoc();

        // Eliminar la reserva
        $delete = $conexion->prepare("DELETE FROM reservas WHERE id = ? AND id_pasajero = ?");
        $delete->bind_param("ii", $id_reserva, $id_pasajero);
        $delete->execute();

        // Devolver el espacio al viaje
        $updateViaje = $conexion->prepare("
            UPDATE viajes 
            SET espacios_disponibles = espacios_disponibles + 1 
            WHERE id = ?
        ");
        $updateViaje->bind_param("i", $reserva['id_viaje']);
        $updateViaje->execute();

        $mensaje = "✅ Reserva cancelada correctamente.";
    } else {
        $mensaje = "⚠️ No se puede cancelar esta reserva.";
    }
}

// Consultar todas las reservas del pasajero (solo choferes activos)
$sql = "
    SELECT r.id, r.estado, r.fecha_reserva, 
           v.titulo, v.lugar_salida, v.lugar_llegada, v.fecha_hora,
           u.nombre AS chofer_nombre, u.apellido AS chofer_apellido, u.estado AS estado_chofer
    FROM reservas r
    INNER JOIN viajes v ON r.id_viaje = v.id
    INNER JOIN usuarios u ON v.id_chofer = u.id
    WHERE r.id_pasajero = ? 
      AND u.estado = 'ACTIVA'
    ORDER BY r.fecha_reserva DESC
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_pasajero);
$stmt->execute();
$reservas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Reservas - Aventones</title>
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        line-height: 1.4; /* evita recortes de letras descendentes */
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

    /* Contenido principal */
    main {
        flex: 1;
        padding: 30px 40px;
    }
    h2 {
        color: #333;
        margin-bottom: 15px;
    }

    /* Tabla de reservas */
    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
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
    th { background: #007bff; color: white; }
    tr:hover { background: #f1f1f1; }

    /* Mensajes */
    .mensaje {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
    }
    .exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* Acciones */
    .acciones a {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 5px;
        text-decoration: none;
        color: white;
        font-size: 0.9em;
    }
    .btn-cancelar { background: #dc3545; }
    .btn-cancelar:hover { background: #b52b38; }

    /* Enlaces inferiores */
    .volver {
        background: #6c757d;
        color: white;
        padding: 8px 14px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
    }
    .volver:hover { background: #5a6268; }

    /* Botón cerrar sesión */
    .cerrar-sesion {
        background: #dc3545;
        color: white;
        padding: 8px 14px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
    }
    .cerrar-sesion:hover { background: #c82333; }

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
<script>
function confirmarCancelacion() {
    return confirm("¿Está seguro de que desea cancelar esta reserva?");
}
</script>
</head>
<body>

<header>
    <img src="../logo/logo.png" alt="Logo Aventones">
    <a href="../cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
</header>

<main>
    <h2>📋 Mis Reservas</h2>

    <!-- Mensajes de confirmación o error -->
    <?php if (isset($mensaje)): ?>
        <div class="mensaje <?php echo (str_contains($mensaje, '✅')) ? 'exito' : 'error'; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <!-- Tabla de reservas -->
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Chofer</th>
                <th>Salida</th>
                <th>Llegada</th>
                <th>Fecha/Hora</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reservas->num_rows > 0): ?>
                <?php while ($r = $reservas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($r['chofer_nombre'] . ' ' . $r['chofer_apellido']); ?></td>
                        <td><?php echo htmlspecialchars($r['lugar_salida']); ?></td>
                        <td><?php echo htmlspecialchars($r['lugar_llegada']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['fecha_hora'])); ?></td>
                        <td><?php echo htmlspecialchars($r['estado']); ?></td>
                        <td class="acciones">
                            <?php if (in_array($r['estado'], ['PENDIENTE', 'ACEPTADA'])): ?>
                                <a href="?cancelar=<?php echo $r['id']; ?>" class="btn-cancelar" onclick="return confirmarCancelacion();">Cancelar</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">No tiene reservas activas o los choferes fueron desactivados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="buscar_viajes.php" class="volver">← Volver a buscar viajes</a>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
