<?php

$id = uniqid('message.', true);
$now = time();

if ($params['task'] == 'preview') {
  $this->render('forum/preview_topic');
  exit;
}

$grid->db->insert('message', array(
  'id' => $id,
  'user_id' => $grid->user->id,
  'content' => $params['content'],
  'parent_id' => 0,
  'server_id' => $grid->meta['server_id'],
  'created' => $now,
  'updated' => $now
));

if (!empty($_POST['username']) && $grid->user->name != $_POST['username']) {
  $update = array(
    'name' => $_POST['username'],
    'updated' => $now
  );
  $grid->db->update('user', $update, $grid->user->id);
}

$this->redirect(GRID_URL);

?>
