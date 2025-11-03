<?php
// Verifica que haya sesión activa
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    header("Location: /Proyecto1/inicio_sesion.php");
    exit;
}

// Opcional: verificación por rol (puedes activarla según el módulo)
function verificarRol($rolEsperado) {
    if ($_SESSION['rol'] !== $rolEsperado) {
        header("Location: /Proyecto1/inicio_sesion.php");
        exit;
    }
}
?>
