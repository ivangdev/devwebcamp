<?php
/**
 * Database Connection Configuration
 * 
 * Este archivo establece la conexión a la base de datos MySQL para DevWebCamp.
 * Utiliza variables de entorno para la configuración segura de la conexión.
 * 
 * Variables de entorno requeridas:
 * @var string DB_HOST - Dirección del servidor de base de datos
 * @var string DB_USER - Nombre de usuario de MySQL
 * @var string DB_PASS - Contraseña de MySQL
 * @var string DB_NAME - Nombre de la base de datos
 * 
 * @return mysqli|void - Retorna la conexión o termina el script si hay error
 */

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
