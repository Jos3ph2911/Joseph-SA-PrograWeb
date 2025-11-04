<?php
session_start();
include("config/conexion.php");

$errores = [];
$correo = "";

if (isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'administrador': header("Location: administrador/panel.php"); break;
        case 'chofer': header("Location: chofer/viajes/listar.php"); break;
        case 'pasajero': header("Location: pasajero/buscar_viajes.php"); break;
    }
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
            $esAdmin = ($usuario['correo'] === 'admin@aventones.com');

            if ($usuario['estado'] != "ACTIVA") {
                $errores['general'] = "Su cuenta aún no está activada. Revise su correo.";
            } elseif (
                (!$esAdmin && password_verify($contrasena, $usuario['contrasena'])) ||
                ($esAdmin && $usuario['contrasena'] === md5($contrasena))
            ) {
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol'];

                switch ($usuario['rol']) {
                    case 'administrador': header("Location: administrador/panel.php"); break;
                    case 'chofer': header("Location: chofer/viajes/listar.php"); break;
                    case 'pasajero': header("Location: pasajero/buscar_viajes.php"); break;
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
    <link rel="stylesheet" href="assets/estilos.css">
    <style>
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
        }
    </style>
</head>
<body>

<header>
    <img src="logo/logo.png" alt="Logo Aventones">
</header>

<main class="contenedor">
    <h2>Inicio de Sesión</h2>

    <?php if (isset($errores['general'])): ?>
        <div class="mensaje-error">
            <?php echo $errores['general']; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="formulario">
        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($correo); ?>" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <button type="submit" class="btn">Iniciar Sesión</button>

        <a href="registro.php" class="enlace-pequeno">¿No tiene cuenta? Regístrese aquí</a>
    </form>

    <a href="indexPrincipal.php" class="btn-secundario btn-pequeno">← Volver al Inicio</a>
</main>

<footer>
    © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
</footer>

</body>
</html>
