<?php

if (defined('CANONICAL_HOST') && strtolower($_SERVER['HTTP_HOST']) != CANONICAL_HOST) {
  $intro = (wispr_pong() == 'show-intro') ? 'intro' : '';
  header('Location: http://' . CANONICAL_HOST . '/' . $intro);
  exit;
}

$this->partial_for('announcements', 'introduction');

$topics = $grid->db->select('message', array(
  'where' => 'parent_id = 0',
  'order' => 'created DESC',
  'limit' => $items_per_page
));

$topic_ids = array();
$topic_lookup = array();
foreach ($topics as $topic) {
  $topic->reply_count = 0;
  $topic_ids[] = $topic->id;
  $topic_lookup[$topic->id] = $topic;
}
if (!empty($topic_ids)) {
  $topic_ids = "'" . implode("','", $topic_ids) . "'";
  $reply_query = $grid->db->query("
    SELECT parent_id, COUNT(id) AS reply_count
    FROM message
    WHERE parent_id IN ($topic_ids)
    GROUP BY parent_id
  ");
  $replies = $reply_query->fetchAll(PDO::FETCH_OBJ);
  foreach ($replies as $messages) {
    $topic = $topic_lookup[$messages->parent_id];
    $topic->reply_count = $messages->reply_count;
  }
}

$topic_count_query = $grid->db->query("
  SELECT COUNT(id)
  FROM message
  WHERE parent_id = 0
");
$topic_count = $topic_count_query->fetchColumn();

if ($topic_count > $items_per_page) {
  $next_page = 'forum?p=2';
} else {
  $end_of_messages = true;
}

$files = $grid->db->select('file', array(
  'order' => 'created DESC',
  'limit' => $items_per_page
));

$file_ids = array();
$file_lookup = array();
foreach ($files as $file) {
  $file->reply_count = 0;
  $file_ids[] = $file->id;
  $file_lookup[$file->id] = $file;
}
if (!empty($file_ids)) {
  $file_ids = "'" . implode("','", $file_ids) . "'";
  $reply_query = $grid->db->query("
    SELECT parent_id, COUNT(id) AS reply_count
    FROM message
    WHERE parent_id IN ($file_ids)
    GROUP BY parent_id
  ");
  $replies = $reply_query->fetchAll(PDO::FETCH_OBJ);
  foreach ($replies as $messages) {
    $file = $topic_lookup[$messages->parent_id];
    $file->reply_count = $messages->reply_count;
  }
}

$file_count_query = $grid->db->query("
  SELECT COUNT(id)
  FROM file
");
$file_count = $file_count_query->fetchColumn();

if ($file_count > $items_per_page) {
  $media_next_page = 'media?p=2';
} else {
  $end_of_files = true;
}

?>
