<div class="sequence">
  <?php if (!empty($prev_page)) { ?>
    <div class="post">
      <a href="<?php echo $prev_page; ?>" class="prev"><span class="prev icon"></span>Previous page</a>
    </div>
  <?php } ?>
  <?php foreach ($items as $item) { ?>
    <div class="post">
      <?php
      
      $type = $item->get_table();
      $this->partial($type, array($type => $item));
      
      ?>
      <p class="utilities"><a href="<?php echo $item->id; ?><?php
        if ($item->reply_count == 0) {
          echo '#reply">post a reply';
        } else if ($item->reply_count == 1) {
          echo '">1 reply';
        } else {
          echo "\">$item->reply_count replies";
        }
      ?></a></p>
    </div>
  <?php } ?>
  <div class="post">
    <?php
    
    if (empty($items)) {
      echo 'There is nothing here yet';
    } else if (!empty($end_of_items)) {
      echo 'You have reached the end';
    }
    
    if (!empty($next_page)) {
      echo "<a href=\"$next_page\" class=\"next\"><span class=\"next icon\"></span>Next page</a>";
    }
    
    ?>
  </div>
</div>
