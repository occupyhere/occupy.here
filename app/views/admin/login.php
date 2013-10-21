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
    header('Location: ' . GRID_URL . 'admin');
    exit;
  } else {
    $_SESSION['is_admin'] = false;
    header('Location: ' . GRID_URL . 'admin?error=1');
    $grid->log("Failed admin login attempt by {$_SERVER['REMOTE_ADDR']}");
    exit;
  }
} else {
  $_SESSION['is_admin'] = false;
  header('Location: ' . GRID_URL . '/admin?error=1');
  $grid->log("Failed admin login attempt by {$_SERVER['REMOTE_ADDR']}");
}

?>
