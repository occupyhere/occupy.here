<?php

$vars = array(
  'status' => 'done'
);
$grid->db->update('wispr', $vars, $_SERVER['REMOTE_ADDR']);

?>
