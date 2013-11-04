<?php

$now = time();
if (empty($params['posted_before'])) {
  $posts = get_posts(array(
    'where' => "$where AND expires > ?",
    'values' => array($value, $now),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
} else {
  $posts = get_posts(array(
    'where' => "$where AND created < ? AND expires > ?",
    'values' => array($value, $params['posted_before'], $now),
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
    WHERE $where
      AND created < ?
  ", array($value, $next_page));
  $next_topics = $next_topics_query->fetchColumn();
  if ($this->view == 'user.php') {
    $base = "u/{$params['id']}";
  } else if ($this->view == 'container.php') {
    $base = "c/{$params['id']}";
  } else {
    $base = 'forum';
  }
  if ($next_topics == 0) {
    $next_page = null;
    $end_of_items = true;
  } else {
    $next_page = "$base/$next_page";
  }
} else {
  $end_of_items = true;
}

if (!empty($params['posted_before'])) {
  $prev_topics = $grid->db->select('message', array(
    'where' => "$where AND created >= ? AND expires > ?",
    'values' => array($value, $params['posted_before'], $now),
    'order' => 'created',
    'limit' => $items_per_page
  ));
  if (!empty($prev_topics)) {
    if ($this->view == 'user.php') {
      $base = "u/{$params['id']}";
    } else if ($this->view == 'container.php') {
      $base = "c/{$params['id']}";
    } else {
      $base = 'forum';
    }
    $first_prev_item = array_pop($prev_topics);
    $prev_page = "$base/" . (intval($first_prev_item->created) + 1);
  }
}

?>
