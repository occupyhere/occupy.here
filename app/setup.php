<?php

global $grid;
define('REVISION', 4);
require_once dirname(__FILE__) . '/functions.php';

$this->db = new Grid_Database();
setup_meta();
//setup_library();
check_for_expired_content();

if (!empty($_GET['import'])) {
  check_for_import_content();
}

$grid->add_event('setup_response', 'setup_user');
$grid->add_event('page_load', 'check_for_ssl');

$no_layout = array(
  'layout' => false
);

// WISPR is Apple's wifi detection mechanism
//$this->get('/intro', 'wispr/intro');
//$this->get('/library/test/success.html', 'wispr/check', $no_layout);
//$this->post('/wispr_done', 'wispr/done', $no_layout);
$this->get('/library/test/success.html', 'wispr/success', $no_layout);

$this->get('/', 'home');
$this->get('/forum', 'forum');
$this->get('/forum/(\d+)', 'forum', array(
  'vars' => array('posted_before')
));

$this->get('/about', 'about');
$this->get('/files', 'files');
$this->get('/account', 'account');
$this->get('/logout', 'logout');
$this->get('/p/$id', 'topic');
$this->get('/p/$id/edit', 'edit_post');
$this->get('/u/$id', 'user');
$this->get('/u/$id/$posted_before', 'user');
$this->get('/c/library', 'library');
$this->get('/c/$id', 'container');

$this->post('/api/post_topic', 'api/post_topic', $no_layout);
$this->post('/api/post_reply', 'api/post_reply', $no_layout);
$this->post('/api/update_account', 'api/update_account', $no_layout);
$this->post('/api/set_time', 'api/set_time', $no_layout);
$this->post('/api/sync_data', 'api/sync_data', $no_layout);
$this->post('/api/preview', 'api/preview', $no_layout);
$this->post('/api/upload_file', 'api/upload_file', $no_layout);
$this->post('/api/post_file', 'api/post_file', $no_layout);
$this->post('/api/backup', 'api/backup', $no_layout);
$this->post('/api/hide_announcement', 'api/hide_announcement', $no_layout);
$this->get('/api/enable_ssl', 'api/enable_ssl', $no_layout);

?>
