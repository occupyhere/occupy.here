<?php

$page = empty($_GET['p']) ? 1 : intval($_GET['p']);

$files = $grid->db->select('file', array(
  'order' => 'created DESC',
  'limit' => $items_per_page,
  'offset' => ($page - 1) * $items_per_page
));

$file_count_query = $grid->db->query("
  SELECT COUNT(id)
  FROM file
");
$file_count = $file_count_query->fetchColumn();

if ($file_count > $items_per_page * $page) {
  $media_next_page = 'media?p=2';
} else if (!empty($files)) {
  $end_of_files = true;
}

if ($page > 1) {
  $media_prev_page = 'media?p=' . ($page - 1);
}

?>
