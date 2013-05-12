<?php

if (empty($_SESSION['is_admin'])) {
  die('Sorry, backups are restricted to admin users');
}

if (empty($params['file'])) {
  $grid->log('Generating backup');
  $now = time();
  $filename = GRID_DIR . "/public/uploads/occupy.here-$now.zip";
  $uploads_bak = (file_exists(GRID_DIR . '/public/uploads.bak')) ? ' public/uploads.bak' : '';
  exec('cd ' . GRID_DIR . ' && ' . ZIP_BIN . " -r $filename data/app.db data/app.log public/uploads$uploads_bak", $output);
  
  if (file_exists($filename)) {
    $response = (object) array(
      'status' => 'ok',
      'file' => "occupy.here-$now.zip"
    );
  } else {
    $response = (object) array(
      'status' => 'error',
      'output' => join("\n", $output)
    );
  }
  
  header('Content-Type: application/json');
  echo json_encode($response);
} else {
  $file = str_replace('..', '', $params['file']);
  $path = GRID_DIR . "/public/uploads/$file";
  if (file_exists($path)) {
    header('Content-Type: application/zip');
    $fh = fopen($path, 'r');
    while ($chunk = fread($fh, 1024)) {
      echo $chunk;
    }
    fclose($fh);
  }
  unlink($path);
}

?>
