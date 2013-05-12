<?php



$nolayout = array(
  'layout' => false
);

$this->get('/admin', 'admin/main');
$this->post('/admin', 'admin/login');
$this->post('/admin/logout', 'admin/logout');
$this->post('/admin/backup_download', 'admin/backup_download', $nolayout);
$this->post('/admin/upgrade', 'admin/upgrade', $nolayout);

?>
