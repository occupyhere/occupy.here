<?php

class Admin_Auth {
  
  function __construct($passwd_file = '/etc/passwd') {
    $this->passwd_file = $passwd_file;
  }
  
  function check_login($username, $password) {
    if (defined('ADMIN_PASSWORD') && defined('ADMIN_USERNAME')) {
      if ($username == ADMIN_USERNAME &&
          $password == ADMIN_PASSWORD) {
        return true;
      }
    }
    if (file_exists($this->passwd_file)) {
      $file = file($this->passwd_file);
      foreach ($file as $line) {
        $len = strlen($username);
        if (substr($line, 0, $len + 1) == "$username:") {
          $end = strpos($line, ':', $len + 1);
          $hash = substr($line, $len + 1, $end - $len - 1);
          // TODO: are first 11 chars always the salt in OpenWrt?
          $salt = substr($hash, 0, 11);
          $check = crypt($password, $salt);
          return ($check == $hash);
        }
      }
    }
    return false;
  }
  
}

?>
