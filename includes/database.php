<?php
// Conectar a la base de datos utilizando las variables de entorno
$db = mysqli_connect(
  $_ENV['DB_HOST'] ?? '', // Host de la base de datos
  $_ENV['DB_USER'] ?? '', // Usuario de la base de datos
  $_ENV['DB_PASS'] ?? '', // Contraseña de la base de datos
  $_ENV['DB_NAME'] ?? ''  // Nombre de la base de datos
);

// Verificar si la conexión fue exitosa
if (!$db) {
  // Mostrar mensajes de error si la conexión falla
  echo 'Error: No se pudo conectar a MySQL.';
  echo 'errno de depuración: ' . mysqli_connect_errno();
  echo 'error de depuración: ' . mysqli_connect_error();
  exit; // Terminar la ejecución del script si no se puede conectar
}