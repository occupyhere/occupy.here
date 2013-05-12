<?php

if (!empty($_POST['id']) && preg_match('/^[a-z-]+$/', $_POST['id'])) {
  if (!empty($_SESSION['hidden_announcements'])) {
    array_push($_SESSION['hidden_announcements'], $_POST['id']);
  } else {
    $_SESSION['hidden_announcements'] = array($_POST['id']);
  }
}

?>
