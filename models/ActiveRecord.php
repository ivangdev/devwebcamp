<?php

namespace Model; // Define el espacio de nombres para la clase ActiveRecord

class ActiveRecord
{
  protected static \mysqli $db; // Objeto de la clase Database, utilizado para la conexión a la base de datos
  protected static string $tabla = ''; // Nombre de la tabla en la base de datos
  protected static array $columnasDB = []; // Array que contiene los nombres de las columnas de la tabla

  // Alertas y Mensajes
  protected static array $alertas = []; // Array asociativo que almacena mensajes de alerta

  // Método para definir la conexión a la base de datos
  public static function setDB(\mysqli $database)
  {
    self::$db = $database; // Asigna la conexión a la base de datos
  }

  // Método para establecer un tipo de alerta
  public static function setAlerta(string $tipo, string $mensaje)
  {
    static::$alertas[$tipo][] = $mensaje; // Agrega un mensaje de alerta al tipo especificado
  }

  // Método para obtener las alertas
  public static function getAlertas()
  {
    return static::$alertas; // Retorna el array de alertas
  }

  // Método de validación que se hereda en modelos
  public function validar()
  {
    static::$alertas = []; // Reinicia las alertas antes de la validación
    return static::$alertas; // Retorna las alertas (vacías por defecto)
  }

  // Método para realizar una consulta SQL y crear objetos en memoria (Active Record)
  public static function consultarSQL(string $query)
  {
    // Ejecuta la consulta en la base de datos
    $resultado = self::$db->query($query);

    // Itera sobre los resultados y crea objetos a partir de ellos
    $array = [];
    while ($registro = $resultado->fetch_assoc()) {
      $array[] = static::crearObjeto($registro); // Crea objetos a partir de los registros obtenidos
    }

    // Libera la memoria ocupada por el resultado
    $resultado->free();

    // Retorna el array de objetos creados
    return $array;
  }

  // Método para crear un objeto en memoria que es igual al de la base de datos
  protected static function crearObjeto(array $registro)
  {
    $objeto = new static; // Crea una nueva instancia de la clase

    // Asigna valores a las propiedades del objeto si existen en el registro
    foreach ($registro as $key => $value) {
      if (property_exists($objeto, $key)) {
        $objeto->$key = $value; // Asigna el valor a la propiedad correspondiente
      }
    }
    return $objeto; // Retorna el objeto creado
  }

  // Método para identificar y unir los atributos de la base de datos
  public function atributos()
  {
    $atributos = [];
    foreach (static::$columnasDB as $columna) {
      if ($columna === 'id') // Evita incluir el ID en los atributos
        continue;
      $atributos[$columna] = $this->$columna; // Asigna los atributos del objeto
    }
    return $atributos; // Retorna el array de atributos
  }

  // Método para sanitizar los datos antes de guardarlos en la base de datos
  public function sanitizarAtributos()
  {
    $atributos = $this->atributos(); // Obtiene los atributos del objeto
    $sanitizado = [];
    foreach ($atributos as $key => $value) {
      $sanitizado[$key] = self::$db->escape_string($value); // Sanitiza cada atributo para evitar inyecciones SQL
    }
    return $sanitizado; // Retorna los atributos sanitizados
  }

  // Método para sincronizar la base de datos con los objetos en memoria
  public function sincronizar(array $args = [])
  {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value; // Asigna valores a las propiedades del objeto si existen
      }
    }
  }

  // Método para guardar registros (CRUD)
  public function guardar()
  {
    $resultado = '';
    if (!is_null($this->id)) {
      // Si el ID no es nulo, se actualiza un registro existente
      $resultado = $this->actualizar();
    } else {
      // Si el ID es nulo, se crea un nuevo registro
      $resultado = $this->crear();
    }
    return $resultado; // Retorna el resultado de la operación
  }

  // Método para obtener todos los registros de la tabla
  public static function all()
  {
    $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC"; // Consulta para obtener todos los registros
    $resultado = self::consultarSQL($query);
    return $resultado; // Retorna todos los registros obtenidos
  }

  // Método para buscar un registro por su ID
  public static function find(int $id)
  {
    $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id}"; // Consulta para buscar por ID
    $resultado = self::consultarSQL($query);
    return array_shift($resultado); // Retorna el primer registro encontrado
  }
  
  // Método para obtener registros con una cantidad específica
  public static function get(int $limite)
  {
    $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite} ORDER BY id DESC"; // Consulta para limitar resultados
    $resultado = self::consultarSQL($query);
    return array_shift($resultado); // Retorna el primer registro encontrado
  }

  // Método para realizar una búsqueda con una condición específica en una columna
  public static function where(string $columna, $valor)
  {
    $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'"; // Consulta para buscar por columna
    $resultado = self::consultarSQL($query);
    return array_shift($resultado); // Retorna el primer registro encontrado
  }

  // Método para crear un nuevo registro en la base de datos
  public function crear()
  {
    // Sanitizar los datos antes de la inserción
    $atributos = $this->sanitizarAtributos();

    // Construir la consulta SQL para insertar en la base de datos
    $query = "INSERT INTO " . static::$tabla . " (";
    $query .= join(', ', array_keys($atributos)); // Agrega las claves de los atributos
    $query .= ") VALUES ('";
    $query .= join("', '", array_values($atributos)); // Agrega los valores de los atributos
    $query .= " ')"; // Cierra la consulta

    // debuguear($query); // Descomentar si la consulta no funciona

    // Ejecutar la consulta y retornar el resultado
    $resultado = self::$db->query($query);
    return [
      'resultado' => $resultado,
      'id' => self::$db->insert_id, // Retorna el ID del nuevo registro
    ];
  }

  // Método para actualizar un registro existente
  public function actualizar()
  {
    $atributos = $this->sanitizarAtributos(); // Obtiene los atributos sanitizados

    // Preparar los valores para la consulta de actualización
    $valores = [];
    foreach ($atributos as $key => $value) {
      $valores[] = "{$key} = '{$value}'"; // Prepara cada campo para la consulta
    }

    // Construir la consulta SQL para actualizar el registro
    $query = "UPDATE " . static::$tabla . " SET ";
    $query .= join(', ', $valores); // Agrega los valores a la consulta
    $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' "; // Condición para actualizar
    $query .= " LIMIT 1 "; // Limita la actualización a un solo registro

    // Ejecutar la consulta de actualización
    $resultado = self::$db->query($query);
    return $resultado; // Retorna el resultado de la actualización 
  }

  // Método para eliminar un registro por ID
  public function eliminar()
  {
    $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1"; // Consulta para eliminar
    $resultado = self::$db->query($query); // Ejecutar la consulta
    return $resultado; // Retorna el resultado de la eliminación 
  } 
}