<?php

$id = uniqid('file.', true);
$now = time();

$ext = '';
$dir = '000';
if (preg_match('/file\..+\.(\d\d\d)/', $id, $matches)) {
  $dir = $matches[1];
}
if (preg_match('/\.(.+)$/', $params['original'], $matches)) {
  $ext = '.' . strtolower($matches[1]);
  if ($ext == '.jpeg') {
    $ext = '.jpg';
  }
}

if (!file_exists(GRID_DIR . "/data/media/$dir")) {
  mkdir(GRID_DIR . "/data/media/$dir");
}
$path = "$dir/$id$ext";
rename(GRID_DIR . "/data/tmp/{$params['filename']}", GRID_DIR . "/data/media/$path");

$grid->db->insert('file', array(
  'id' => $id,
  'user_id' => $grid->user->id,
  'name' => $params['name'],
  'type' => $params['type'],
  'path' => $path,
  'original' => $params['original'],
  'server_id' => $grid->meta['server_id'],
  'created' => $now,
  'updated' => $now
));

if (!empty($_POST['username']) && $grid->user->name != $_POST['username']) {
  $update = array(
    'name' => $_POST['username'],
    'updated' => $now
  );
  $grid->db->update('user', $update, $grid->user->id);
}

$this->redirect(GRID_URL);

?>
