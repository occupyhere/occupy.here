<?php

if (!empty($params['posted_before'])) {
  $posts = get_posts(array(
    'where' => 'user_id = ? AND created < ?',
    'values' => array($params['id'], $params['posted_before']),
    'order' => 'created DESC',
    'limit' => 10
  ));
} else {
  $posts = get_posts(array(
    'where' => 'user_id = ?',
    'values' => array($params['id']),
    'order' => 'created DESC',
    'limit' => 10
  ));
}

if (count($posts) == $items_per_page) {
  $last_index = count($posts) - 1;
  $last_post = $posts[$last_index];
  $next_page = "u/{$params['id']}/$last_post->created";
  $next_topics_query = $grid->db->query("
    SELECT COUNT(id)
    FROM message
    WHERE user_id = ?
      AND created < ?
  ", array($params['id'], $next_page));
  $next_topics = $next_topics_query->fetchColumn();
  if ($next_topics == 0) {
    $next_page = null;
    $end_of_items = true;
  }
} else {
  $end_of_items = true;
}

if (!empty($params['posted_before'])) {
  $prev_topics = $grid->db->select('message', array(
    'where' => "user_id = ? AND created >= ?",
    'values' => array($params['id'], $params['posted_before']),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
  if (!empty($prev_topics)) {
    $first_prev_item = $prev_topics[0];
    $prev_page = "u/{$params['id']}/" . (intval($first_prev_item->created) + 1);
  }
}

?>
