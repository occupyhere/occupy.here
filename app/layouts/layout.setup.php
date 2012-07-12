<?php

// Defaults
$page_title = 'occupy.here - distributed wifi occupation';
$body_class = '';
$username = get_username($grid->user);
$messages_per_page = 10;

//$this->stylesheet('nokia/stylesheet.css');
$this->javascript('mootools-core-1.4.5-full-compat-yc.js', 'header');
$this->javascript('mootools-more-1.4.0.1.js', 'header');
$this->javascript('functions.js', 'header');

$this->stylesheet('reset.css');
$this->stylesheet('occupy.here.css');
$this->javascript('manycopies.js');
$this->javascript('occupy.here.js');

if (preg_match('/public$/', GRID_PATH)) {
  $this->partial_for('announcements', 'server_config');
}

if (!is_writable(GRID_DIR . '/data') || !is_writable(GRID_DIR . '/data/sessions')) {
  $this->partial_for('announcements', 'data_config');
}

$base_path = GRID_URL;
if (GRID_PATH == '/') {
  $base_path = substr($base_path, 0, -1);
}

?>
