<?php

$back_url = null;

$body_class .= ' files';
$parent_id = 'c/library';
$now = time();

$posts = get_posts(array(
  'where' => "parent_id = ? AND expires > ?",
  'values' => array($parent_id, $now),
  'order' => 'created DESC'
));

?>
