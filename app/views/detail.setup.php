<?php

$id = "{$params['type']}.{$params['digits']}";
$item = $grid->db->record($params['type'], $id);
$replies = $grid->db->select('message', array(
  'where' => 'parent_id = ?',
  'values' => array($id),
  'order' => 'created'
));

?>
