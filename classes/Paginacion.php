<?php

namespace Classes;

class Paginacion
{
  public $pagina_actual;
  public $registros_por_pagina;
  public $total_registros;

  public function __construct($pagina_actual = 1, $registros_por_pagina = 10, $total_registros = 0)
  {
    // (int) para castear a entero
    $this->pagina_actual = (int) $pagina_actual;
    $this->registros_por_pagina = (int) $registros_por_pagina;
    $this->total_registros = (int) $total_registros;
  }


  public function offset()
  {
    // Operacion para calcular el offset 
    return $this->registros_por_pagina * ($this->pagina_actual - 1);
  }

  public function total_paginas()
  {
    return ceil($this->total_registros / $this->registros_por_pagina);
  }

  public function pagina_anterior()
  {
    $anterior = $this->pagina_actual - 1;
    // Si la pagina anterior es mayor a 0, entonces retorna la pagina anterior, sino retorna false
    return ($anterior > 0) ? $anterior : false;
  }

  public function pagina_siguiente()
  {
    $siguiente = $this->pagina_actual + 1;
    // Si la pagina siguiente es menor o igual al total de paginas, entonces retorna la pagina siguiente, sino retorna false
    return ($siguiente <= $this->total_paginas()) ? $siguiente : false;
  }
}
