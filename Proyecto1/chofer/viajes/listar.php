<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo chofer puede acceder
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

$id_chofer = $_SESSION['id'];

// Obtener los viajes del chofer
$sql = "SELECT v.id, v.titulo, v.lugar_salida, v.lugar_llegada, v.fecha_hora, 
               v.costo_por_espacio, v.espacios_totales, v.espacios_disponibles,
               ve.placa AS vehiculo
        FROM viajes v
        JOIN vehiculos ve ON v.id_vehiculo = ve.id
        WHERE v.id_chofer = ?
        ORDER BY v.fecha_hora DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Viajes - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn-nuevo {
            display: inline-block; background-color: #28a745; color: white;
            padding: 8px 12px; border-radius: 5px; text-decoration: none;
            font-weight: bold; margin-bottom: 15px;
        }
        .btn-nuevo:hover { background-color: #218838; }
        .btn-volver {
            display: inline-block; background-color: #6c757d; color: white;
            padding: 8px 12px; border-radius: 5px; text-decoration: none;
            font-weight: bold; margin-bottom: 15px; margin-left: 10px;
        }
        .btn-volver:hover { background-color: #5a6268; }
        .acciones a { margin-right: 8px; text-decoration: none; color: #007bff; }
        .acciones a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>🛣️ Mis Viajes</h2>

    <a href="crear.php" class="btn-nuevo">+ Crear nuevo viaje</a>
    <a href="../vehiculos/listar.php" class="btn-volver">← Volver a mis vehículos</a>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Vehículo</th>
                <th>Salida</th>
                <th>Llegada</th>
                <th>Fecha y hora</th>
                <th>Costo</th>
                <th>Espacios</th>
                <th>Disponibles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['vehiculo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['lugar_salida']); ?></td>
                        <td><?php echo htmlspecialchars($fila['lugar_llegada']); ?></td>
                        <td><?php echo htmlspecialchars($fila['fecha_hora']); ?></td>
                        <td>₡<?php echo number_format($fila['costo_por_espacio'], 2); ?></td>
                        <td><?php echo $fila['espacios_totales']; ?></td>
                        <td><?php echo $fila['espacios_disponibles']; ?></td>
                        <td class="acciones">
                            <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                            <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que desea eliminar este viaje?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">No tiene viajes registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
