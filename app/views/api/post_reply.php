<?php

$id = uniqid('message.', true);
$now = time();

$grid->db->insert('message', array(
  'id' => $id,
  'user_id' => $grid->user->id,
  'content' => $params['content'],
  'parent_id' => $params['parent_id'],
  'server_id' => $grid->meta['server_id'],
  'created' => $now,
  'updated' => $now
));

$query = $grid->db->query("
  SELECT COUNT(*)
  FROM message
  WHERE parent_id = ?
", array($params['parent_id']));

$update_reply_count = array(
  'reply_count' => $query->fetchColumn(0)
);

$grid->db->update('message', $update_reply_count, $params['parent_id']);

if (isset($_POST['username']) && $grid->user->name != $_POST['username']) {
  $update = array(
    'name' => $_POST['username'],
    'updated' => $now
  );
  $grid->db->update('user', $update, $grid->user->id);
}

$this->redirect(GRID_PATH . "{$params['parent_id']}#replies");

?>
