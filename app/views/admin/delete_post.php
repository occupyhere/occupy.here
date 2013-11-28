<?php
if (!empty($_SESSION['is_admin']) && !empty($_POST['id'])) {
  $grid->db->query("
    DELETE FROM message
    WHERE id = ?
  ", array($_POST['id']));
  $attachments = $grid->db->select('file', array(
    'where' => 'message_id = ?',
    'values' => array($_POST['id'])
  ));
  if (!empty($attachments)) {
    $attachment = $attachments[0];
    $path = GRID_DIR . '/public/' . dirname($attachment->path);
    if (file_exists($path)) {
      exec("rm -rf $path");
    }
    $grid->db->query("
      DELETE FROM file
      WHERE message_id = ?
    ", array($_POST['id']));
  }
}

?>
