<?php

namespace Model;

class Dia extends ActiveRecord
{
  protected static string $tabla = 'dias';
  protected static array $columnasDB = ['id', 'nombre'];

  public $id;
  public $nombre;
}
