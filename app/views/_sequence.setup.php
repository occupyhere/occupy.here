<?php

$now = time();
if (empty($params['posted_before'])) {
  $posts = get_posts(array(
    'where' => "parent_id = ? AND expires > ?",
    'values' => array($parent_id, $now),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
} else {
  $posts = get_posts(array(
    'where' => "parent_id = ? AND created < ? AND expires > ?",
    'values' => array($parent_id, $params['posted_before'], $now),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
}

if (count($posts) == $items_per_page) {
  $last_index = count($posts) - 1;
  $last_post = $posts[$last_index];
  $next_page = $last_post->created;
  $next_topics_query = $grid->db->query("
    SELECT COUNT(id)
    FROM message
    WHERE parent_id = ?
      AND created < ?
  ", array($parent_id, $next_page));
  $next_topics = $next_topics_query->fetchColumn();
  if ($next_topics == 0) {
    $next_page = null;
    $end_of_items = true;
  } else {
    $next_page = "/forum/$next_page";
  }
} else {
  $end_of_items = true;
}

if (!empty($params['posted_before'])) {
  $prev_topics = $grid->db->select('message', array(
    'where' => "parent_id = ? AND created >= ? AND expires > ?",
    'values' => array($parent_id, $params['posted_before'], $now),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
  if (!empty($prev_topics)) {
    $first_prev_item = $prev_topics[0];
    $prev_page = "/forum/" . intval($first_prev_item->created) + 1;
  }
}

?>
