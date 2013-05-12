<?php

$body_class = 'files';

$files = $grid->db->select('file', array(
  'order' => 'lower(name), created DESC'
));

$message_ids = array();
foreach ($files as $file) {
  $message_ids[] = $file->message_id;
}
$message_ids = "'" . implode("','", $message_ids) . "'";
$topics_query = $grid->db->query("
  SELECT *
  FROM message
  WHERE id IN ($message_ids)
");
$topics_list = $topics_query->fetchAll(PDO::FETCH_OBJ);

$topics = array();
foreach ($topics_list as $topic) {
  $topics[$topic->id] = $topic;
}

?>
