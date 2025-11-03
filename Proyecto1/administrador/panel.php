<?php
session_start();
include("../config/conexion.php");
include("../includes/autenticar.php");

// Solo administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../inicio_sesion.php");
    exit();
}

// Contadores rápidos
$totalUsuarios = $conexion->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalChoferes = $conexion->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol='chofer'")->fetch_assoc()['total'];
$totalPasajeros = $conexion->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol='pasajero'")->fetch_assoc()['total'];
$totalViajes = $conexion->query("SELECT COUNT(*) AS total FROM viajes")->fetch_assoc()['total'];
$totalReservas = $conexion->query("SELECT COUNT(*) AS total FROM reservas")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración - Aventones</title>
<style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 40px; }
    h1 { color: #007bff; }
    .contenedor { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 25px; }
    .tarjeta {
        background: white; padding: 20px; border-radius: 10px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1); text-align: center;
    }
    .tarjeta h3 { margin: 10px 0; color: #333; }
    .tarjeta p { font-size: 1.6em; margin: 5px 0; color: #007bff; font-weight: bold; }
    .acciones { margin-top: 30px; }
    .acciones a {
        display: inline-block; margin: 10px; padding: 10px 20px;
        background: #007bff; color: white; text-decoration: none;
        border-radius: 6px; font-weight: bold;
    }
    .acciones a:hover { background: #0056b3; }
    .cerrar { background: #dc3545 !important; }
    .cerrar:hover { background: #b52b38 !important; }
</style>
</head>
<body>

<h1>👨‍💼 Panel de Administración</h1>
<p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>

<div class="contenedor">
    <div class="tarjeta">
        <h3>Usuarios registrados</h3>
        <p><?php echo $totalUsuarios; ?></p>
    </div>
    <div class="tarjeta">
        <h3>Choferes</h3>
        <p><?php echo $totalChoferes; ?></p>
    </div>
    <div class="tarjeta">
        <h3>Pasajeros</h3>
        <p><?php echo $totalPasajeros; ?></p>
    </div>
    <div class="tarjeta">
        <h3>Viajes activos</h3>
        <p><?php echo $totalViajes; ?></p>
    </div>
    <div class="tarjeta">
        <h3>Reservas totales</h3>
        <p><?php echo $totalReservas; ?></p>
    </div>
</div>

<div class="acciones">
    <a href="usuarios/listar.php">👥 Gestionar Usuarios</a>
    <a href="viajes/listar.php">🛣️ Ver Viajes</a>
    <a href="reservas/listar.php">📋 Ver Reservas</a>
    <a href="../cerrar_sesion.php" class="cerrar">🔒 Cerrar Sesión</a>
</div>

</body>
</html>
