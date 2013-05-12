<?php

$_SESSION['always_use_ssl'] = true;
if (isset($_SERVER['HTTP_REFERER'])) {
  $url = str_replace('http:', 'https:', $_SERVER['HTTP_REFERER']);
} else {
  $url = str_replace('http:', 'https:', GRID_URL);
}
header("Location: $url");

?>
