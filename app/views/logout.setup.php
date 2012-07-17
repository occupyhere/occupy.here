<?php

@session_start();
session_destroy();
setcookie('SESSION', '', time() - 1000);

$this->redirect(GRID_URL);

?>
