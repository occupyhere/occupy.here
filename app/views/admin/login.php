<?php

if (!defined('PASSWD_PATH')) {
  define('PASSWD_PATH', '/etc/shadow');
}

if (!empty($_POST['admin_username']) && !empty($_POST['admin_password'])) {
  $username = strtolower($_POST['admin_username']);
  $password = $_POST['admin_password'];
  $auth = new Admin_Auth(PASSWD_PATH);
  if ($auth->check_login($username, $password)) {
    $_SESSION['is_admin'] = true;
    $grid->log("Admin login by {$_SERVER['REMOTE_ADDR']}");
    header('Location: ' . GRID_URL . '/admin');
    exit;
  } else {
    $_SESSION['is_admin'] = false;
    $this->partial('login_form', array(
      'feedback' => 'Sorry that login didn’t match any known account.'
    ));
  }
} else {
  $_SESSION['is_admin'] = false;
  $this->partial('login_form', array(
    'feedback' => 'You need to provide a username and password.'
  ));
  $grid->log('Failed admin login attempt');
}

?>
