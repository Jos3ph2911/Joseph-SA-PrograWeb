<?php
// Autenticación general según rol
// 1️ Verificar que haya sesión activa
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    header("Location: /Proyecto1/inicio_sesion.php");
    exit;
}

// 2 Detectar en qué módulo se encuentra el usuario
$ruta = $_SERVER['PHP_SELF'];
$rol = $_SESSION['rol'];

// 3️ Verificar acceso según carpeta actual
if (strpos($ruta, '/administrador/') !== false && $rol !== 'administrador') {
    header("Location: /Proyecto1/inicio_sesion.php");
    exit;
}

if (strpos($ruta, '/chofer/') !== false && $rol !== 'chofer') {
    header("Location: /Proyecto1/inicio_sesion.php");
    exit;
}

if (strpos($ruta, '/pasajero/') !== false && $rol !== 'pasajero') {
    header("Location: /Proyecto1/inicio_sesion.php");
    exit;
}

// 4️ Función auxiliar (si deseas validaciones adicionales en archivos específicos)
function verificarRol($rolEsperado) {
    if ($_SESSION['rol'] !== $rolEsperado) {
        header("Location: /Proyecto1/inicio_sesion.php");
        exit;
    }
}
?>
