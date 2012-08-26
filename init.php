<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ||
             $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

define('GRID_DIR', dirname(__FILE__));
define('GRID_PATH', dirname($_SERVER['PHP_SELF']));
define('GRID_URL', $protocol . $_SERVER['HTTP_HOST'] . GRID_PATH);

if (!file_exists(GRID_DIR . '/config.php')) {
  if (is_writable(GRID_DIR)) {
    copy(GRID_DIR . '/config-example.php', GRID_DIR . '/config.php');
  } else {
    die("Please create config.php (see: config-example.php).");
  }
}

require_once GRID_DIR . '/config.php';
require_once GRID_DIR . '/library/autoload.php';

chdir(GRID_DIR);
$offset = (GRID_PATH == '/') ? 1 : strlen(GRID_PATH);
$url = parse_url($_SERVER['REQUEST_URI']);

$request_path = substr($url['path'], $offset);
if (GRID_PATH != '/' && $request_path != '/') {
  $request_path = substr($request_path, 1);
}

global $grid;
$grid = new Grid();
$grid->setup($request_path, $_SERVER['REQUEST_METHOD']);
$grid->main();

?>
