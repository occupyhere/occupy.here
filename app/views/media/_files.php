<?php foreach ($files as $file) { ?>
  <div class="post">
    <?php $this->partial('file', array('file' => $file)); ?>
    <p class="utilities"><?php
    
    $this->partial('download_file', array('file' => $file));
    
    ?><a href="media/<?php echo $file->id; ?><?php
      if ($file->reply_count == 0) {
        echo '#reply">post a reply';
      } else if ($file->reply_count == 1) {
        echo '">1 reply';
      } else {
        echo "\">$file->reply_count replies";
      }
    ?></a></p>
  </div>
<?php } ?>
<div class="post">
  <?php
  
  if (empty($files)) {
    echo 'There are no media files here yet';
  } else if (!empty($end_of_files)) {
    echo 'End of media files';
  }
  
  if (!empty($media_next_page)) {
    echo "<a href=\"$media_next_page\" class=\"go\">Next page</a>";
  }
  
  if (!empty($prev_page)) {
    echo "<a href=\"$prev_page\" class=\"prev\">Prev page</a><br class=\"clear\" />";
  }

  ?>
</div>
