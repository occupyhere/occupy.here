<?php

$file = $grid->db->record('file', $params['id']);
$replies = $grid->db->select('file', array(
  'where' => 'parent_id = ?',
  'values' => array($params['id']),
  'order' => 'created'
));

?>
