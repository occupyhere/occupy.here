<?php

$body_class = "$body_class topic";

$post = $grid->db->record('message', $params['id']);
if (!empty($post->file_id)) {
  $post->attachment = $grid->db->record('file', $post->file_id);
}

$replies = get_posts(array(
  'where' => 'parent_id = ?',
  'values' => array($params['id']),
  'order' => 'created'
));

$post->reply_count = count($replies);

if (!empty($post->parent_id)) {
  if (substr($post->parent_id, 0, 2) == 'c/') {
    $container = $grid->db->record('container', substr($post->parent_id, 2));
    if ($container->id == 'library') {
      $container->name = _('Library');
    }
    $back_title = esc($container->name);
    $back_url = GRID_URL . $post->parent_id;
  } else {
    $back_title = _('In reply to');
    $back_url = GRID_URL . "p/$post->parent_id";
  }
} else {
  $back_title = _('Forum');
  $back_url = GRID_URL . 'forum';
}

if (!empty($_GET['delete'])) {
  if (!empty($post->file_id)) {
    $grid->db->delete('file', $post->file_id);
  }
  $grid->db->delete('message', $post->id);
  foreach ($replies as $reply) {
    $grid->db->delete('message', $reply->id);
  }
  $grid->response->redirect($back_url);
  exit;
}

?>
