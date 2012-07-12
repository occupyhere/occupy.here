<?php

if ($grid->user && $grid->user->name != $_POST['username']) {
  $now = time();
  $update = array(
    'name' => $_POST['username'],
    'bio' => $_POST['bio'],
    'updated' => $now
  );
  $grid->db->update('user', $update, $grid->user->id);
}

$this->redirect(GRID_URL);

?>
