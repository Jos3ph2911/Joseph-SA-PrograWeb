<?php
include("db_conexion.php");

$id = isset($_GET['id']) ? $_GET['id'] : null;
$registro = ['fecha' => '', 'cantidad' => '', 'monto_total' => '', 'gravado' => 'No'];

if ($id) {
    $resultado = $conexion->query("SELECT * FROM ventas WHERE id=$id");
    if ($resultado->num_rows > 0) {
        $registro = $resultado->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Editar' : 'Agregar' ?> Venta</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; }
        form { width: 50%; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        .boton { background: #0078D7; color: white; border: none; padding: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h2 style="text-align:center;"><?= $id ? 'Editar' : 'Agregar' ?> Registro de Venta</h2>
    <form method="POST" action="guardar_registro.php">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= $registro['fecha'] ?>" required>
        
        <label>Cantidad:</label>
        <input type="number" name="cantidad" value="<?= $registro['cantidad'] ?>" required>
        
        <label>Monto Total (₡):</label>
        <input type="number" step="0.01" name="monto_total" value="<?= $registro['monto_total'] ?>" required>
        
        <label>¿Es gravado?</label>
        <select name="gravado">
            <option value="Si" <?= ($registro['gravado'] == 'Si') ? 'selected' : '' ?>>Sí</option>
            <option value="No" <?= ($registro['gravado'] == 'No') ? 'selected' : '' ?>>No</option>
        </select>

        <button type="submit" class="boton">Guardar</button>
    </form>
</body>
</html>
