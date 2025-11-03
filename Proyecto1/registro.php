<?php
include("config/conexion.php");
include("config/correo.php");

// Determinar origen (login o index principal)
$origen = $_GET['origen'] ?? 'index';

$errores = [];
$exito = "";
$nombre = $apellido = $cedula = $fecha = $correo = $telefono = $rol = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']);
    $fecha = $_POST['fecha_nacimiento'];
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // --- VALIDACIONES ---
    if ($nombre == "") $errores['nombre'] = "Debe ingresar su nombre.";
    if ($apellido == "") $errores['apellido'] = "Debe ingresar su apellido.";
    if ($cedula == "") $errores['cedula'] = "Debe ingresar su número de cédula.";
    if ($fecha == "") $errores['fecha'] = "Debe seleccionar su fecha de nacimiento.";
    if ($correo == "") $errores['correo'] = "Debe ingresar su correo electrónico.";
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores['correo'] = "Formato de correo no válido.";
    if ($contrasena == "") $errores['contrasena'] = "Debe ingresar una contraseña.";
    elseif (strlen($contrasena) < 6) $errores['contrasena'] = "La contraseña debe tener al menos 6 caracteres.";
    if ($rol == "") $errores['rol'] = "Debe seleccionar un rol.";

    if ($telefono == "") {
        $errores['telefono'] = "Debe ingresar su teléfono.";
    } elseif (!preg_match('/^[0-9+\-\s]{6,20}$/', $telefono)) {
        $errores['telefono'] = "Teléfono inválido. Use solo números, espacios, + o - (6-20 caracteres).";
    }

    // Verificar duplicados
    $verificar = $conexion->prepare("SELECT correo, cedula FROM usuarios WHERE correo = ? OR cedula = ?");
    $verificar->bind_param("ss", $correo, $cedula);
    $verificar->execute();
    $resultado = $verificar->get_result();
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            if ($fila['correo'] == $correo) $errores['correo'] = "Este correo ya está registrado.";
            if ($fila['cedula'] == $cedula) $errores['cedula'] = "Esta cédula ya está registrada.";
        }
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] != 0) {
        $errores['foto'] = "Debe subir una fotografía.";
    }

    if (empty($errores)) {
        $carpetaDestino = __DIR__ . "/subidas/fotos_usuarios/";
        if (!is_dir($carpetaDestino)) mkdir($carpetaDestino, 0777, true);

        $nombreArchivo = uniqid() . "_" . basename($_FILES['foto']['name']);
        $rutaCompleta = $carpetaDestino . $nombreArchivo;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            $errores['foto'] = "Error al subir la imagen. Intente nuevamente.";
        } else {
            $foto = "subidas/fotos_usuarios/" . $nombreArchivo;
            $token = bin2hex(random_bytes(16));
            $hash = password_hash($contrasena, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (nombre, apellido, cedula, fecha_nacimiento, correo, telefono, foto, contrasena, rol, estado, token_activacion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDIENTE', ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssssssss", $nombre, $apellido, $cedula, $fecha, $correo, $telefono, $foto, $hash, $rol, $token);

            if ($stmt->execute()) {
                enviarCorreoActivacion($correo, $nombre, $token);
                $exito = "✅ Registro exitoso. Revise su correo para activar la cuenta.";
                $nombre = $apellido = $cedula = $fecha = $correo = $telefono = $rol = "";
            } else {
                $errores['general'] = "Error al registrar el usuario. Intente más tarde.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background:#f8f9fa; }
        form { max-width: 480px; margin:auto; background:white; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        label { font-weight: bold; display:block; margin-top:8px; }
        input, select { width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box; }
        .mensaje { padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .campo-error { border: 1px solid #e74c3c; background-color: #fdecea; }
        .texto-error { color: #e74c3c; font-size: 0.9em; margin-top:6px; margin-bottom: 6px; }
        .fila { margin-bottom: 6px; }
        button { margin-top:10px; padding:8px 14px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer; }
        button:hover { background:#0056b3; }
        .volver {
            display: inline-block;
            margin-top: 15px;
            background-color: #6c757d;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .volver:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Registro de Usuario</h2>

    <?php if ($exito): ?>
        <div class="mensaje exito"><?php echo $exito; ?></div>
    <?php endif; ?>

    <?php if (isset($errores['general'])): ?>
        <div class="mensaje error"><?php echo $errores['general']; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>

        <div class="fila">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" class="<?php echo isset($errores['nombre']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['nombre'])) echo "<div class='texto-error' id='error-nombre'>{$errores['nombre']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($apellido); ?>" class="<?php echo isset($errores['apellido']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['apellido'])) echo "<div class='texto-error' id='error-apellido'>{$errores['apellido']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="cedula">Cédula:</label>
            <input type="text" name="cedula" id="cedula" value="<?php echo htmlspecialchars($cedula); ?>" class="<?php echo isset($errores['cedula']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['cedula'])) echo "<div class='texto-error' id='error-cedula'>{$errores['cedula']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="fecha">Fecha de nacimiento:</label>
            <input type="date" name="fecha_nacimiento" id="fecha" value="<?php echo htmlspecialchars($fecha); ?>" class="<?php echo isset($errores['fecha']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['fecha'])) echo "<div class='texto-error' id='error-fecha'>{$errores['fecha']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($correo); ?>" class="<?php echo isset($errores['correo']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['correo'])) echo "<div class='texto-error' id='error-correo'>{$errores['correo']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($telefono); ?>" class="<?php echo isset($errores['telefono']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['telefono'])) echo "<div class='texto-error' id='error-telefono'>{$errores['telefono']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" class="<?php echo isset($errores['contrasena']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['contrasena'])) echo "<div class='texto-error' id='error-contrasena'>{$errores['contrasena']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" class="<?php echo isset($errores['rol']) ? 'campo-error' : ''; ?>" required>
                <option value="">-- Seleccione --</option>
                <option value="chofer" <?php echo ($rol == "chofer") ? "selected" : ""; ?>>Chofer</option>
                <option value="pasajero" <?php echo ($rol == "pasajero") ? "selected" : ""; ?>>Pasajero</option>
            </select>
            <?php if (isset($errores['rol'])) echo "<div class='texto-error' id='error-rol'>{$errores['rol']}</div>"; ?>
        </div>

        <div class="fila">
            <label for="foto">Fotografía:</label>
            <input type="file" name="foto" id="foto" accept="image/*" class="<?php echo isset($errores['foto']) ? 'campo-error' : ''; ?>" required>
            <?php if (isset($errores['foto'])) echo "<div class='texto-error' id='error-foto'>{$errores['foto']}</div>"; ?>
        </div>

        <button type="submit">Registrarse</button>

        <!-- Botón de regreso inteligente -->
        <a href="javascript:history.back()" class="volver">⬅ Volver</a>
    </form>

    <script>
        document.querySelectorAll("input, select").forEach(campo => {
            campo.addEventListener("input", () => {
                const errorDiv = document.getElementById("error-" + campo.name);
                if (errorDiv) errorDiv.remove();
                campo.classList.remove("campo-error");
            });
            campo.addEventListener("change", () => {
                const errorDiv = document.getElementById("error-" + campo.name);
                if (errorDiv) errorDiv.remove();
                campo.classList.remove("campo-error");
            });
        });
    </script>
</body>
</html>
