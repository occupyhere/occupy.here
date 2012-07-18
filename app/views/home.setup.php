<?php

if (defined('CANONICAL_HOST') && strtolower($_SERVER['HTTP_HOST']) != CANONICAL_HOST) {
  $intro = (wispr_pong() == 'show-intro') ? 'intro' : '';
  header('Location: http://' . CANONICAL_HOST . '/' . $intro);
  exit;
}

$this->partial_for('announcements', 'introduction');

if (!empty($params['posted_before'])) {
  $topics = $grid->db->select('message', array(
    'where' => "parent_id = 0 AND created < ?",
    'values' => array($params['posted_before']),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
  $files = $grid->db->select('file', array(
    'where' => 'created < ?',
    'values' => array($params['posted_before']),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
} else {
  $topics = $grid->db->select('message', array(
    'where' => "parent_id = 0",
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
  $files = $grid->db->select('file', array(
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
}

function sort_by_created($a, $b) {
  if ($a->created == $b->created) {
    return 0;
  }
  return ($a->created < $b->created) ? 1 : -1;
}

$items = array_merge($topics, $files);
usort($items, 'sort_by_created');
$items = array_slice($items, 0, $items_per_page);

$lookup = array();
foreach ($items as $item) {
  $item->reply_count = 0;
  $lookup[$item->id] = $item;
}

if (!empty($items)) {
  $ids = array_keys($lookup);
  $ids = "'" . implode("','", $ids) . "'";
  $reply_query = $grid->db->query("
    SELECT parent_id, COUNT(id) AS reply_count
    FROM message
    WHERE parent_id IN ($ids)
    GROUP BY parent_id
  ");
  $replies = $reply_query->fetchAll(PDO::FETCH_OBJ);
  foreach ($replies as $messages) {
    $item = $lookup[$messages->parent_id];
    $item->reply_count = $messages->reply_count;
  }
}

$topic_count_query = $grid->db->query("
  SELECT COUNT(id)
  FROM message
  WHERE parent_id = 0
");
$topic_count = $topic_count_query->fetchColumn();

$file_count_query = $grid->db->query("
  SELECT COUNT(id)
  FROM file
");
$file_count = $file_count_query->fetchColumn();

$item_count = $topic_count + $file_count;

if (count($items) > $items_per_page) {
  $last_index = count($items) - 1;
  $last_item = $items[$last_index];
  $next_page = $last_item->created;
} else {
  $end_of_items = true;
}

?>
