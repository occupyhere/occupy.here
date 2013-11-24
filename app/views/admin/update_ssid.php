<?php

if (!empty($_SESSION['is_admin']) && !empty($_POST['ssid'])) {
  if (file_exists('/etc/config/wireless')) {
    $ssid = $_POST['ssid'];
    exec("uci set wireless.@wifi-iface[0].ssid=$ssid");
    exec("uci commit wireless");
    exec('wifi down; wifi');
    $grid->log("Network SSID updated to '$ssid'");
  }
}

?>
