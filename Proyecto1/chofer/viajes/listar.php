<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo chofer
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

$id_chofer = $_SESSION['id'];

// Contador de reservas pendientes
$sqlPendientes = "
    SELECT COUNT(*) AS total 
    FROM reservas r
    INNER JOIN viajes v ON r.id_viaje = v.id
    WHERE v.id_chofer = ? AND r.estado = 'PENDIENTE'
";
$stmtPend = $conexion->prepare($sqlPendientes);
$stmtPend->bind_param("i", $id_chofer);
$stmtPend->execute();
$resPend = $stmtPend->get_result()->fetch_assoc();
$pendientes = $resPend['total'] ?? 0;

// Obtener viajes
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
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        header img {
            height: 180px;
            width: auto;
            object-fit: contain;
        }

        main {
            flex: 1;
            margin: 30px 40px;
        }

        h2 {
            color: #333;
            margin-bottom: 5px;
        }

        h3 {
            color: #555;
            margin-top: 0;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            color: white;
            text-align: center;
            transition: background 0.2s;
            vertical-align: middle;
        }

        .btn-crear { background-color: #28a745; }
        .btn-crear:hover { background-color: #218838; }

        .btn-secundario { background-color: #6c757d; }
        .btn-secundario:hover { background-color: #5a6268; }

        .btn-azul { background-color: #007bff; position: relative; }
        .btn-azul:hover { background-color: #0056b3; }

        .btn-cerrar { background-color: #dc3545; }
        .btn-cerrar:hover { background-color: #c82333; }

        .acciones a {
            margin-right: 8px;
            text-decoration: none;
            color: #007bff;
        }
        .acciones a:hover {
            text-decoration: underline;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: red;
            color: white;
            border-radius: 50%;
            font-size: 0.8em;
            font-weight: bold;
            padding: 2px 6px;
            min-width: 18px;
            text-align: center;
        }

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
        <a href="../../cerrar_sesion.php" class="btn btn-cerrar">Cerrar sesión</a>
    </header>

    <main>
        <div class="top-bar">
            <div>
                <h2>Mis Viajes</h2>
                <h3>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 👋</h3>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <a href="crear.php" class="btn btn-crear">+ Crear nuevo viaje</a>
            <a href="../vehiculos/listar.php" class="btn btn-secundario">🚗 Ver mis vehículos</a>
            <a href="../reservas/reservas_recibidas.php" class="btn btn-azul">
                📋 Ver reservas recibidas
                <?php if ($pendientes > 0): ?>
                    <span class="badge"><?php echo $pendientes; ?></span>
                <?php endif; ?>
            </a>
        </div>

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
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($fila['fecha_hora']))); ?></td>
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
    </main>

    <footer>
        © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
    </footer>

</body>
</html>
