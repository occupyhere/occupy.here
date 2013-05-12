<?php

if (empty($_SESSION['is_admin'])) {
  die('You must login as an admin user.');
}

$storage_path = '/';
if (!empty($grid->meta['storage_path'])) {
  $storage_path = $grid->meta['storage_path'];
}
$old_storage_path = $storage_path;

if (!empty($_POST['storage_path'])) {
  
  $storage_path = $_POST['storage_path'];
  
  if ($storage_path == $old_storage_path) {
    die('Storage path unchanged!');
  }
  
  $grid->log("Setting storage path to $storage_path");
  
  save_meta(array(
    'storage_path' => $storage_path
  ));
  
  if ($storage_path == '/') {
    $storage_path = '';
  }
  
  if (!file_exists("$storage_path/occupy.here/data/incoming")) {
    exec("mkdir -p $storage_path/occupy.here/data/incoming", $result);
  }
  if (!file_exists("$storage_path/occupy.here/public/uploads")) {
    exec("mkdir -p $storage_path/occupy.here/public/uploads", $result);
  }
  
  if (is_link('/occupy.here/data/incoming')) {
    unlink('/occupy.here/data/incoming');
  } else {
    rename('/occupy.here/data/incoming', '/occupy.here/data/incoming.bak');
  }
  
  if ($storage_path == '') {
    if (file_exists("/occupy.here/data/incoming.bak")) {
      rename("/occupy.here/data/incoming.bak", "/occupy.here/data/incoming");
    } else {
      mkdir("/occupy.here/data/incoming");
    }
  } else {
    symlink("$storage_path/occupy.here/data/incoming", '/occupy.here/data/incoming');
  }
  
  if (is_link('/occupy.here/public/uploads')) {
    unlink('/occupy.here/public/uploads');
  } else {
    rename('/occupy.here/public/uploads', '/occupy.here/public/uploads.bak');
  }
  
  if ($storage_path == '') {
    if (file_exists("/occupy.here/public/uploads.bak")) {
      rename("/occupy.here/public/uploads.bak", "/occupy.here/public/uploads");
    } else {
      mkdir("/occupy.here/public/uploads");
    }
  } else {
    symlink("$storage_path/occupy.here/public/uploads", '/occupy.here/public/uploads');
  }
  
  echo "Storage path updated.";
}

?>
