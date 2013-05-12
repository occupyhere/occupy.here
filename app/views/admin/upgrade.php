<?php

set_time_limit(0);

if (empty($_SESSION['is_admin'])) {
  die('You must login as an admin user.');
}

if (empty($_FILES['file'])) {
  die('You are currently running revision ' . REVISION . '.');
}

?>
<html>
  <body>
    <?php
    
    function esc($value) {
      $value = str_replace("'", "\\'", $value);
      $value = htmlentities($value);
      echo $value;
    }
    
    function upgrade_dir($from, $to) {
      global $grid;
      $grid->log("Upgrade: copying $from to $to");
      if (!file_exists($to)) {
        mkdir($to);
      }
      $dh = opendir($from);
      while ($file = readdir($dh)) {
        if ($file == '.' || $file == '..') {
          continue;
        }
        if (is_dir("$from/$file")) {
          upgrade_dir("$from/$file", "$to/$file");
        } else {
          copy("$from/$file", "$to/$file");
        }
      }
    }
    
    $response = 'ok';
    try {
      $base = dirname(GRID_DIR);
      $upgrade = "$base/occupy.here-new";
      
      setup_uploads();
      $grid->log('Upgrade: moving uploaded file to ' . GRID_DIR . "/public/uploads/tmp/occupy.here.zip");
      move_uploaded_file($_FILES['file']['tmp_name'], GRID_DIR . "/public/uploads/tmp/occupy.here.zip");
      
      if (file_exists(GRID_DIR . "/public/uploads/tmp/occupy.here")) {
        $grid->log("Upgrade: removing existing directory " . GRID_DIR . "/public/uploads/tmp/occupy.here");
        exec('rm -rf ' . GRID_DIR . "/public/uploads/tmp/occupy.here", $result);
      }
      
      $grid->log('Upgrade: unzipping file ' . GRID_DIR . "/public/uploads/tmp/occupy.here.zip");
      exec("cd " . GRID_DIR . "/public/uploads/tmp && " . UNZIP_BIN . " " . GRID_DIR . "/public/uploads/tmp/occupy.here.zip", $result);
      
      upgrade_dir(GRID_DIR . "/public/uploads/tmp/occupy.here", GRID_DIR);
      
      $grid->log('Upgrade: removing directory ' . GRID_DIR  . "/public/uploads/tmp/occupy.here");
      exec('rm -rf ' . GRID_DIR  . "/public/uploads/tmp/occupy.here", $result);
      
      $grid->log('Upgrade: removing zip file ' . GRID_DIR  . "/public/uploads/tmp/occupy.here.zip");
      unlink(GRID_DIR . "/public/uploads/tmp/occupy.here.zip");
      
      if (file_exists(GRID_DIR . '/library/post-upgrade.php')) {
        $grid->log('Upgrade: running post-upgrade.php');
        require_once GRID_DIR . '/library/post-upgrade.php';
      }
      
    } catch (Exception $e) {
      $message = $e->getMessage();
      $grid->log("Upgrade error: $message");
      $response = "There was an problem upgrading. Please check ‘data/app.log’.";
    }

    ?>
    <script>
    
    parent.upgrade_feedback('<?php echo esc($response); ?>');
    
    </script>
  </body>
</html>
