<?php

namespace MVC; // Definimos el namespace de la clase
class Router
{
  // Almacena las rutas GET como un array asociativo [url => función]
  public array $getRoutes = [];

  // Almacena las rutas POST como un array asociativo [url => función]
  public array $postRoutes = [];

  /**
   * Registra una ruta GET
   * @param string $url La URL a registrar
   * @param callable $fn Función a ejecutar cuando se accede a la URL
   */
  public function get($url, $fn): void
  {
    $this->getRoutes[$url] = $fn;
  }

  /**
   * Registra una ruta POST
   * @param string $url La URL a registrar
   * @param callable $fn Función a ejecutar cuando se accede a la URL
   */
  public function post($url, $fn): void
  {
    $this->postRoutes[$url] = $fn;
  }

  /**
   * Comprueba las rutas registradas y ejecuta la función correspondiente
   * según el método de la petición (GET o POST)
   */
  public function comprobarRutas(): void
  {
    $url_actual = $_SERVER['PATH_INFO'] ?? '/'; // Obtenemos la url actual
    $method = $_SERVER['REQUEST_METHOD']; // Obtenemos el método de la petición

    if ($method === 'GET') { // Si el método es GET
      $fn = $this->getRoutes[$url_actual] ?? null;  // Si la url actual existe en el array de rutas, ejecutamos la función
    } else {
      $fn = $this->postRoutes[$url_actual] ?? null;  // Si la url actual existe en el array de rutas, ejecutamos la función
    }

    if ($fn) {
      call_user_func($fn, $this); // call_user_func ejecuta la función que le pasamos como parámetro
    } else {
      echo 'Página no encontrada'; // Mensaje de error si no se encuentra la ruta
    }
  }

  /**
   * Renderiza una vista con los datos proporcionados
   * @param string $view Nombre de la vista a renderizar
   * @param array $datos Datos a pasar a la vista
   */
  public function render($view, $datos = []): void
  {
    foreach ($datos as $key => $value) { // Recorremos el array de datos y los convertimos en variables
      $$key = $value; // $$key es igual a $value 
    }

    ob_start(); // Iniciamos el buffer de salida para almacenar el contenido en una variable

    include_once __DIR__ . "/views/$view.php"; // Incluimos la vista que queremos renderizar

    $contenido = ob_get_clean(); // Guardamos el contenido del buffer en una variable y limpiamos el buffer

    include_once __DIR__ . '/views/layout.php'; // Incluimos el layout de la página
  }
}