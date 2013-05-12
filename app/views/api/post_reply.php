<?php

$id = generate_id();
$now = time();

$attachment = (empty($_POST['attachment'])) ? null : $_POST['attachment'];

$grid->db->insert('message', array(
  'id' => $id,
  'user_id' => $grid->user->id,
  'content' => $params['content'],
  'parent_id' => $params['parent_id'],
  'server_id' => $grid->meta['server_id'],
  'file_id' => $attachment,
  'created' => $now,
  'updated' => $now
));

update_user();
if (!empty($attachment)) {
  attach_file($id, $attachment);
}

$this->redirect(GRID_PATH . "p/{$params['parent_id']}#post_$id");

?>
