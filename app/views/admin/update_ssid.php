<?php

if (!empty($_SESSION['is_admin']) && !empty($_POST['ssid'])) {
  if (file_exists('/etc/config/wireless')) {
    $ssid = $_POST['ssid'];
    $wireless = file_get_contents('/etc/config/wireless');
    $wireless = preg_replace('/(option ssid\s*).+$/m', '$1' . "'$ssid'", $wireless);
    file_put_contents('/etc/config/wireless', $wireless);
    exec('wifi down; wifi');
    $grid->log("Network SSID updated to '$ssid'");
  }
}

?>
