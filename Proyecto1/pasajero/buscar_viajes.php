<?php
session_start();
include("../config/conexion.php");
include("../includes/autenticar.php");

// Verificar acceso solo para pasajeros
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'pasajero') {
    header("Location: ../inicio_sesion.php");
    exit();
}

// Captura de filtros
$lugar_salida = trim($_GET['salida'] ?? '');
$lugar_llegada = trim($_GET['llegada'] ?? '');
$fecha = trim($_GET['fecha'] ?? '');

// Construcción de consulta base 
$sql = "SELECT v.*, u.nombre AS nombre_chofer, u.apellido AS apellido_chofer,
               veh.marca, veh.modelo, veh.anio, veh.placa, veh.foto
        FROM viajes v
        INNER JOIN usuarios u ON v.id_chofer = u.id
        INNER JOIN vehiculos veh ON v.id_vehiculo = veh.id
        WHERE v.espacios_disponibles > 0
          AND u.estado = 'ACTIVA'";

$condiciones = [];
$parametros = [];
$tipos = "";

// Filtro: lugar de salida
if ($lugar_salida !== '') {
    $condiciones[] = "v.lugar_salida LIKE ?";
    $parametros[] = "%$lugar_salida%";
    $tipos .= "s";
}

// Filtro: lugar de llegada
if ($lugar_llegada !== '') {
    $condiciones[] = "v.lugar_llegada LIKE ?";
    $parametros[] = "%$lugar_llegada%";
    $tipos .= "s";
}

// Filtro: fecha específica
if ($fecha !== '') {
    $condiciones[] = "DATE(v.fecha_hora) = ?";
    $parametros[] = $fecha;
    $tipos .= "s";
}

if (!empty($condiciones)) {
    $sql .= " AND " . implode(" AND ", $condiciones);
}

$sql .= " ORDER BY v.fecha_hora ASC";

$stmt = $conexion->prepare($sql);
if (!empty($parametros)) {
    $stmt->bind_param($tipos, ...$parametros);
}
if (!$stmt->execute()) {
    die("Error en consulta: " . $stmt->error);
}
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Aventones - Aventones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.4;
            margin: 0;
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
        }

        main { flex: 1; padding: 30px 40px; }
        h2 { color: #333; margin-bottom: 20px; }

        form {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input[type="text"], input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1;
            min-width: 150px;
        }

        button, .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 9px 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.95em;
        }

        button:hover, .btn:hover { background: #0056b3; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 11px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }

        .vehiculo-foto {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .acciones { display: flex; gap: 10px; }

        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
            font-size: 0.9em;
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

        .panel-botones {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-ver-reservas {
            background: #17a2b8;
        }

        .btn-ver-reservas:hover {
            background: #138496;
        }
    </style>
</head>
<body>

<header>
    <img src="../logo/logo.png" alt="Logo Aventones">
    <a href="../cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
</header>

<main>
    <h2>🚗 Buscar Aventones Disponibles</h2>

    <form method="GET" action="">
        <input type="text" name="salida" placeholder="Lugar de salida" value="<?php echo htmlspecialchars($lugar_salida); ?>">
        <input type="text" name="llegada" placeholder="Lugar de llegada" value="<?php echo htmlspecialchars($lugar_llegada); ?>">
        <input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
        <button type="submit">Buscar</button>
        <a href="buscar_viajes.php" class="btn" style="background:#6c757d;">Limpiar</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Foto Vehículo</th>
                <th>Título</th>
                <th>Chofer</th>
                <th>Salida</th>
                <th>Llegada</th>
                <th>Fecha/Hora</th>
                <th>Vehículo</th>
                <th>Costo</th>
                <th>Espacios</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($viaje = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($viaje['foto']) && file_exists("../" . $viaje['foto'])): ?>
                                <img src="../<?php echo htmlspecialchars($viaje['foto']); ?>" alt="Vehículo" class="vehiculo-foto">
                            <?php else: ?>
                                <span style="color:#999;">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($viaje['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($viaje['nombre_chofer'] . " " . $viaje['apellido_chofer']); ?></td>
                        <td><?php echo htmlspecialchars($viaje['lugar_salida']); ?></td>
                        <td><?php echo htmlspecialchars($viaje['lugar_llegada']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($viaje['fecha_hora'])); ?></td>
                        <td><?php echo htmlspecialchars($viaje['marca'] . " " . $viaje['modelo'] . " (" . $viaje['anio'] . ")"); ?></td>
                        <td>₡<?php echo number_format($viaje['costo_por_espacio'], 2); ?></td>
                        <td><?php echo htmlspecialchars($viaje['espacios_disponibles']); ?></td>
                        <td class="acciones">
                            <a href="reservar.php?id=<?php echo $viaje['id']; ?>" class="btn">Reservar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align:center;">No hay viajes disponibles según su búsqueda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="panel-botones">
        <a href="mis_reservas.php" class="btn btn-ver-reservas">📋 Ver Mis Reservas</a>
    </div>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
