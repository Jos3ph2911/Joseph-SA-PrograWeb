<?php
include("config/conexion.php");

// Variables para filtros
$lugar_salida = $_GET['salida'] ?? '';
$lugar_llegada = $_GET['llegada'] ?? '';
$orden = $_GET['orden'] ?? 'fecha_asc';

// Consulta base: solo viajes futuros con espacios disponibles y chofer activo
$sql = "SELECT v.titulo, v.lugar_salida, v.lugar_llegada, v.fecha_hora, 
               v.costo_por_espacio, v.espacios_disponibles,
               veh.marca, veh.modelo, veh.anio
        FROM viajes v
        INNER JOIN vehiculos veh ON v.id_vehiculo = veh.id
        INNER JOIN usuarios u ON v.id_chofer = u.id
        WHERE v.fecha_hora >= NOW() 
        AND v.espacios_disponibles > 0
        AND u.estado = 'ACTIVA'";

// Aplicar filtros si existen
$parametros = [];
$tipos = "";

if ($lugar_salida != '') {
    $sql .= " AND v.lugar_salida LIKE ?";
    $param_salida = "%$lugar_salida%";
    $parametros[] = &$param_salida;
    $tipos .= "s";
}
if ($lugar_llegada != '') {
    $sql .= " AND v.lugar_llegada LIKE ?";
    $param_llegada = "%$lugar_llegada%";
    $parametros[] = &$param_llegada;
    $tipos .= "s";
}

// Ordenar resultados
switch ($orden) {
    case "fecha_desc": $sql .= " ORDER BY v.fecha_hora DESC"; break;
    case "salida_asc": $sql .= " ORDER BY v.lugar_salida ASC"; break;
    case "salida_desc": $sql .= " ORDER BY v.lugar_salida DESC"; break;
    case "llegada_asc": $sql .= " ORDER BY v.lugar_llegada ASC"; break;
    case "llegada_desc": $sql .= " ORDER BY v.lugar_llegada DESC"; break;
    default: $sql .= " ORDER BY v.fecha_hora ASC"; break;
}

$stmt = $conexion->prepare($sql);

if (!empty($parametros)) {
    array_unshift($parametros, $tipos);
    call_user_func_array([$stmt, 'bind_param'], $parametros);
}

$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Aventones - Plataforma de Rides</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0; padding: 0;
        background: #f4f6f9;
        color: #333;
    }
    header {
        background: #007bff;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header h1 {
        margin: 0;
        font-size: 1.8em;
        letter-spacing: 1px;
    }
    nav a {
        color: white;
        margin-left: 15px;
        text-decoration: none;
        background: rgba(255,255,255,0.2);
        padding: 6px 12px;
        border-radius: 5px;
    }
    nav a:hover {
        background: rgba(255,255,255,0.35);
    }
    main {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #007bff;
        margin-bottom: 20px;
    }
    form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    input, select, button, a.limpiar {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    input[type="text"] {
        flex: 1;
        min-width: 180px;
    }
    select {
        min-width: 150px;
    }
    button {
        background: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background: #0056b3;
    }
    a.limpiar {
        background: #6c757d;
        color: white;
        text-decoration: none;
        display: inline-block;
    }
    a.limpiar:hover {
        background: #5a6268;
    }
    table {
        width: 100%;
        border-collapse: collapse;
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
    tr:hover {
        background: #f1f1f1;
    }
    footer {
        background: #007bff;
        color: white;
        text-align: center;
        padding: 10px;
        margin-top: 40px;
    }
</style>
</head>
<body>

<header>
    <h1>🚗 Aventones</h1>
    <nav>
        <a href="inicio_sesion.php">Iniciar Sesión</a>
        <a href="registro.php">Registrarse</a>
    </nav>
</header>

<main>
    <h2>Buscar Aventones Públicos</h2>
    <form method="GET" action="">
        <input type="text" name="salida" placeholder="Lugar de salida" value="<?php echo htmlspecialchars($lugar_salida); ?>">
        <input type="text" name="llegada" placeholder="Lugar de llegada" value="<?php echo htmlspecialchars($lugar_llegada); ?>">
        <select name="orden">
            <option value="fecha_asc" <?php if($orden=="fecha_asc") echo "selected"; ?>>Fecha (más recientes primero)</option>
            <option value="fecha_desc" <?php if($orden=="fecha_desc") echo "selected"; ?>>Fecha (más antiguos primero)</option>
            <option value="salida_asc" <?php if($orden=="salida_asc") echo "selected"; ?>>Lugar de salida (A-Z)</option>
            <option value="salida_desc" <?php if($orden=="salida_desc") echo "selected"; ?>>Lugar de salida (Z-A)</option>
            <option value="llegada_asc" <?php if($orden=="llegada_asc") echo "selected"; ?>>Lugar de llegada (A-Z)</option>
            <option value="llegada_desc" <?php if($orden=="llegada_desc") echo "selected"; ?>>Lugar de llegada (Z-A)</option>
        </select>
        <button type="submit">Buscar</button>
        <a href="indexPrincipal.php" class="limpiar">Limpiar</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Salida</th>
                <th>Llegada</th>
                <th>Fecha/Hora</th>
                <th>Vehículo</th>
                <th>Costo</th>
                <th>Asientos</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($viaje = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($viaje['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($viaje['lugar_salida']); ?></td>
                    <td><?php echo htmlspecialchars($viaje['lugar_llegada']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($viaje['fecha_hora'])); ?></td>
                    <td><?php echo htmlspecialchars("{$viaje['marca']} {$viaje['modelo']} ({$viaje['anio']})"); ?></td>
                    <td>₡<?php echo number_format($viaje['costo_por_espacio'], 2); ?></td>
                    <td><?php echo htmlspecialchars($viaje['espacios_disponibles']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">No se encontraron viajes disponibles.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
