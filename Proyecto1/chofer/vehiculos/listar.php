<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Verificar si el usuario tiene rol de chofer
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

$id_chofer = $_SESSION['id'];

// Consultar vehículos del chofer actual
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
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        .acciones a {
            margin-right: 8px;
        }
        .btn-nuevo {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-nuevo:hover {
            background-color: #218838;
        }
        .btn-salir {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .btn-salir:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <h2>🚗 Mis Vehículos</h2>

    <!-- Botones superiores -->
    <div style="margin-bottom: 15px;">
        <a href="../../cerrar_sesion.php" class="btn-salir">Cerrar sesión</a>
        <a href="crear.php" class="btn-nuevo">+ Registrar nuevo vehículo</a>
    </div>

    <!-- Tabla de vehículos -->
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Color</th>
                <th>Capacidad</th>
                <th>Foto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['placa']); ?></td>
                        <td><?php echo htmlspecialchars($fila['marca']); ?></td>
                        <td><?php echo htmlspecialchars($fila['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['anio']); ?></td>
                        <td><?php echo htmlspecialchars($fila['color']); ?></td>
                        <td><?php echo htmlspecialchars($fila['capacidad']); ?></td>
                        <td>
                            <?php if (!empty($fila['foto'])): ?>
                                <img src="../../<?php echo $fila['foto']; ?>" width="80" alt="Foto vehículo">
                            <?php else: ?>
                                Sin foto
                            <?php endif; ?>
                        </td>
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
