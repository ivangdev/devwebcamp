<?php

namespace Model;

class Ponente extends ActiveRecord
{
  public $id;
  public $nombre;
  public $apellido;
  public $ciudad;
  public $pais;
  public $imagen;
  public $tags;
  public $redes;

  protected static string $tabla = 'ponentes';
  protected static array $columnaDB = ['id', 'nombre', 'apellido', 'ciudad', 'pais', 'imagen', 'tags', 'redes'];

  public function __construct($args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->nombre = $args['nombre'] ?? '';
    $this->apellido = $args['apellido'] ?? '';
    $this->ciudad = $args['ciudad'] ?? '';
    $this->pais = $args['pais'] ?? '';
    $this->imagen = $args['imagen'] ?? '';
    $this->tags = $args['tags'] ?? '';
    $this->redes = $args['redes'] ?? '';
  }

  public function validar()
  {
    if (!$this->nombre) {
      self::$alertas['error'][] = 'El nombre es obligatorio';
    }
    if (!$this->apellido) {
      self::$alertas['error'][] = 'El apellido es obligatorio';
    }
    if (!$this->ciudad) {
      self::$alertas['error'][] = 'La ciudad es obligatoria';
    }
    if (!$this->pais) {
      self::$alertas['error'][] = 'El país es obligatorio';
    }
    if (!$this->imagen) {
      self::$alertas['error'][] = 'La imagen es obligatoria';
    }
    if (!$this->tags) {
      self::$alertas['error'][] = 'Los tags son obligatorios';
    }
    return self::$alertas;
  }
}
