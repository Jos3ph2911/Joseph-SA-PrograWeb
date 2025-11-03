<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

// Consultar todos los viajes con datos del chofer y vehículo
$sql = "
    SELECT v.id, v.titulo, v.lugar_salida, v.lugar_llegada, v.fecha_hora,
           v.costo_por_espacio, v.espacios_totales, v.espacios_disponibles,
           u.nombre AS nombre_chofer, u.apellido AS apellido_chofer,
           ve.marca, ve.modelo, ve.anio, ve.placa,
           (SELECT COUNT(*) FROM reservas r WHERE r.id_viaje = v.id) AS total_reservas
    FROM viajes v
    INNER JOIN usuarios u ON v.id_chofer = u.id
    INNER JOIN vehiculos ve ON v.id_vehiculo = ve.id
    ORDER BY v.fecha_hora DESC
";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Viajes Registrados - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 40px; }
        h2 { color: #007bff; }
        table {
            width: 100%; border-collapse: collapse; background: white;
            border-radius: 8px; overflow: hidden;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }
        .volver {
            display:inline-block; margin-top:20px; background:#6c757d;
            color:white; padding:8px 15px; border-radius:5px; text-decoration:none;
        }
        .volver:hover { background:#5a6268; }
    </style>
</head>
<body>

<h2>🛣️ Viajes Registrados</h2>
<p>Listado general de todos los viajes registrados en el sistema.</p>

<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Chofer</th>
            <th>Vehículo</th>
            <th>Salida</th>
            <th>Llegada</th>
            <th>Fecha/Hora</th>
            <th>Costo</th>
            <th>Totales</th>
            <th>Disponibles</th>
            <th>Reservas</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($v = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($v['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($v['nombre_chofer'] . ' ' . $v['apellido_chofer']); ?></td>
                    <td><?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')'); ?></td>
                    <td><?php echo htmlspecialchars($v['lugar_salida']); ?></td>
                    <td><?php echo htmlspecialchars($v['lugar_llegada']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($v['fecha_hora'])); ?></td>
                    <td>₡<?php echo number_format($v['costo_por_espacio'], 2); ?></td>
                    <td><?php echo $v['espacios_totales']; ?></td>
                    <td><?php echo $v['espacios_disponibles']; ?></td>
                    <td><?php echo $v['total_reservas']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" style="text-align:center;">No hay viajes registrados en el sistema.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="../panel.php" class="volver">← Volver al panel</a>

</body>
</html>
