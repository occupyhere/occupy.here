<?php

function __autoload($class) {
  $base = dirname(__FILE__);
  if (preg_match_all('/([A-Z]+[a-z]*)/', $class, $matches)) {
    $path = $matches[0];
    if (count($path) == 1) {
      $path[] = $path[0];
    }
    $filename = strtolower(implode('/', $path)) . '.php';
    if (file_exists("$base/$filename")) {
      include_once "$base/$filename";
    }
  }
}

?>
