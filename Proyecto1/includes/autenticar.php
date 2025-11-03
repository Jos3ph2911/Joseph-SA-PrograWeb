<?php
if (!isset($_SESSION['id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit;
}
?>
