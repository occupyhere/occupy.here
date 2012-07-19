<html>
  <body>
    <?php
    
    function esc($value) {
      $value = str_replace("'", "\\'", $value);
      $value = htmlentities($value);
      echo $value;
    }
    
    extract($_FILES['file']);
    $filename = basename($tmp_name);
    
    setup_uploads();
    move_uploaded_file($tmp_name, GRID_DIR . "/public/uploads/tmp/$filename");
    
    ?>
    <script>
    
    parent.upload_complete('<?php echo esc($filename); ?>', '<?php echo esc($name); ?>', '<?php echo esc($type); ?>');
    
    </script>
  </body>
</html>
