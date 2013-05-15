<?php

$id = generate_id();
$now = time();
$expires = $now + intval($_POST['ttl']);

$attachment = (empty($_POST['attachment'])) ? null : $_POST['attachment'];

$grid->db->insert('message', array(
  'id' => $id,
  'user_id' => $grid->user->id,
  'content' => $params['content'],
  'parent_id' => 0,
  'server_id' => $grid->meta['server_id'],
  'file_id' => $attachment,
  'expires' => $expires,
  'created' => $now,
  'updated' => $now
));

update_user();

if (!empty($attachment)) {
  attach_file($id, $attachment);
}

$container = get_container();
$url = (empty($container)) ? GRID_URL : GRID_URL . "c/$container->id";

if (!empty($container)) {
  $grid->db->update('message', array(
    'parent_id' => "c/$container->id"
  ), $id);
  $grid->db->update('container', array(
    'updated' => $now
  ), $container->id);
}

$this->redirect($url);
exit;

?>
