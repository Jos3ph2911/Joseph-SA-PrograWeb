<?php
$conexion = new mysqli("localhost", "root", "", "isw613_workshop2");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$resultado = $conexion->query("SELECT * FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios guardados</title>
  <style>
    table {
      border-collapse: collapse;
      width: 70%;
      margin: 20px 0;
    }
    th, td {
      border: 1px solid black;
      padding: 8px 12px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <h2>Usuarios guardados</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>Correo</th>
      <th>Teléfono</th>
    </tr>
    <?php while($fila = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?=$fila['id']?></td>
        <td><?=$fila['primer_nombre']?></td>
        <td><?=$fila['apellido']?></td>
        <td><?=$fila['correo']?></td>
        <td><?=$fila['telefono']?></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <br><a href="form.php">Volver al formulario</a>
</body>
</html>
<?php
$conexion->close();
?>
