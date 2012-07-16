<?php

$page = empty($_GET['p']) ? 1 : intval($_GET['p']);

$topics = $grid->db->select('message', array(
  'where' => 'parent_id = 0',
  'order' => 'created DESC',
  'limit' => $items_per_page,
  'offset' => ($page - 1) * $items_per_page
));

$topic_count_query = $grid->db->query("
  SELECT COUNT(id)
  FROM message
  WHERE parent_id = 0
");
$topic_count = $topic_count_query->fetchColumn();

if ($topic_count > $items_per_page * $page) {
  $next_page = 'forum?p=2';
} else if (!empty($topics)) {
  $end_of_messages = true;
}

if ($page > 1) {
  $prev_page = 'forum?p=' . ($page - 1);
}

?>
