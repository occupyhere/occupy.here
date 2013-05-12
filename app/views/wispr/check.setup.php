<?php

if (wispr_pong() != 'done') {
  $hostname = HOSTNAME;
  header("Location: http://$hostname/");
  wispr_ping();
  exit;
}

?>
