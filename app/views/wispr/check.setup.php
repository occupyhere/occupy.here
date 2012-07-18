<?php

if (wispr_pong() != 'done') {
  header('Location: http://' . CANONICAL_HOST . '/');
  wispr_ping();
  exit;
}

?>
