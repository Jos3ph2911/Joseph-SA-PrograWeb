<?php

// Mostrar errores en consola
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "🔍 Iniciando script de notificación...\n";

// Inclusiones
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/enviar_correo.php'; // función PHPMailer

$MINUTOS = 1; // Tiempo de referencia
$LOG_PATH = __DIR__ . "\\notificar_reservas.log";

echo "===============================================\n";
echo " Script de notificación de reservas pendientes\n";
echo " Ejecutado: " . date("Y-m-d H:i:s") . "\n";
echo " Tiempo de referencia: $MINUTOS minutos\n";
echo "===============================================\n\n";

// Consulta para encontrar reservas pendientes con más de X minutos
$sql = "
    SELECT 
        r.id AS id_reserva,
        r.fecha_reserva,
        u.correo AS correo_chofer,
        CONCAT(u.nombre, ' ', u.apellido) AS nombre_chofer,
        v.titulo
    FROM reservas r
    INNER JOIN viajes v ON r.id_viaje = v.id
    INNER JOIN usuarios u ON v.id_chofer = u.id
    WHERE r.estado = 'PENDIENTE'
      AND u.estado = 'ACTIVA'
      AND TIMESTAMPDIFF(MINUTE, r.fecha_reserva, NOW()) > ?
";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Error preparando consulta: " . $conexion->error . "\n");
}
$stmt->bind_param("i", $MINUTOS);

if (!$stmt->execute()) {
    die("❌ Error ejecutando consulta: " . $stmt->error . "\n");
}

$resultado = $stmt->get_result();

if (!$resultado) {
    die("❌ Error obteniendo resultados: " . $stmt->error . "\n");
}

echo "🔎 Reservas encontradas: " . $resultado->num_rows . "\n";

if ($resultado->num_rows === 0) {
    echo "✅ No hay reservas pendientes mayores a $MINUTOS minutos.\n";
    exit;
}

// Abrir log
$log = fopen($LOG_PATH, "a");
$contador = 0;

// Recorrer reservas pendientes
while ($fila = $resultado->fetch_assoc()) {
    $correo = $fila['correo_chofer'];
    $nombre = $fila['nombre_chofer'];
    $titulo = $fila['titulo'];
    $fecha = $fila['fecha_reserva'];

    $asunto = "Recordatorio: Reserva pendiente en su viaje '$titulo'";
    $mensaje = "
        <h2>Hola $nombre,</h2>
        <p>Este es un recordatorio automático del sistema <strong>Aventones</strong>.</p>
        <p>Tiene una reserva pendiente desde <strong>$fecha</strong> para el viaje:</p>
        <p><em>$titulo</em></p>
        <p>Por favor, inicie sesión en la plataforma para aceptarla o rechazarla.</p>
        <hr>
        <small>Mensaje automático generado el " . date("d/m/Y H:i") . ".</small>
    ";

    // Enviar correo usando PHPMailer
    $resultadoCorreo = enviarCorreoNotificacion($correo, $nombre, $asunto, $mensaje);

    if ($resultadoCorreo) {
        echo "📧 Notificación enviada a: $correo (viaje: $titulo)\n";
        fwrite($log, "[" . date("Y-m-d H:i:s") . "] Correo enviado a $correo (viaje: $titulo)\n");
        $contador++;
    } else {
        echo "❌ Error al enviar correo a: $correo\n";
        fwrite($log, "[" . date("Y-m-d H:i:s") . "] ERROR al enviar a $correo\n");
    }
}

fclose($log);

echo "\nProceso completado. Total de notificaciones enviadas: $contador\n";
echo "Archivo de log: $LOG_PATH\n";
echo "===============================================\n";
?>
