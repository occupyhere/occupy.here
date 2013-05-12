<?php

if (empty($grid->user->admin)) {
  $this->partial('admin_login');
} else {
  $this->partial('admin_config');
}

?>
