<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario</title>
</head>
<body>
  <h2>Registrar Usuario</h2>
  <form action="insertar.php" method="post">
    Nombre:<br> <input type="text" name="primer_nombre" required><br><br>
    Apellido:<br> <input type="text" name="apellido" required><br><br>
    Correo:<br> <input type="email" name="correo" required><br><br>
    Teléfono:<br> <input type="text" name="telefono" required><br><br>
    <button type="submit">Guardar</button>
  </form>
  <br>
  <a href="lista.php">Ver usuarios guardados</a>
</body>
</html>

