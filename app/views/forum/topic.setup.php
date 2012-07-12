<?php

$topic = $grid->db->record('message', $params['id']);
$replies = $grid->db->select('message', array(
  'where' => 'parent_id = ?',
  'values' => array($params['id']),
  'order' => 'created'
));

?>
