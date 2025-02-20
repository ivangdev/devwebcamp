<?php

/**
 * Función para depurar variables.
 *
 * @param string $variable La variable a depurar.
 * @return string
 */
function debuguear(string $variable): string
{
  echo "<pre>";
  var_dump($variable);
  echo "</pre>";
  exit;
}

/**
 * Función para sanitizar HTML.
 *
 * @param string $html El HTML a sanitizar.
 * @return string El HTML sanitizado.
 */
function s(string $html): string
{
  $s = htmlspecialchars($html);
  return $s;
}