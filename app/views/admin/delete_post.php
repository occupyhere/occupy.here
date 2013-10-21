<?php

if (!empty($_SESSION['is_admin']) && !empty($_POST['id'])) {
  $grid->db->query("
    DELETE FROM message
    WHERE id = ?
  ", array($_POST['id']));
}

?>
