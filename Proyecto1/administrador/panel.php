<?php
session_start();
include("../config/conexion.php");
include("../includes/autenticar.php");

// Verificar acceso solo para administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../inicio_sesion.php");
    exit();
}

// Contadores generales
$totalUsuarios = $conexion->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];

// Solo choferes activos
$totalChoferes = $conexion->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol='chofer' AND estado='ACTIVA'")->fetch_assoc()['total'];

// Solo pasajeros activos
$totalPasajeros = $conexion->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol='pasajero' AND estado='ACTIVA'")->fetch_assoc()['total'];

// Solo viajes activos (chofer activo)
$totalViajes = $conexion->query("
    SELECT COUNT(*) AS total
    FROM viajes v
    INNER JOIN usuarios u ON v.id_chofer = u.id
    WHERE u.estado = 'ACTIVA'
")->fetch_assoc()['total'];

// Solo reservas de choferes activos
$totalReservas = $conexion->query("
    SELECT COUNT(*) AS total
    FROM reservas r
    INNER JOIN viajes v ON r.id_viaje = v.id
    INNER JOIN usuarios u ON v.id_chofer = u.id
    WHERE u.estado = 'ACTIVA'
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración - Aventones</title>
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: Arial, sans-serif;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        line-height: 1.4; /* evita recortes de letras descendentes */
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

    /* Contenido principal */
    main {
        flex: 1;
        padding: 30px 40px;
    }
    h1 {
        color: #007bff;
        margin-bottom: 5px;
    }
    p {
        color: #333;
        margin-bottom: 25px;
    }

    /* Tarjetas de resumen */
    .contenedor {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
    }
    .tarjeta {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
        text-align: center;
    }
    .tarjeta h3 {
        margin: 10px 0;
        color: #333;
        line-height: 1.4;
    }
    .tarjeta p {
        font-size: 1.6em;
        margin: 5px 0;
        color: #007bff;
        font-weight: bold;
    }

    /* Enlaces de acción */
    .acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .acciones a {
        display: inline-block;
        padding: 10px 20px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        text-align: center;
        transition: background 0.2s;
    }
    .acciones a:hover { background: #0056b3; }

    /* Pie de página */
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
    <img src="../logo/logo.png" alt="Logo Aventones">
    <a href="../cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
</header>

<main>
    <h1> Panel de Administración</h1>
    <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>

    <!-- Tarjetas informativas -->
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

    <!-- Acciones principales -->
    <div class="acciones">
        <a href="usuarios/listar.php">👥 Gestionar Usuarios</a>
        <a href="viajes/listar.php">🛣️ Ver Viajes</a>
        <a href="reservas/listar.php">📋 Ver Reservas</a>
    </div>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
