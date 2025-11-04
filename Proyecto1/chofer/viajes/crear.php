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
$errores = [];
$exito = "";

$titulo = $lugar_salida = $lugar_llegada = $fecha_hora = $costo_por_espacio = $espacios_totales = $vehiculo_id = "";

// Obtener vehículos del chofer
$sqlVehiculos = "SELECT id, placa, marca, modelo FROM vehiculos WHERE id_usuario = ?";
$stmtVeh = $conexion->prepare($sqlVehiculos);
$stmtVeh->bind_param("i", $id_chofer);
$stmtVeh->execute();
$vehiculos = $stmtVeh->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $vehiculo_id = $_POST['vehiculo_id'];
    $lugar_salida = trim($_POST['lugar_salida']);
    $lugar_llegada = trim($_POST['lugar_llegada']);
    $fecha_hora = $_POST['fecha_hora'];
    $costo_por_espacio = trim($_POST['costo_por_espacio']);
    $espacios_totales = trim($_POST['espacios_totales']);

    if ($titulo == "") $errores['titulo'] = "Debe ingresar un título para el viaje.";
    if ($vehiculo_id == "") $errores['vehiculo_id'] = "Debe seleccionar un vehículo.";
    if ($lugar_salida == "") $errores['lugar_salida'] = "Debe indicar el lugar de salida.";
    if ($lugar_llegada == "") $errores['lugar_llegada'] = "Debe indicar el lugar de llegada.";
    if ($fecha_hora == "") $errores['fecha_hora'] = "Debe seleccionar una fecha y hora válidas.";
    if ($costo_por_espacio == "" || !is_numeric($costo_por_espacio) || $costo_por_espacio <= 0)
        $errores['costo_por_espacio'] = "Debe ingresar un costo válido.";
    if ($espacios_totales == "" || !is_numeric($espacios_totales) || $espacios_totales < 1)
        $errores['espacios_totales'] = "Debe indicar al menos 1 espacio.";

    if (empty($errores)) {
        $sql = "INSERT INTO viajes (id_chofer, id_vehiculo, titulo, lugar_salida, lugar_llegada, fecha_hora, costo_por_espacio, espacios_totales, espacios_disponibles)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iissssddd", $id_chofer, $vehiculo_id, $titulo, $lugar_salida, $lugar_llegada, $fecha_hora, $costo_por_espacio, $espacios_totales, $espacios_totales);

        if ($stmt->execute()) {
            $exito = "✅ Viaje creado correctamente.";
            $titulo = $lugar_salida = $lugar_llegada = $fecha_hora = $costo_por_espacio = $espacios_totales = $vehiculo_id = "";
        } else {
            $errores['general'] = "❌ Error al registrar el viaje. Intente nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Viaje - Aventones</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            border-radius: 0;
        }

        main {
            flex: 1;
            padding: 30px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            max-width: 450px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 7px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        button:hover { background: #218838; }

        .mensaje {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            max-width: 450px;
        }

        .exito {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .campo-error {
            border: 1px solid #e74c3c;
            background-color: #fdecea;
        }

        .texto-error {
            color: #e74c3c;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

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
    <script>
        function limpiarError(idCampo, idError) {
            document.getElementById(idCampo).classList.remove('campo-error');
            const error = document.getElementById(idError);
            if (error) error.style.display = 'none';
        }
    </script>
</head>
<body>

<header>
    <img src="../../logo/logo.png" alt="Logo Aventones">
    <a href="listar.php" class="btn-volver">← Volver a mis viajes</a>
</header>

<main>
    <h2>Crear nuevo viaje</h2>

    <?php if ($exito): ?>
        <div class="mensaje exito"><?php echo $exito; ?></div>
    <?php endif; ?>

    <?php if (isset($errores['general'])): ?>
        <div class="mensaje error"><?php echo $errores['general']; ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <label for="titulo">Título del viaje:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>"
               class="<?php echo isset($errores['titulo']) ? 'campo-error' : ''; ?>"
               oninput="limpiarError('titulo','err_titulo')">
        <?php if (isset($errores['titulo'])) echo "<div id='err_titulo' class='texto-error'>{$errores['titulo']}</div>"; ?>

        <label for="vehiculo_id">Vehículo:</label>
        <select id="vehiculo_id" name="vehiculo_id" class="<?php echo isset($errores['vehiculo_id']) ? 'campo-error' : ''; ?>"
                oninput="limpiarError('vehiculo_id','err_vehiculo')">
            <option value="">-- Seleccione un vehículo --</option>
            <?php while ($v = $vehiculos->fetch_assoc()): ?>
                <option value="<?php echo $v['id']; ?>" <?php echo ($vehiculo_id == $v['id']) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($v['placa'] . " - " . $v['marca'] . " " . $v['modelo']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php if (isset($errores['vehiculo_id'])) echo "<div id='err_vehiculo' class='texto-error'>{$errores['vehiculo_id']}</div>"; ?>

        <label for="lugar_salida">Lugar de salida:</label>
        <input type="text" id="lugar_salida" name="lugar_salida" value="<?php echo htmlspecialchars($lugar_salida); ?>"
               class="<?php echo isset($errores['lugar_salida']) ? 'campo-error' : ''; ?>"
               oninput="limpiarError('lugar_salida','err_salida')">
        <?php if (isset($errores['lugar_salida'])) echo "<div id='err_salida' class='texto-error'>{$errores['lugar_salida']}</div>"; ?>

        <label for="lugar_llegada">Lugar de llegada:</label>
        <input type="text" id="lugar_llegada" name="lugar_llegada" value="<?php echo htmlspecialchars($lugar_llegada); ?>"
               class="<?php echo isset($errores['lugar_llegada']) ? 'campo-error' : ''; ?>"
               oninput="limpiarError('lugar_llegada','err_llegada')">
        <?php if (isset($errores['lugar_llegada'])) echo "<div id='err_llegada' class='texto-error'>{$errores['lugar_llegada']}</div>"; ?>

        <label for="fecha_hora">Fecha y hora:</label>
        <input type="datetime-local" id="fecha_hora" name="fecha_hora" value="<?php echo htmlspecialchars($fecha_hora); ?>"
               class="<?php echo isset($errores['fecha_hora']) ? 'campo-error' : ''; ?>"
               oninput="limpiarError('fecha_hora','err_fecha')">
        <?php if (isset($errores['fecha_hora'])) echo "<div id='err_fecha' class='texto-error'>{$errores['fecha_hora']}</div>"; ?>

        <label for="costo_por_espacio">Costo por espacio (₡):</label>
        <input type="number" step="0.01" id="costo_por_espacio" name="costo_por_espacio" value="<?php echo htmlspecialchars($costo_por_espacio); ?>"
               class="<?php echo isset($errores['costo_por_espacio']) ? 'campo-error' : ''; ?>"
               oninput="limpiarError('costo_por_espacio','err_costo')">
        <?php if (isset($errores['costo_por_espacio'])) echo "<div id='err_costo' class='texto-error'>{$errores['costo_por_espacio']}</div>"; ?>

        <label for="espacios_totales">Cantidad de espacios:</label>
        <input type="number" id="espacios_totales" name="espacios_totales" value="<?php echo htmlspecialchars($espacios_totales); ?>"
               class="<?php echo isset($errores['espacios_totales']) ? 'campo-error' : ''; ?>"
               oninput="limpiarError('espacios_totales','err_espacios')">
        <?php if (isset($errores['espacios_totales'])) echo "<div id='err_espacios' class='texto-error'>{$errores['espacios_totales']}</div>"; ?>

        <button type="submit">Guardar viaje</button>
    </form>

    <br>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
