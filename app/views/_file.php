<h3>
  <?php echo get_username($file->user_id); ?>
  <span class="when"><?php echo elapsed_time($file->created); ?></span>
</h3>
<a href="uploads/<?php echo $file->path ?>"><?php echo htmlentities($file->name); ?></a>
<?php if (preg_match('/^image/', $file->type)) { ?>
  <?php
  
  $href = ($params['view'] == 'detail') ? "uploads/$file->path" : $file->id;
  list($orig_width, $orig_height) = getimagesize(GRID_DIR . "/data/media/$file->path");
  $width = $orig_width;
  $height = $orig_height;
  $alt = htmlentities($file->name);
  if ($params['view'] == 'detail') {
    if ($width > 472) {
      $width = 472;
      $height = round($width / ($orig_width / $orig_height));
    }
  } else if ($width > 150) {
    $width = 150;
    $height = round($width / ($orig_width / $orig_height));
    if ($height > 150) {
      $height = 150;
      $width = round($height / ($orig_height / $orig_width));
    }
  }
  
  ?>
  <a href="<?php echo $href; ?>">
    <?php echo "<img src=\"uploads/$file->path\" alt=\"$alt\" width=\"$width\" height=\"$height\" class=\"upload\" />\n"; ?>
  </a>
<?php } ?>
