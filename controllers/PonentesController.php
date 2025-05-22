<?php

namespace Controllers;

use Model\Ponente;
use MVC\Router;
use Intervention\Image\ImageManager as Image;
use Intervention\Image\Drivers\Gd\Driver; 

class PonentesController
{
  public static function index(Router $router)
  {
    $router->render('admin/ponentes/index', [
      'titulo' => 'Ponentes / Conferencistas',
    ]);
  }

  public static function crear(Router $router)
  {
    $alertas = [];
    $ponente = new Ponente;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

      $ponente->sincronizar($_POST);

      // Validar los datos
      $alertas = $ponente->validar();

      // Guardar el registro
    }

    $router->render('admin/ponentes/crear', [
      'titulo' => 'Registrar Ponente',
      'alertas' => $alertas,
      'ponente' => $ponente
    ]);
  }
}
