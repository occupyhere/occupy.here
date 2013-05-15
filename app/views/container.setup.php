<?php

$parent_id = "c/{$params['id']}";
$container = $grid->db->record('container', $params['id']);

if (empty($container)) {
  $grid->response->redirect(GRID_URL);
  exit;
}

?>
