<?php

// Cargar la aplicación principal
require_once __DIR__ . '/../includes/app.php';

// Importar las clases necesarias
use MVC\Router;
use Controllers\AuthController;
use Controllers\DashboardController;
use Controllers\EventosController;
use Controllers\PonentesController;
use Controllers\RegalosController;
use Controllers\RegistradosController;

// Crear una nueva instancia del enrutador
$router = new Router();

// Rutas para el inicio de sesión
// Mostrar el formulario de inicio de sesión
$router->get('/login', [AuthController::class, 'login']);
// Procesar el inicio de sesión
$router->post('/login', [AuthController::class, 'login']);
// Procesar el cierre de sesión
$router->post('/logout', [AuthController::class, 'logout']);

// Rutas para la creación de cuentas
// Mostrar el formulario de registro
$router->get('/registro', [AuthController::class, 'registro']);
// Procesar el registro de una nueva cuenta
$router->post('/registro', [AuthController::class, 'registro']);

// Rutas para la recuperación de contraseña
// Mostrar el formulario para olvidar la contraseña
$router->get('/olvide', [AuthController::class, 'olvide']);
// Procesar la solicitud de recuperación de contraseña
$router->post('/olvide', [AuthController::class, 'olvide']);

// Rutas para restablecer la contraseña
// Mostrar el formulario para restablecer la contraseña
$router->get('/reestablecer', [AuthController::class, 'reestablecer']);
// Procesar el restablecimiento de la contraseña
$router->post('/reestablecer', [AuthController::class, 'reestablecer']);

// Rutas para la confirmación de cuenta
// Mostrar el mensaje de confirmación
$router->get('/mensaje', [AuthController::class, 'mensaje']);
// Confirmar la cuenta del usuario
$router->get('/confirmar-cuenta', [AuthController::class, 'confirmar']);

// Area de administración
$router->get('/admin/dashboard', [DashboardController::class, 'index']);

$router->get('/admin/ponentes', [PonentesController::class, 'index']);
$router->get('/admin/ponentes/crear', [PonentesController::class, 'crear']);
$router->post('/admin/ponentes/crear', [PonentesController::class, 'crear']);

$router->get('/admin/eventos', [EventosController::class, 'index']);

$router->get('/admin/registrados', [RegistradosController::class, 'index']);

$router->get('/admin/regalos', [RegalosController::class, 'index']);

// Comprobar las rutas definidas
$router->comprobarRutas();
