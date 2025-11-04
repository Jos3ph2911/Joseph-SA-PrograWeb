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

// Obtener los vehículos del chofer
$sql = "SELECT * FROM vehiculos WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Vehículos - Aventones</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: #007bff;
            color: white;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        header img {
            height: 180px;
            width: auto;
            object-fit: contain;
            border: none;
            border-radius: 0;
        }

        main {
            flex: 1;
            margin: 30px 40px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Botones */
        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            color: white;
            text-align: center;
            transition: background 0.2s;
            vertical-align: middle;
        }

        .btn-verde { background-color: #28a745; }
        .btn-verde:hover { background-color: #218838; }

        .btn-gris { background-color: #6c757d; }
        .btn-gris:hover { background-color: #5a6268; }

        .btn-rojo { background-color: #dc3545; }
        .btn-rojo:hover { background-color: #c82333; }

        .acciones a {
            margin-right: 8px;
            text-decoration: none;
            color: #007bff;
        }
        .acciones a:hover {
            text-decoration: underline;
        }

        /* Imágenes de vehículos */
        .tabla-foto {
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        /* Pie de página */
        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <header>
        <img src="../../logo/logo.png" alt="Logo Aventones">
    </header>

    <main>
        <h2>Mis Vehículos</h2>

        <div style="margin-bottom: 20px;">
            <a href="crear.php" class="btn btn-verde">+ Agregar nuevo vehículo</a>
            <a href="../viajes/listar.php" class="btn btn-gris">← Volver a mis viajes</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Placa</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Color</th>
                    <th>Capacidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($fila['foto'])): ?>
                                    <img src="../../<?php echo htmlspecialchars($fila['foto']); ?>" alt="Foto del vehículo" width="80" class="tabla-foto">
                                <?php else: ?>
                                    <span>Sin foto</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($fila['placa']); ?></td>
                            <td><?php echo htmlspecialchars($fila['marca']); ?></td>
                            <td><?php echo htmlspecialchars($fila['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['anio']); ?></td>
                            <td><?php echo htmlspecialchars($fila['color']); ?></td>
                            <td><?php echo htmlspecialchars($fila['capacidad']); ?></td>
                            <td class="acciones">
                                <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                                <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que desea eliminar este vehículo?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No tiene vehículos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        © <?php echo date("Y"); ?> Aventones | Proyecto ISW-613
    </footer>

</body>
</html>
