<?php

if (empty($_SESSION['is_admin'])) {
  die(_('Sorry, backups are restricted to admin users'));
}

if (empty($params['file'])) {
  $now = time();
  $filename = GRID_DIR . "/public/occupy.here-$now.zip";
  $grid->log("Generating backup occupy.here-$now.zip");
  $zip = ZIP_BIN;
  if (!file_exists($zip) && file_exists('/opt/usr/bin/zip')) {
    $zip = '/opt/usr/bin/zip';
  }
  exec('cd ' . GRID_DIR . " && $zip -r $filename data/app.db data/app.log public/uploads", $output);
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
  $path = GRID_DIR . "/public/$file";
  if (file_exists($path)) {
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Last-Modified: ' . gmdate ('D, d M Y H:i:s', filemtime($path)) . ' GMT');
    header('Cache-Control: private', false);
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($path));
    header('Connection: close');
    $fh = fopen($path, 'r');
    while ($chunk = fread($fh, 1024)) {
      echo $chunk;
    }
    fclose($fh);
    unlink($path);
  }
  echo "<html><head></head><body><script>parent.backup_complete();</script></body></html>";
}

?>
