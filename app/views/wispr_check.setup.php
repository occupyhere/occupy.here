<?php

if (wispr_pong() != 'done') {
  header('Location: http://occupy.here/');
  wispr_ping();
  exit;
}

?>
