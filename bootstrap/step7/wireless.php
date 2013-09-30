<?php

exec('ifconfig', $ifconfig);
$ifconfig = join("\n", $ifconfig);
$wireless = file_get_contents('/etc/config/wireless');
if (preg_match('/HWaddr\s(.+)$/m', $ifconfig, $matches)) {
  $mac = str_replace(':', '', $matches[1]);
  $ssid = "OCCUPY.HERE / $mac";
  $wireless = preg_replace('/(option ssid\s*)OpenWrt/', '$1' . "'$ssid'", $wireless);
  $wireless = preg_replace('/(option disabled\s*1)/', '# $1', $wireless);
  file_put_contents('/etc/config/wireless', $wireless);
  exec('wifi down; wifi');
}

?>
