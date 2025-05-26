<?php

namespace Controllers;

use Classes\Paginacion;
use Model\Ponente;
use MVC\Router;
use Intervention\Image\ImageManager as Image;
use Intervention\Image\Drivers\Gd\Driver;

class PonentesController
{
  public static function index(Router $router)
  {
    // Paginacion
    $pagina_actual = $_GET['page'];
    $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

    if (!$pagina_actual || $pagina_actual < 1) {
      header('Location: /admin/ponentes?page=1');
    }

    $registrios_por_pagina = 10;
    $total = Ponente::total();
    $paginacion = new Paginacion($pagina_actual, $registrios_por_pagina, $total);

    debuguear($paginacion->pagina_siguiente());

    $ponentes = Ponente::all();

    // Proteger ruta en caso de que no sea admin
    if (!is_admin()) {
      header('Location: /login');
    }

    $router->render('admin/ponentes/index', [
      'titulo' => 'Ponentes / Conferencistas',
      'ponentes' => $ponentes
    ]);
  }

  public static function crear(Router $router)
  {
    // Proteger ruta en caso de que no sea admin
    if (!is_admin()) {
      header('Location: /login');
    }
    $alertas = [];
    $ponente = new Ponente;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Proteger ruta en caso de que no sea admin
      if (!is_admin()) {
        header('Location: /login');
      }

      // Leer imagen
      if (!empty($_FILES['imagen']['tmp_name'])) {
        $carpeta_imagenes = '../public/img/speakers';

        // Crear la carpeta si no existe
        if (!is_dir($carpeta_imagenes)) {
          mkdir($carpeta_imagenes, 0777, true);
        }

        $manager = new Image(Driver::class);
        $imagen_png = $manager->read($_FILES['imagen']['tmp_name'])->contain(800, 800)->encodeByExtension('png', 80);
        $imagen_webp = $manager->read($_FILES['imagen']['tmp_name'])->contain(800, 800)->encodeByExtension('webp', 80);

        $nombre_imagen = md5(uniqid(rand(), true));
        $_POST['imagen'] = $nombre_imagen;
      }

      $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);

      $ponente->sincronizar($_POST);

      // Validar los datos
      $alertas = $ponente->validar();

      // Guardar el registro
      if (empty($alertas)) {
        // Guardar las imagenes
        $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
        $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');

        // Guardar en la base de datos
        $resultado = $ponente->guardar();

        if ($resultado) {
          header('Location: /admin/ponentes');
        }
      }
    }

    $router->render('admin/ponentes/crear', [
      'titulo' => 'Registrar Ponente',
      'alertas' => $alertas,
      'ponente' => $ponente,
      'redes' => json_decode($ponente->redes)
    ]);
  }

  public static function editar(Router $router)
  {
    // Proteger ruta en caso de que no sea admin
    if (!is_admin()) {
      header('Location: /login');
    }

    $alertas = [];
    // Validar ID
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if (!$id) { // Si no es un Integer
      header('Location: /admin/ponentes');
    }

    // Obtener ponente a editar
    $ponente = Ponente::find($id);

    if (!$ponente) {
      header('Location: /admin/ponentes');
    }

    // Variable temporal
    $ponente->imagen_actual = $ponente->imagen; // Guardar la imagen actual

    // debuguear($redes);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Proteger ruta en caso de que no sea admin
      if (!is_admin()) {
        header('Location: /login');
      }

      // Leer imagen
      if (!empty($_FILES['imagen']['tmp_name'])) {
        $carpeta_imagenes = '../public/img/speakers';

        // Crear la carpeta si no existe
        if (!is_dir($carpeta_imagenes)) {
          mkdir($carpeta_imagenes, 0777, true);
        }

        $manager = new Image(Driver::class);
        $imagen_png = $manager->read($_FILES['imagen']['tmp_name'])->contain(800, 800)->encodeByExtension('png', 80);
        $imagen_webp = $manager->read($_FILES['imagen']['tmp_name'])->contain(800, 800)->encodeByExtension('webp', 80);

        $nombre_imagen = md5(uniqid(rand(), true));
        $_POST['imagen'] = $nombre_imagen;
      } else {
        $_POST['imagen'] = $ponente->imagen_actual; // Si no se sube una nueva imagen, se mantiene la actual
      }

      $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);
      $ponente->sincronizar($_POST);
      $alertas = $ponente->validar();

      // Validar que la imagen no sea la misma que la actual
      if (empty($alertas)) {
        // Si la imagen es la misma que la actual, no se guarda una nueva
        if (isset($nombre_imagen)) {
          // Guardar las imagenes
          $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
          $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');
        }
        // Guardar en la base de datos
        $resultado = $ponente->guardar();

        if ($resultado) {
          header('Location: /admin/ponentes');
        }
      }
    }

    $router->render('admin/ponentes/editar', [
      'titulo' => 'Actualizar Ponente',
      'alertas' => $alertas,
      'ponente' => $ponente,
      'redes' => json_decode($ponente->redes)
    ]);
  }

  public static function eliminar()
  {
    // Proteger ruta en caso de que no sea admin
    if (!is_admin()) {
      header('Location: /login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $ponente = Ponente::find($id);

      if (!isset($ponente)) {
        header('Location: /admin/ponentes');
      }

      // debuguear($ponente);
      $resultado = $ponente->eliminar();
      if ($resultado) {
        header('Location: /admin/ponentes');
      }
    }
  }
}
