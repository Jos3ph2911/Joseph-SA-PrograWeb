<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

$id_chofer = $_SESSION['id'];

$sql = "SELECT 
            r.id AS id_reserva, 
            r.estado AS estado_reserva, 
            r.fecha_reserva,
            v.titulo, v.lugar_salida, v.lugar_llegada, v.fecha_hora,
            u.nombre AS nombre_pasajero, u.apellido AS apellido_pasajero
        FROM reservas r
        INNER JOIN viajes v ON r.id_viaje = v.id
        INNER JOIN usuarios u ON r.id_pasajero = u.id
        WHERE v.id_chofer = ?
        ORDER BY r.fecha_reserva DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$resultado = $stmt->get_result();

function badgeEstado($estado) {
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
    <title>Reservas Recibidas - Aventones</title>
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
        }

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
            border-radius: 0;
        }

        main {
            flex: 1;
            padding: 30px 40px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:hover { background: #f1f1f1; }

        .acciones a {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            margin-right: 5px;
            color: white;
        }

        .aceptar { background: #28a745; }
        .aceptar:hover { background: #218838; }

        .rechazar { background: #dc3545; }
        .rechazar:hover { background: #c82333; }

        .cancelar { background: #6c757d; }
        .cancelar:hover { background: #5a6268; }

        .btn-volver {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-volver:hover { background-color: #5a6268; }

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
    <a href="../viajes/listar.php" class="btn-volver">← Volver</a>
</header>

<main>
    <h2>Reservas recibidas</h2>
    <p>Gestione las solicitudes de sus viajes.</p>

    <table>
        <thead>
            <tr>
                <th>Pasajero</th>
                <th>Viaje</th>
                <th>Salida</th>
                <th>Llegada</th>
                <th>Fecha del viaje</th>
                <th>Fecha de reserva</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($r = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['nombre_pasajero'] . " " . $r['apellido_pasajero']); ?></td>
                        <td><?php echo htmlspecialchars($r['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($r['lugar_salida']); ?></td>
                        <td><?php echo htmlspecialchars($r['lugar_llegada']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['fecha_hora'])); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['fecha_reserva'])); ?></td>
                        <td><?php echo badgeEstado($r['estado_reserva']); ?></td>
                        <td class="acciones">
                            <?php if ($r['estado_reserva'] === 'PENDIENTE'): ?>
                                <a href="procesar.php?id=<?php echo $r['id_reserva']; ?>&accion=aceptar" class="aceptar">Aceptar</a>
                                <a href="procesar.php?id=<?php echo $r['id_reserva']; ?>&accion=rechazar" class="rechazar">Rechazar</a>
                            <?php elseif ($r['estado_reserva'] === 'ACEPTADA'): ?>
                                <a href="cancelar.php?id=<?php echo $r['id_reserva']; ?>" class="cancelar" onclick="return confirm('¿Seguro que desea cancelar esta reserva?')">Cancelar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No tiene reservas recibidas aún.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
