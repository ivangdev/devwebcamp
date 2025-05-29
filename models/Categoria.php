<?php

namespace Model;

class Categoria extends ActiveRecord
{
  protected static string $tabla = 'categorias';
  protected static array $columnasDB = ['id', 'nombre'];

  public $id;
  public $nombre;
}
