<form action="api/upload_file" method="post" enctype="multipart/form-data" target="upload" id="upload-form">
  <input type="hidden" name="max_file_size" value="<?php
  
  $upload_max_filesize = parse_size(ini_get('upload_max_filesize'));
  $post_max_size = parse_size(ini_get('post_max_size'));
  echo min($upload_max_filesize, $post_max_size);
  
  ?>">
  <input type="file" name="file" id="attach_input" />
  <iframe name="upload" class="hidden" border="0"></iframe>
</form>
<ul id="incoming_files">
  <?php
  
  $dh = opendir(GRID_DIR . '/data/incoming');
  while ($file = readdir($dh)) {
    if (substr($file, 0, 1) == '.') {
      continue;
    }
    $file = get_filename($file);
    echo "<li><a href=\"#\">$file</a></li>\n";
  }
  
  ?>
</ul>
