<?php

if (!empty($_SESSION['is_admin']) && !empty($_POST['default_path']) &&
    preg_match('#^(/[a-zA-Z0-9-]+)+$#', $_POST['default_path'])) {
  save_meta(array(
    'default_path' => $_POST['default_path']
  ));
}

$hostname = HOSTNAME;
header("Location: http://$hostname/admin");

?>
