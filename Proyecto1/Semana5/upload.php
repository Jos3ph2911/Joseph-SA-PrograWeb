<?php

var_dump("nameFile");
var_dump("tmp_name");

$target_dir = "uploads/"; // Directorio donde se guardarán los archivos C:\ISW613\httpdocs\Semana5\uploads
// Ruta completa del archivo en el servidor
$target_file = $target_dir.basename($_FILES["nameFile"]["name"]);

// Verifica si se subió un archivo
if (isset($_FILES["nameFile"]) && $_FILES["nameFile"]["error"] == 0) {
// Verifica si el directorio de subida existe, sino lo crea
if (!file_exists($target_dir)) {
mkdir($target_dir, 0777, true);
}
// Intenta mover el archivo temporal a la carpeta de destino
if (move_uploaded_file($_FILES["nameFile"]["tmp_name"], $target_file))
{
echo "El archivo ". htmlspecialchars( basename(
$_FILES["nameFile"]["name"])). " ha sido subido con éxito.";
} else { echo "Hubo un error al subir tu archivo."; }
} else { echo "No se ha seleccionado ningún archivo o hubo un error durante la
carga."; }
?>