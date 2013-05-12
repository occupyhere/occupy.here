<?php

if (defined('HOSTNAME') && strtolower($_SERVER['HTTP_HOST']) != HOSTNAME) {
  $path = '';
  if (wispr_pong() == 'show-intro') {
    $path = 'intro';
  }
  $hostname = HOSTNAME;
  header("Location: http://$hostname/$path");
  exit;
}

$ssl_enabled = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ||
                $_SERVER['SERVER_PORT'] == 443);

if (empty($params['posted_before'])) {
  $posts = get_posts(array(
    'where' => "parent_id = 0",
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
} else {
  $posts = get_posts(array(
    'where' => "parent_id = 0 AND created < ?",
    'values' => array($params['posted_before']),
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
    WHERE parent_id = 0
      AND created < ?
  ", array($next_page));
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
    'where' => "parent_id = 0 AND created >= ?",
    'values' => array($params['posted_before']),
    'order' => 'created DESC',
    'limit' => $items_per_page
  ));
  if (!empty($prev_topics)) {
    $first_prev_item = $prev_topics[0];
    $prev_page = intval($first_prev_item->created) + 1;
  }
}

$back_url = null;

?>
