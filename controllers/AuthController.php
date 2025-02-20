<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class AuthController
{
  /**
   * Maneja el inicio de sesión de los usuarios.
   *
   * @param Router $router El enrutador para renderizar vistas.
   * @return void
   */
  public static function login(Router $router)
  {
    $alertas = [];

    // Verificar si la solicitud es de tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usuario = new Usuario($_POST);
      $alertas = $usuario->validarLogin();

      if (empty($alertas)) {
        // Verificar si el usuario existe
        $usuario = Usuario::where('email', $usuario->email);
        if (!$usuario || !$usuario->confirmado) {
          Usuario::setAlerta('error', 'El usuario no existe o no ha sido confirmado');
        } else {
          // El usuario existe, verificar la contraseña
          if (password_verify($_POST['password'], $usuario->password)) {
            // Iniciar sesión
            session_start();
            $_SESSION['usuario'] = $usuario->id;
            $_SESSION['nombre'] = $usuario->nombre;
            $_SESSION['apellido'] = $usuario->apellido;
            $_SESSION['email'] = $usuario->email;
            $_SESSION['admin'] = $usuario->admin ?? null;
          } else {
            Usuario::setAlerta('error', 'La contraseña es incorrecta');
          }
        }
      }
    }

    $alertas = Usuario::getAlertas();

    // Renderizar la vista
    $router->render('auth/login', [
      'titulo' => 'Iniciar Sesión',
      'alertas' => $alertas,
    ]);
  }

  /**
   * Maneja el cierre de sesión de los usuarios.
   *
   * @return void
   */
  public static function logout()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      $_SESSION = [];
      header('Location: /');
    }
  }

  /**
   * Maneja el registro de nuevos usuarios.
   *
   * @param Router $router El enrutador para renderizar vistas.
   * @return void
   */
  public static function registro(Router $router)
  {
    $alertas = [];
    $usuario = new Usuario();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usuario->sincronizar($_POST);
      $alertas = $usuario->validar_cuenta();

      if (empty($alertas)) {
        $existeUsuario = Usuario::where('email', $usuario->email);
        if ($existeUsuario) {
          Usuario::setAlerta('error', 'El usuario ya existe');
          $alertas = Usuario::getAlertas();
        } else {
          // Hashear el password
          $usuario->hashPassword();

          // Eliminar el password2
          unset($usuario->password2);

          // Generar el token
          $usuario->generarToken();

          // Crear un nuevo usuario
          $resultado = $usuario->guardar();

          // Enviar el email de confirmación
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          $email->enviarConfirmacion();

          if ($resultado) {
            header('Location: /mensaje');
          }
        }
      }
    }

    // Renderizar la vista
    $router->render('auth/registro', [
      'titulo' => 'Crear tu cuenta en DevWebcamp',
      'usuario' => $usuario,
      'alertas' => $alertas,
    ]);
  }

  /**
   * Maneja la solicitud de restablecimiento de contraseña.
   *
   * @param Router $router El enrutador para renderizar vistas.
   * @return void
   */
  public static function olvide(Router $router)
  {
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $usuario = new Usuario($_POST);
      $alertas = $usuario->validarEmail();

      if (empty($alertas)) {
        // Buscar el usuario
        $usuario = Usuario::where('email', $usuario->email);

        if ($usuario && $usuario->confirmado) {
          // Generar un nuevo token
          $usuario->generarToken();
          unset($usuario->password2);

          // Actualizar el usuario
          $usuario->guardar();

          // Enviar el email con instrucciones
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          $email->enviarInstrucciones();

          // Imprimir la alerta
          Usuario::setAlerta('success', 'Hemos enviado las instrucciones a tu email');
        } else {
          Usuario::setAlerta('error', 'El Usuario no existe o no ha sido confirmado');
        }
      }
    }

    $alertas = Usuario::getAlertas();

    // Renderizar la vista
    $router->render('auth/olvide', [
      'titulo' => 'Olvide mi contraseña',
      'alertas' => $alertas,
    ]);
  }

  /**
   * Maneja el restablecimiento de la contraseña.
   *
   * @param Router $router El enrutador para renderizar vistas.
   * @return void
   */
  public static function reestablecer(Router $router)
  {
    $token = s($_GET['token']);

    $token_valido = true;
    if (!$token)
      header('Location: /');

    // Identificar el usuario con el token
    $usuario = Usuario::where('token', $token);

    if (empty($usuario)) {
      Usuario::setAlerta('error', 'Token no válido');
      $token_valido = false;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Añadir el nuevo password
      $usuario->sincronizar($_POST);

      // Validar el password
      $alertas = $usuario->validarPassword();

      if (empty($alertas)) {
        // Hashear el nuevo password
        $usuario->hashPassword();

        // Eliminar el token
        $usuario->token = null;

        // Guardar el usuario en la base de datos
        $resultado = $usuario->guardar();

        // Redireccionar
        if ($resultado) {
          header('Location: /');
        }
      }
    }

    $alertas = Usuario::getAlertas();

    // Renderizar la vista
    $router->render('auth/reestablecer', [
      'titulo' => 'Reestablecer contraseña',
      'alertas' => $alertas,
      'token_valido' => $token_valido,
    ]);
  }

  /**
   * Muestra un mensaje de cuenta creada exitosamente.
   *
   * @param Router $router El enrutador para renderizar vistas.
   * @return void
   */
  public static function mensaje(Router $router)
  {
    // Renderizar la vista
    $router->render('auth/mensaje', [
      'titulo' => 'Cuenta creada Exitosamente',
    ]);
  }

  /**
   * Confirma la cuenta del usuario.
   *
   * @param Router $router El enrutador para renderizar vistas.
   * @return void
   */
  public static function confirmar(Router $router)
  {
    $token = s($_GET['token']);

    if (!$token)
      header('Location: /');

    // Identificar el usuario con el token
    $usuario = Usuario::where('token', $token);

    if (empty($usuario)) {
      // No se encontró el usuario con el token
      Usuario::setAlerta('error', 'Token no válido');
    } else {
      // Confirmar el usuario
      $usuario->confirmado = 1;
      $usuario->token = '';
      unset($usuario->password2);

      // Guardar el usuario en la base de datos
      $usuario->guardar();

      Usuario::setAlerta('success', 'Cuenta comprobada correctamente');
    }

    $router->render('auth/confirmar', [
      'titulo' => 'Confirma tu cuenta DevWebcamp',
      'alertas' => Usuario::getAlertas(),
    ]);
  }
}