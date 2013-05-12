<?php

if (empty($_SESSION['is_admin'])) {
  $this->partial('login_form');
} else {
  $this->partial('config_form');
}

?>
