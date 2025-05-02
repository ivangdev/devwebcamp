<?php
namespace Model;

/**
 * Clase Usuario que representa a un usuario en el sistema.
 * Extiende la clase ActiveRecord para manejar la persistencia en la base de datos.
 */
class Usuario extends ActiveRecord
{
  // Nombre de la tabla en la base de datos
  protected static string $tabla = 'usuarios';
  // Columnas de la tabla
  protected static array $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'confirmado', 'token', 'admin'];

  // Propiedades del usuario
  public ?int $id; // ? significa que puede ser null
  public string $nombre;
  public string $apellido;
  public string $email;
  public string $password;
  public string $password2;
  public $confirmado;
  public $token;
  public $admin;
  public string $password_actual;
  public string $password_nuevo;

  /**
   * Constructor de la clase Usuario.
   * Inicializa las propiedades del usuario con los valores proporcionados en el array $args.
   *
   * @param array $args Valores iniciales para las propiedades del usuario.
   */
  public function __construct(array $args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->nombre = $args['nombre'] ?? '';
    $this->apellido = $args['apellido'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->password2 = $args['password2'] ?? '';
    $this->confirmado = $args['confirmado'] ?? 0;
    $this->token = $args['token'] ?? '';
    $this->admin = $args['admin'] ?? 0;
  }

  // Validar el login de Usuarios
  /**
   * Valida los datos de login del usuario.
   * Verifica que el email y la contraseña sean válidos.
   *
   * @return array Retorna un array con las alertas de error.
   */
  public function validarLogin(): array
  {
    if (!$this->email) {
      self::$alertas['error'][] = 'El email es obligatorio';
    }
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      self::$alertas['error'][] = 'El email es inválido';
    }
    if (!$this->password) {
      self::$alertas['error'][] = 'La contraseña es obligatoria';
    }

    return self::$alertas;
  }

  // Validación para cuentas nuevas
  /**
   * Valida los datos necesarios para crear una nueva cuenta de usuario.
   *
   * @return array Retorna un array con las alertas de error.
   */
  public function validar_cuenta(): array
  {
    if (!$this->nombre) {
      self::$alertas['error'][] = 'El nombre es obligatorio';
    }
    if (!$this->apellido) {
      self::$alertas['error'][] = 'El apellido es obligatorio';
    }
    if (!$this->email) {
      self::$alertas['error'][] = 'El email es obligatorio';
    }
    if (!$this->password) {
      self::$alertas['error'][] = 'La password es obligatoria';
    }
    if (strlen($this->password) < 6) {
      self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
    }
    if ($this->password !== $this->password2) {
      self::$alertas['error'][] = 'Las contraseñas no coinciden';
    }
    return self::$alertas;
  }

  // Valida un email
  /**
   * Valida el formato del email del usuario.
   *
   * @return array Retorna un array con las alertas de error.
   */
  public function validarEmail(): array
  {
    if (!$this->email) {
      self::$alertas['error'][] = 'El email es obligatorio';
    }
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      self::$alertas['error'][] = 'Email no válido';
    }
    return self::$alertas;
  }

  // Validar el password
  /**
   * Valida la contraseña del usuario.
   *
   * @return array Retorna un array con las alertas de error.
   */
  public function validarPassword(): array
  {
    if (!$this->password) {
      self::$alertas['error'][] = 'El password es obligatorio';
    }
    if (strlen($this->password) < 6) {
      self::$alertas['error'][] = 'El password debe tener al menos 6 caracteres';
    }
    return self::$alertas;
  }

  // Validar nuevo password
  /**
   * Valida el cambio de contraseña del usuario.
   *
   * @return array Retorna un array con las alertas de error.
   */
  public function nuevoPassword(): array
  {
    if (!$this->password_actual) {
      self::$alertas['error'][] = 'El password actual es obligatorio';
    }
    if (!$this->password_nuevo) {
      self::$alertas['error'][] = 'El password nuevo es obligatorio';
    }
    if (strlen($this->password_nuevo) < 6) {
      self::$alertas['error'][] = 'El password nuevo debe tener al menos 6 caracteres';
    }
    return self::$alertas;
  }

  // Comprobar el password
  /**
   * Comprueba si el password actual coincide con el password almacenado.
   *
   * @return bool Retorna true si coincide, false en caso contrario.
   */
  public function comprobarPassword(): bool
  {
    return password_verify($this->password_actual, $this->password);
  }

  // Hashea el password
  /**
   * Hashea la contraseña del usuario utilizando el algoritmo BCRYPT.
   */
  public function hashPassword(): void
  {
    $this->password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  // Generar un Token
  /**
   * Genera un token único para el usuario.
   */
  public function generarToken(): void
  {
    $this->token = uniqid();
  }
}