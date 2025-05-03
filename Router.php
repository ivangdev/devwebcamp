<?php

namespace MVC;

/**
 * Class Router
 * 
 * Maneja el enrutamiento de la aplicación MVC, gestionando rutas GET y POST,
 * y renderizando las vistas correspondientes.
 * 
 * @package MVC
 */
class Router
{
  /**
   * Almacena las rutas GET como un array asociativo [url => función]
   * @var array<string, callable>
   */
  public array $getRoutes = [];

  /**
   * Almacena las rutas POST como un array asociativo [url => función]
   * @var array<string, callable>
   */
  public array $postRoutes = [];

  /**
   * Registra una ruta GET
   * 
   * @param string $url La URL a registrar
   * @param callable $fn Función a ejecutar cuando se accede a la URL
   * @return void
   */
  public function get($url, $fn): void
  {
    $this->getRoutes[$url] = $fn;
  }

  /**
   * Registra una ruta POST
   * 
   * @param string $url La URL a registrar
   * @param callable $fn Función a ejecutar cuando se accede a la URL
   * @return void
   */
  public function post($url, $fn): void
  {
    $this->postRoutes[$url] = $fn;
  }

  /**
   * Comprueba las rutas registradas y ejecuta la función correspondiente
   * según el método de la petición (GET o POST)
   * 
   * @throws \Exception Si la ruta no existe
   * @return void
   */
  public function comprobarRutas(): void
  {
    $url_actual = $_SERVER['PATH_INFO'] ?? '/';
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
      $fn = $this->getRoutes[$url_actual] ?? null;
    } else {
      $fn = $this->postRoutes[$url_actual] ?? null;
    }

    if ($fn) {
      call_user_func($fn, $this);
    } else {
      echo 'Página no encontrada';
    }
  }

  /**
   * Renderiza una vista con los datos proporcionados
   * 
   * @param string $view Nombre de la vista a renderizar (sin extensión .php)
   * @param array $datos Array asociativo con los datos a pasar a la vista
   * @return void
   */
  public function render($view, $datos = [])
  {
    foreach ($datos as $key => $value) {
      $$key = $value;
    }

    ob_start();

    include_once __DIR__ . "/views/$view.php";

    $contenido = ob_get_clean(); // Limpiar el buffer de salida

    // Utilizar el layout de acuerdo a la url
    $url_actual = $_SERVER['PATH_INFO'] ?? '/';

    if (str_contains($url_actual, '/admin')) {
      include_once __DIR__ . '/views/admin-layout.php';
    } else {
      include_once __DIR__ . '/views/layout.php';
    }
  }
}
