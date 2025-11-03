<?php
include("config/conexion.php");

$mensaje = "";
$tipo = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar usuario con ese token
    $sql = "SELECT id, estado FROM usuarios WHERE token_activacion = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if ($usuario['estado'] === "ACTIVA") {
            $mensaje = "Esta cuenta ya fue activada previamente.";
            $tipo = "info";
        } else {
            // Actualizar estado a ACTIVA
            $actualizar = $conexion->prepare("UPDATE usuarios SET estado = 'ACTIVA', token_activacion = NULL WHERE token_activacion = ?");
            $actualizar->bind_param("s", $token);
            if ($actualizar->execute()) {
                $mensaje = "✅ Su cuenta ha sido activada correctamente. ¡Ya puede iniciar sesión!";
                $tipo = "exito";
            } else {
                $mensaje = "⚠️ Ocurrió un error al activar su cuenta. Intente más tarde.";
                $tipo = "error";
            }
        }
    } else {
        $mensaje = "❌ Token inválido o cuenta ya activada.";
        $tipo = "error";
    }
} else {
    $mensaje = "Token no proporcionado.";
    $tipo = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Activación de cuenta - Aventones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
            background: #f9f9f9;
        }
        .mensaje {
            display: inline-block;
            padding: 20px 30px;
            border-radius: 10px;
            font-size: 1.2em;
            border: 1px solid #ccc;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .exito { color: #155724; border-color: #c3e6cb; background: #d4edda; }
        .error { color: #721c24; border-color: #f5c6cb; background: #f8d7da; }
        .info { color: #0c5460; border-color: #bee5eb; background: #d1ecf1; }
        a {
            display: block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="mensaje <?php echo $tipo; ?>">
        <?php echo $mensaje; ?>
    </div>

    <a href="inicio_sesion.php">Ir al inicio de sesión</a>
</body>
</html>
