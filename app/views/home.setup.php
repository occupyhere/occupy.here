<?php

if (defined('CANONICAL_HOST') && strtolower($_SERVER['HTTP_HOST']) != CANONICAL_HOST) {
  $intro = (wispr_pong() == 'show-intro') ? 'intro' : '';
  header('Location: http://' . CANONICAL_HOST . '/' . $intro);
  exit;
}

$this->partial_for('announcements', 'introduction');

if (!empty($params['posted_before'])) {
  $topics_query = array(
    'where' => "parent_id = 0 AND created < ?",
    'values' => array($params['posted_before']),
    'order' => 'created DESC',
    'limit' => $items_per_page
  );
  $files_query = array(
    'where' => 'created < ?',
    'values' => array($params['posted_before']),
    'order' => 'created DESC',
    'limit' => $items_per_page
  );
} else {
  $topics_query = array(
    'where' => "parent_id = 0",
    'order' => 'created DESC',
    'limit' => $items_per_page
  );
  $files_query = array(
    'order' => 'created DESC',
    'limit' => $items_per_page
  );
}

$topics = $grid->db->select('message', $topics_query);
$files = $grid->db->select('file', $files_query);

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

if (count($items) == $items_per_page) {
  $last_index = count($items) - 1;
  $last_item = $items[$last_index];
  $next_page = $last_item->created;
  $next_topics_query = $grid->db->query("
    SELECT COUNT(id)
    FROM message
    WHERE parent_id = 0
      AND created < ?
  ", array($next_page));
  $next_topics = $next_topics_query->fetchColumn();
  $next_files_query = $grid->db->query("
    SELECT COUNT(id)
    FROM file
    WHERE created < ?
  ", array($next_page));
  $next_files = $next_files_query->fetchColumn();
  $next_items = $next_topics + $next_files;
  if ($next_items == 0) {
    $next_page = null;
    $end_of_items = true;
  }
} else {
  $end_of_items = true;
}

if (!empty($params['posted_before'])) {
  $prev_topics = $grid->db->select('message', array(
    'where' => "parent_id = 0 AND created >= ?",
    'values' => array($params['posted_before']),
    'order' => 'created',
    'limit' => $items_per_page
  ));
  $prev_files = $grid->db->select('file', array(
    'where' => 'created >= ?',
    'values' => array($params['posted_before']),
    'order' => 'created',
    'limit' => $items_per_page
  ));
  $prev_items = array_merge($prev_topics, $prev_files);
  if (!empty($prev_items)) {
    usort($prev_items, 'sort_by_created');
    $prev_items = array_slice($prev_items, 0, $items_per_page);
    $first_prev_item = $prev_items[0];
    $prev_page = $first_prev_item->created + 1;
  }
}

?>
