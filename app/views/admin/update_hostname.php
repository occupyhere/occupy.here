<?php

$hostname = HOSTNAME;

if (!empty($_SESSION['is_admin']) && !empty($_POST['hostname'])) {
  $hostname = $_POST['hostname'];
  $config = file_get_contents(GRID_DIR . '/config.php');
  $config = preg_replace("/(define\('HOSTNAME', )'([^']+)'/m", '$1' . "'$hostname'", $config);
  file_put_contents(GRID_DIR . '/config.php', $config);
  $grid->log("Hostname updated to '$hostname'");
}

header("Location: http://$hostname/admin");

?>
