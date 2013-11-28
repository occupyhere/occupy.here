<?php

$back_url = null;

$body_class .= ' files';
$parent_id = 'c/library';
$now = time();

if (defined('READABILITY_API_KEY')) {
  $post_title = _('Import');
} else {
  $post_url = null;
}

check_for_import_content();

$posts = get_posts(array(
  'where' => "parent_id = ? AND expires > ?",
  'values' => array($parent_id, $now),
  'order' => 'created DESC'
));

?>
