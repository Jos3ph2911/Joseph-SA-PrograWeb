<?php
// Verificar si los campos están vacíos
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=empty");
    exit();
}

// Guardar valores
$username = htmlspecialchars($_POST['username']);
$password = $_POST['password'];


// Mensaje de bienvenida quemado
echo "<h2>Bienvenido, " . $username . "!</h2>";
echo "<p>Has iniciado sesión correctamente.</p>";
?>
