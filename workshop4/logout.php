<?php
//Funcion para que la sesion no quede abierta
session_start();
session_destroy();
header("Location: login.php");
?>
