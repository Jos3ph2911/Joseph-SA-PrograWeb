<?php
session_start();
include("../../config/conexion.php");
include("../../includes/autenticar.php");

// Solo chofer puede acceder
if ($_SESSION['rol'] !== 'chofer') {
    header("Location: ../../inicio_sesion.php");
    exit();
}

$id_chofer = $_SESSION['id'];
$errores = [];
$exito = "";

// Obtener el ID del vehículo
if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id_vehiculo = intval($_GET['id']);

// Cargar datos actuales
$sql = "SELECT * FROM vehiculos WHERE id = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_vehiculo, $id_chofer);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("❌ No tiene permiso para editar este vehículo.");
}

$vehiculo = $resultado->fetch_assoc();

$placa = $vehiculo['placa'];
$color = $vehiculo['color'];
$marca = $vehiculo['marca'];
$modelo = $vehiculo['modelo'];
$anio = $vehiculo['anio'];
$capacidad = $vehiculo['capacidad'];
$fotoActual = $vehiculo['foto'];

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = trim($_POST['placa']);
    $color = trim($_POST['color']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $anio = trim($_POST['anio']);
    $capacidad = trim($_POST['capacidad']);

    // Validaciones
    if ($placa == "") $errores['placa'] = "Debe ingresar la placa del vehículo.";
    if ($color == "") $errores['color'] = "Debe ingresar el color.";
    if ($marca == "") $errores['marca'] = "Debe ingresar la marca.";
    if ($modelo == "") $errores['modelo'] = "Debe ingresar el modelo.";
    if ($anio == "") $errores['anio'] = "Debe ingresar el año.";
    elseif (!is_numeric($anio) || $anio < 1900 || $anio > date("Y") + 1)
        $errores['anio'] = "El año ingresado no es válido.";
    if ($capacidad == "") $errores['capacidad'] = "Debe ingresar la capacidad.";
    elseif (!is_numeric($capacidad) || $capacidad < 1)
        $errores['capacidad'] = "La capacidad debe ser un número mayor que 0.";

    // Manejo de foto
    $nuevaFoto = false;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $nuevaFoto = true;
    }

    if (empty($errores)) {
        if ($nuevaFoto) {
            $carpetaDestino = __DIR__ . "/../../subidas/fotos_vehiculos/";
            if (!is_dir($carpetaDestino)) mkdir($carpetaDestino, 0777, true);

            $nombreArchivo = uniqid() . "_" . basename($_FILES['foto']['name']);
            $rutaCompleta = $carpetaDestino . $nombreArchivo;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
                $rutaRelativa = "subidas/fotos_vehiculos/" . $nombreArchivo;

                // Eliminar la foto anterior si existe
                if ($fotoActual && file_exists(__DIR__ . "/../../" . $fotoActual)) {
                    unlink(__DIR__ . "/../../" . $fotoActual);
                }

                $fotoActual = $rutaRelativa;
            } else {
                $errores['foto'] = "No se pudo subir la nueva foto.";
            }
        }

        if (empty($errores)) {
            $sqlUpdate = "UPDATE vehiculos 
                          SET placa=?, color=?, marca=?, modelo=?, anio=?, capacidad=?, foto=?
                          WHERE id=? AND id_usuario=?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssssissi", $placa, $color, $marca, $modelo, $anio, $capacidad, $fotoActual, $id_vehiculo, $id_chofer);

            if ($stmtUpdate->execute()) {
                $exito = "✅ Vehículo actualizado correctamente.";
            } else {
                $errores['general'] = "Error al actualizar el vehículo.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vehículo - Aventones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; }
        h2 { color: #333; }
        form { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 480px; }
        label { font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 5px; }
        .campo-error { border: 1px solid #e74c3c; background-color: #fdecea; }
        .texto-error { color: #e74c3c; font-size: 0.9em; margin-bottom: 10px; }
        .mensaje { padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn-guardar { background: #007bff; color: white; padding: 10px 14px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-guardar:hover { background: #0056b3; }
        .btn-volver {
            display: inline-block; background: #6c757d; color: white;
            padding: 8px 12px; border-radius: 5px; text-decoration: none;
            font-weight: bold; margin-top: 10px;
        }
        .btn-volver:hover { background: #5a6268; }
        img { margin-top: 10px; border-radius: 8px; }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("input").forEach(input => {
                input.addEventListener("input", () => {
                    const errorDiv = input.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains("texto-error")) {
                        errorDiv.style.display = "none";
                        input.classList.remove("campo-error");
                    }
                });
            });
        });
    </script>
</head>
<body>
    <h2>✏️ Editar vehículo</h2>

    <?php if ($exito): ?>
        <div class="mensaje exito"><?php echo $exito; ?></div>
    <?php endif; ?>
    <?php if (isset($errores['general'])): ?>
        <div class="mensaje error"><?php echo $errores['general']; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <label>Placa:</label>
        <input type="text" name="placa" value="<?php echo htmlspecialchars($placa); ?>" class="<?php echo isset($errores['placa']) ? 'campo-error' : ''; ?>" required>
        <?php if (isset($errores['placa'])) echo "<div class='texto-error'>{$errores['placa']}</div>"; ?>

        <label>Color:</label>
        <input type="text" name="color" value="<?php echo htmlspecialchars($color); ?>" class="<?php echo isset($errores['color']) ? 'campo-error' : ''; ?>" required>
        <?php if (isset($errores['color'])) echo "<div class='texto-error'>{$errores['color']}</div>"; ?>

        <label>Marca:</label>
        <input type="text" name="marca" value="<?php echo htmlspecialchars($marca); ?>" class="<?php echo isset($errores['marca']) ? 'campo-error' : ''; ?>" required>
        <?php if (isset($errores['marca'])) echo "<div class='texto-error'>{$errores['marca']}</div>"; ?>

        <label>Modelo:</label>
        <input type="text" name="modelo" value="<?php echo htmlspecialchars($modelo); ?>" class="<?php echo isset($errores['modelo']) ? 'campo-error' : ''; ?>" required>
        <?php if (isset($errores['modelo'])) echo "<div class='texto-error'>{$errores['modelo']}</div>"; ?>

        <label>Año:</label>
        <input type="number" name="anio" value="<?php echo htmlspecialchars($anio); ?>" class="<?php echo isset($errores['anio']) ? 'campo-error' : ''; ?>" required>
        <?php if (isset($errores['anio'])) echo "<div class='texto-error'>{$errores['anio']}</div>"; ?>

        <label>Capacidad:</label>
        <input type="number" name="capacidad" value="<?php echo htmlspecialchars($capacidad); ?>" class="<?php echo isset($errores['capacidad']) ? 'campo-error' : ''; ?>" required>
        <?php if (isset($errores['capacidad'])) echo "<div class='texto-error'>{$errores['capacidad']}</div>"; ?>

        <label>Fotografía actual:</label><br>
        <?php if ($fotoActual && file_exists(__DIR__ . "/../../" . $fotoActual)): ?>
            <img src="../../<?php echo $fotoActual; ?>" width="120" alt="Foto actual">
        <?php else: ?>
            <p>No hay foto disponible</p>
        <?php endif; ?>
        <br><br>

        <label>Cambiar foto (opcional):</label>
        <input type="file" name="foto" accept="image/*" class="<?php echo isset($errores['foto']) ? 'campo-error' : ''; ?>">
        <?php if (isset($errores['foto'])) echo "<div class='texto-error'>{$errores['foto']}</div>"; ?>

        <button type="submit" class="btn-guardar">Guardar cambios</button>
    </form>

    <a href="listar.php" class="btn-volver">← Volver a mis vehículos</a>
</body>
</html>
