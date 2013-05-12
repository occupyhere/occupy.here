<?php

// Defaults
$page_title = 'occupy.here / distributed wifi occupation';
$body_class = '';

if (!empty($grid->user)) {
  $username = get_username($grid->user);
  $body_class .= "user_{$grid->user->id}";
} else {
  $username = 'Anonymous';
}

$items_per_page = 10;
$back_url = GRID_URL;
$back_title = 'Home';

$this->stylesheet('reset.css');
$this->stylesheet('open_sans/open_sans.css');
$this->stylesheet('occupy.here.css');

$this->javascript('mootools-core-1.4.5-full-compat-yc.js');
$this->javascript('mootools-more-1.4.0.1.js');
$this->javascript('manycopies.js');
$this->javascript('occupy.here.js');

$hidden_announcements = array();
if (!empty($_COOKIE['hidden_announcements'])) {
  $hidden_announcements = explode(',', $_COOKIE['hidden_announcements']);
}

if (!in_array('offline', $hidden_announcements)) {
  $this->partial_for('announcements', 'announcements/offline');
}

/*if (preg_match('/public$/', GRID_PATH)) {
  $this->partial_for('announcements', 'server_config');
}

if (!is_writable(GRID_DIR . '/data') || !is_writable(GRID_DIR . '/data/sessions')) {
  $this->partial_for('announcements', 'data_config');
}

if (!empty($_SESSION['hidden_announcements'])) {
  $this->partial_for('header', 'hidden_announcements');
}*/

$base_path = GRID_URL;
if (GRID_PATH == '/') {
  $base_path = substr($base_path, 0, -1);
}

?>
