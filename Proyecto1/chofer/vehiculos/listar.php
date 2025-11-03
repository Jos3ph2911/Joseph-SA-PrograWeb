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

// Obtener los vehículos del chofer
$sql = "SELECT * FROM vehiculos WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Vehículos - Aventones</title>
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
            font-weight: bold; margin-left: 10px;
        }
        .btn-volver:hover { background-color: #5a6268; }
        .acciones a { margin-right: 8px; text-decoration: none; color: #007bff; }
        .acciones a:hover { text-decoration: underline; }
        img { border-radius: 8px; }
    </style>
</head>
<body>
    <h2>🚗 Mis Vehículos</h2>

    <div style="margin-bottom: 15px;">
        <a href="crear.php" class="btn-nuevo">+ Agregar nuevo vehículo</a>
        <a href="../viajes/listar.php" class="btn-volver">← Volver a mis viajes</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Color</th>
                <th>Capacidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($fila['foto'])): ?>
                                <img src="../../<?php echo htmlspecialchars($fila['foto']); ?>" alt="Foto del vehículo" width="80">
                            <?php else: ?>
                                <span>Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($fila['placa']); ?></td>
                        <td><?php echo htmlspecialchars($fila['marca']); ?></td>
                        <td><?php echo htmlspecialchars($fila['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['anio']); ?></td>
                        <td><?php echo htmlspecialchars($fila['color']); ?></td>
                        <td><?php echo htmlspecialchars($fila['capacidad']); ?></td>
                        <td class="acciones">
                            <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                            <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que desea eliminar este vehículo?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No tiene vehículos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
