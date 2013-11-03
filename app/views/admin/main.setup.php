<?php

/*global $grid;
$storage_path = '/';
if (!empty($grid->meta['storage_path'])) {
  $storage_path = $grid->meta['storage_path'];
}

$valid_mounts = array('/', '/usb');
$mount_aliases = array(
  '/overlay' => '/'
);

exec(DF_BIN . ' -h', $df_result);
$disks = array();
$found_usb = false;
foreach ($df_result as $i => $line) {
  $words = preg_split('/\s+/', $line);
  if ($i == 0) {
    $keys = $words;
  } else if (substr($words[0], 0, 4) == '/dev') {
    $disk = array();
    foreach ($keys as $j => $key) {
      if (substr($key, 0, 10) == 'Filesystem') {
        $key = 'filesystem';
      } else if (substr($key, 0, 5) == 'Mount') {
        $key = 'mount';
        $mount = $words[$j];
        if (!empty($mount_aliases[$mount])) {
          $mount = $mount_aliases[$mount];
        }
        if ($mount == $storage_path) {
          $disk['selected'] = true;
        }
        if ($words[$j] == '/usb') {
          $found_usb = true;
        }
        $words[$j] = $mount;
      } else if (substr($key, 0, 4) == 'Size') {
        $key = 'size';
      } else if (substr($key, 0, 5) == 'Avail') {
        $key = 'avail';
      }
      if (isset($words[$j])) {
        $disk[$key] = $words[$j];
      }
    }
    if (in_array($disk['mount'], $valid_mounts)) {
      $disks[] = $disk;
    }
  }
}

if (!$found_usb) {
  $disks[] = array(
    'mount' => '/usb',
    'filesystem' => '',
    'disabled' => true
  );
}*/

?>
