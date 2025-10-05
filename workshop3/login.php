<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
</head>
<body>

<h2>Iniciar Sesión</h2>

<form action="verificar.php" method="post">

    <label>Usuario:</label><br>
    <input type="text" name="username" value="<?php echo isset($_GET['user']) ? $_GET['user'] : ''; ?>" required>
    <br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Entrar</button>

</form>

<?php
if (isset($_GET['error']) && $_GET['error'] == 'empty') {
    echo "<p style='color:red;'>Debe llenar todos los campos.</p>";
}
?>

</body>
</html>
