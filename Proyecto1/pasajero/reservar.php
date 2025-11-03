<?php
session_start();
include("../config/conexion.php");
include("../includes/autenticar.php");

// Solo pasajero
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'pasajero') {
    header("Location: ../inicio_sesion.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: buscar_viajes.php");
    exit();
}

$id_viaje = intval($_GET['id']);
$id_pasajero = $_SESSION['id'];
$mensaje = "";
$exito = false;

// 🔹 Verificar si el viaje existe y si el chofer está ACTIVO
$verificarViaje = $conexion->prepare("
    SELECT v.*, u.estado AS estado_chofer
    FROM viajes v
    INNER JOIN usuarios u ON v.id_chofer = u.id
    WHERE v.id = ?
");
$verificarViaje->bind_param("i", $id_viaje);
$verificarViaje->execute();
$viaje = $verificarViaje->get_result()->fetch_assoc();

if (!$viaje) {
    $mensaje = "❌ El viaje no existe o ha sido eliminado.";
} elseif ($viaje['estado_chofer'] !== 'ACTIVA') {
    $mensaje = "⚠️ Este viaje no está disponible porque el chofer ha sido desactivado.";
} else {
    // 🔹 Verificar si ya existe una reserva activa
    $verificar = $conexion->prepare("
        SELECT * FROM reservas 
        WHERE id_viaje = ? 
          AND id_pasajero = ? 
          AND estado IN ('PENDIENTE', 'ACEPTADA')
    ");
    $verificar->bind_param("ii", $id_viaje, $id_pasajero);
    $verificar->execute();
    $reservaExistente = $verificar->get_result();

    if ($reservaExistente->num_rows > 0) {
        $mensaje = "⚠️ Ya tiene una reserva activa para este viaje.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {

        // 🔹 Verificar espacios disponibles y chofer activo antes de reservar
        $consulta = $conexion->prepare("
            SELECT v.*, u.estado AS estado_chofer 
            FROM viajes v
            INNER JOIN usuarios u ON v.id_chofer = u.id
            WHERE v.id = ? AND v.espacios_disponibles > 0 AND u.estado = 'ACTIVA'
        ");
        $consulta->bind_param("i", $id_viaje);
        $consulta->execute();
        $viajeValido = $consulta->get_result()->fetch_assoc();

        if (!$viajeValido) {
            $mensaje = "❌ No hay espacios disponibles o el chofer fue desactivado.";
        } else {
            // 🔹 Registrar la reserva
            $insert = $conexion->prepare("INSERT INTO reservas (id_viaje, id_pasajero) VALUES (?, ?)");
            $insert->bind_param("ii", $id_viaje, $id_pasajero);
            $insert->execute();

            // 🔹 Reducir espacio disponible
            $update = $conexion->prepare("UPDATE viajes SET espacios_disponibles = espacios_disponibles - 1 WHERE id = ?");
            $update->bind_param("i", $id_viaje);
            $update->execute();

            $mensaje = "✅ Reserva realizada correctamente. Pendiente de aprobación del chofer.";
            $exito = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 40px; text-align:center; }
        .contenedor { background:white; padding:25px; border-radius:10px; max-width:480px; margin:auto; box-shadow:0 0 8px rgba(0,0,0,0.1); }
        h2 { color:#007bff; }
        p { font-size:1.1em; margin:10px 0; }
        .btn { display:inline-block; padding:10px 20px; border-radius:6px; background:#007bff; color:white; text-decoration:none; margin-top:15px; cursor:pointer; }
        .btn:hover { background:#0056b3; }
        form { display:inline; }
    </style>
    <script>
        function confirmarReserva() {
            return confirm("¿Está seguro de que desea realizar esta reserva?");
        }

        // Redirigir automáticamente tras reservar con éxito
        <?php if ($exito): ?>
        setTimeout(() => {
            window.location.href = "mis_reservas.php";
        }, 3000);
        <?php endif; ?>
    </script>
</head>
<body>

<div class="contenedor">
    <h2>Reserva de viaje</h2>

    <?php if ($mensaje): ?>
        <p><?php echo $mensaje; ?></p>
        <?php if (!$exito): ?>
            <a href="buscar_viajes.php" class="btn">← Volver a buscar viajes</a>
        <?php else: ?>
            <p style="font-size:0.9em; color:gray;">Será redirigido a sus reservas en unos segundos...</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Confirme si desea reservar este viaje.</p>
        <form method="POST" onsubmit="return confirmarReserva();">
            <input type="hidden" name="confirmar" value="1">
            <button type="submit" class="btn">Confirmar Reserva</button>
            <a href="buscar_viajes.php" class="btn" style="background:#6c757d;">Cancelar</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
