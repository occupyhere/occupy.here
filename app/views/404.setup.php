<?php

if (defined('CANONICAL_HOST') && strtolower($_SERVER['HTTP_HOST']) != CANONICAL_HOST) {
  header('Location: http://' . CANONICAL_HOST . '/');
  exit;
}

?>
