<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Verificar acceso solo para administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

// Consultar todas las reservas con detalle de chofer y pasajero activos
$sql = "
    SELECT r.id AS id_reserva, r.estado, r.fecha_reserva,
           v.titulo, v.lugar_salida, v.lugar_llegada, v.fecha_hora,
           p.nombre AS nombre_pasajero, p.apellido AS apellido_pasajero,
           c.nombre AS nombre_chofer, c.apellido AS apellido_chofer
    FROM reservas r
    INNER JOIN viajes v ON r.id_viaje = v.id
    INNER JOIN usuarios p ON r.id_pasajero = p.id
    INNER JOIN usuarios c ON v.id_chofer = c.id
    WHERE p.estado = 'ACTIVA' AND c.estado = 'ACTIVA'
    ORDER BY r.fecha_reserva DESC
";
$resultado = $conexion->query($sql);

// Función para mostrar etiqueta de estado
function estadoEtiqueta($estado) {
    switch ($estado) {
        case 'PENDIENTE':
            return "<span style='color:#856404;background:#fff3cd;padding:3px 6px;border-radius:5px;'>Pendiente</span>";
        case 'ACEPTADA':
            return "<span style='color:#155724;background:#d4edda;padding:3px 6px;border-radius:5px;'>Aceptada</span>";
        case 'RECHAZADA':
            return "<span style='color:#721c24;background:#f8d7da;padding:3px 6px;border-radius:5px;'>Rechazada</span>";
        case 'CANCELADA':
            return "<span style='color:#383d41;background:#e2e3e5;padding:3px 6px;border-radius:5px;'>Cancelada</span>";
        default:
            return htmlspecialchars($estado);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas - Administración</title>
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
            line-height: 1.4;
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

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
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
    <h2>📋 Reservas Registradas</h2>
    <p>Vista general de todas las reservas del sistema (solo usuarios activos).</p>

    <!-- Tabla de reservas -->
    <table>
        <thead>
            <tr>
                <th>Pasajero</th>
                <th>Chofer</th>
                <th>Viaje</th>
                <th>Salida</th>
                <th>Llegada</th>
                <th>Fecha del Viaje</th>
                <th>Fecha de Reserva</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($r = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['nombre_pasajero'] . ' ' . $r['apellido_pasajero']); ?></td>
                        <td><?php echo htmlspecialchars($r['nombre_chofer'] . ' ' . $r['apellido_chofer']); ?></td>
                        <td><?php echo htmlspecialchars($r['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($r['lugar_salida']); ?></td>
                        <td><?php echo htmlspecialchars($r['lugar_llegada']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['fecha_hora'])); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['fecha_reserva'])); ?></td>
                        <td><?php echo estadoEtiqueta($r['estado']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No hay reservas registradas con usuarios activos.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
