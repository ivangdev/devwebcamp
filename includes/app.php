<?php

// Importar las clases necesarias
use Dotenv\Dotenv;
use Model\ActiveRecord;

// Cargar el autoload de Composer para gestionar las dependencias
require __DIR__ . '/../vendor/autoload.php';

// Crear una instancia de Dotenv para manejar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);

// Cargar las variables de entorno desde el archivo .env de manera segura
$dotenv->safeLoad();

// Incluir el archivo de funciones auxiliares
require 'funciones.php';

// Incluir el archivo de configuración de la base de datos
require 'database.php';

// Establecer la conexión a la base de datos utilizando la configuración cargada
ActiveRecord::setDB($db);