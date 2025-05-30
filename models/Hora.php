<?php

namespace Model;

class Hora extends ActiveRecord
{
  protected static string $tabla = 'horas';
  protected static array $columnasDB = ['id', 'hora'];

  public $id;
  public $hora;
}
