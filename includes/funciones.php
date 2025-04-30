<?php

/**
 * Helper Functions
 * 
 * Este archivo contiene funciones auxiliares utilizadas en toda la aplicación DevWebCamp.
 * Incluye funciones para depuración y sanitización de datos.
 */

/**
 * Función para depurar variables.
 * Imprime una variable en formato legible y detiene la ejecución del script.
 *
 * @param string $variable La variable a depurar.
 * @return string
 */
function debuguear($variable): void
{
  echo "<pre>";
  var_dump($variable);
  echo "</pre>";
  exit;
}

/**
 * Función para sanitizar HTML.
 * Convierte caracteres especiales en entidades HTML para prevenir XSS.
 *
 * @param string $html El HTML a sanitizar.
 * @return string El HTML sanitizado.
 */
function s(string $html): string
{
  $s = htmlspecialchars($html);
  return $s;
}