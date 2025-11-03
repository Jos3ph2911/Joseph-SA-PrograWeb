<?php
session_start();
include("config/conexion.php");

$errores = [];
$correo = "";

// Si ya hay sesión activa, redirigir al panel correspondiente
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] == 'admin') header("Location: administrador/panel.php");
    elseif ($_SESSION['rol'] == 'chofer') header("Location: chofer/vehiculos/listar.php");
    elseif ($_SESSION['rol'] == 'pasajero') header("Location: pasajero/buscar_viajes.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    if ($correo == "" || $contrasena == "") {
        $errores['general'] = "Debe ingresar su correo y contraseña.";
    } else {
        $sql = "SELECT id, nombre, correo, contrasena, rol, estado FROM usuarios WHERE correo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            if ($usuario['estado'] != "ACTIVA") {
                $errores['general'] = "Su cuenta aún no está activada. Revise su correo.";
            } elseif (password_verify($contrasena, $usuario['contrasena'])) {
                // Crear sesión
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol'];

                // Redirigir según rol
                switch ($usuario['rol']) {
                    case 'admin':
                        header("Location: administrador/panel.php");
                        break;
                    case 'chofer':
                        header("Location: chofer/viajes/listar.php");
                        break;
                    case 'pasajero':
                        header("Location: pasajero/buscar_viajes.php");
                        break;
                }
                exit();
            } else {
                $errores['general'] = "Contraseña incorrecta.";
            }
        } else {
            $errores['general'] = "No existe una cuenta con ese correo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; text-align:center; }
        form { display:inline-block; background:#fff; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 8px; margin: 8px 0; border-radius:5px; border:1px solid #ccc; }
        button { padding:8px 15px; border:none; background:#007bff; color:white; border-radius:5px; cursor:pointer; }
        button:hover { background:#0056b3; }
        .error { color:#c0392b; margin-bottom:10px; font-weight:bold; }
        .enlace { display:block; margin-top:10px; color:#007bff; text-decoration:none; }
        .enlace:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <h2>Inicio de Sesión - Aventones</h2>

    <?php if (isset($errores['general'])): ?>
        <div class="error"><?php echo $errores['general']; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Correo electrónico:</label><br>
        <input type="email" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required><br>

        <label>Contraseña:</label><br>
        <input type="password" name="contrasena" required><br>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <a class="enlace" href="registro.php">¿No tiene cuenta? Regístrese aquí</a>
</body>
</html>
