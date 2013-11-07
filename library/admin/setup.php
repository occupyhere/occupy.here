<?php

$nolayout = array(
  'layout' => false
);

$this->get('/admin', 'admin/main');
$this->post('/admin/login', 'admin/login', $nolayout);
$this->post('/admin/logout', 'admin/logout');
$this->post('/admin/backup_download', 'admin/backup_download', $nolayout);
//$this->post('/admin/upgrade', 'admin/upgrade', $nolayout);
$this->post('/admin/update_ssid', 'admin/update_ssid', $nolayout);
$this->post('/admin/update_hostname', 'admin/update_hostname', $nolayout);
$this->post('/admin/update_network', 'admin/update_network', $nolayout);
$this->post('/admin/delete_post', 'admin/delete_post', $nolayout);

?>
