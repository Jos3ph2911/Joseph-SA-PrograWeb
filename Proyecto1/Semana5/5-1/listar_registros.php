<?php
include("db_conexion.php");

// Consultar los registros
$resultado = $conexion->query("SELECT * FROM ventas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ventas</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #e2e2e2; }
        a { text-decoration: none; color: blue; }
        .boton { background: #0078D7; color: white; padding: 6px 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Listado de Ventas</h2>
    <div style="text-align:center;">
        <a href="formulario_registro.php" class="boton">Agregar nueva venta</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cantidad</th>
                <th>Monto Total</th>
                <th>Gravado</th>
                <th>Monto por Unidad</th>
                <th>Impuesto (13%)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()) {
                $montoUnidad = $fila['monto_total'] / $fila['cantidad'];
                $impuesto = ($fila['gravado'] == 'Si') ? $fila['monto_total'] * 0.13 : 0;
            ?>
            <tr>
                <td><?= $fila['fecha'] ?></td>
                <td><?= $fila['cantidad'] ?></td>
                <td>₡<?= number_format($fila['monto_total'], 2) ?></td>
                <td><?= $fila['gravado'] ?></td>
                <td>₡<?= number_format($montoUnidad, 2) ?></td>
                <td>₡<?= number_format($impuesto, 2) ?></td>
                <td>
                    <a href="formulario_registro.php?id=<?= $fila['id'] ?>">Editar</a> |
                    <a href="eliminar_registro.php?id=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
                