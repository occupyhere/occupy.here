<html>
  <body>
    <?php
    
    function esc($value) {
      $value = str_replace("'", "\\'", $value);
      $value = htmlentities($value);
      echo $value;
    }
    
    extract($_FILES['file']);
    $id = generate_id();
    $now = time();
    $path = "uploads/tmp/$name";
    $grid->db->insert('file', array(
      'id' => $id,
      'user_id' => $_SESSION['user_id'],
      'server_id' => $grid->meta['server_id'],
      'name' => $name,
      'path' => $path,
      'created' => $now,
      'updated' => $now
    ));
    
    $file = $grid->db->record('file', $id);
    $json = json_encode($file);
    
    setup_uploads();
    move_uploaded_file($tmp_name, GRID_DIR . "/public/$path");
    
    ?>
    <script>
    
    parent.upload_complete('<?php echo str_replace("'", "\'", $json); ?>');
    
    </script>
  </body>
</html>
