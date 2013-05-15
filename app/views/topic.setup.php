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
    $back_title = esc($container->name);
    $back_url = $post->parent_id;
  } else {
    $back_title = 'In reply to';
    $back_url = "p/$post->parent_id";
  }
} else {
  $back_title = 'Home';
  $back_url = GRID_URL;
}

?>
