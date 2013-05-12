<?php

$id = generate_id('file');
$now = time();

$ext = '';
$dir = '00';
if (preg_match('/file\..+\.(\d\d)/', $id, $matches)) {
  $dir = $matches[1];
}
if (preg_match('/\.(.+)$/', $params['original'], $matches)) {
  $ext = '.' . strtolower($matches[1]);
  if ($ext == '.jpeg') {
    $ext = '.jpg';
  }
}

if (!file_exists(GRID_DIR . "/public/uploads/$dir")) {
  mkdir(GRID_DIR . "/public/uploads/$dir");
}
$path = "$dir/$id$ext";
$filename = str_replace('..', '', $params['filename']);
rename(GRID_DIR . "/public/uploads/tmp/$filename", GRID_DIR . "/public/uploads/$path");

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
