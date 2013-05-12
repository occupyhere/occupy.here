<?php

$body_class = 'not-found';
if (defined('HOSTNAME') && strtolower($_SERVER['HTTP_HOST']) != HOSTNAME) {
  $hostname = HOSTNAME;
  header("Location: http://$hostname/");
  exit;
}

?>
