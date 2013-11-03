<?php

if (!in_array('offline', $hidden_announcements)) {
  $has_seen = isset($_SESSION['has_seen_offline_announcement']);
  $_SESSION['has_seen_offline_announcement'] = true;
  $this->partial_for('announcements', 'announcements/offline');
}

/*if (!in_array('library', $hidden_announcements) && $this->view != 'library.php') {
  $now = time();
  $posts = get_posts(array(
    'where' => "parent_id = ? AND expires > ?",
    'values' => array('c/library', $now),
    'order' => 'created DESC',
    'limit' => 1
  ));
  $latest = $posts[0];
  $latest_url = "/p/$latest->id#post_$latest->id";
  $latest_title = $latest->content;
  $has_seen = isset($_SESSION['has_seen_library_announcement']);
  $_SESSION['has_seen_library_announcement'] = true;
  $this->partial_for('announcements', 'announcements/library');
}*/

$back_url = '';

?>
