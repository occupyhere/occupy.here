<?php

if (wispr_pong() == 'show-intro' && !empty($params['intro'])) {
  $this->partial('wispr_intro');
} else {
  ?>
  <div class="post">
    <?php $this->partial('topic_form'); ?>
    <?php $this->partial('upload_form'); ?>
  </div>
  <div class="sequence">
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
        echo "<a href=\"$next_page\" class=\"go\">Next page</a>";
      }
      
      if (!empty($prev_page)) {
        echo "<a href=\"$prev_page\" class=\"prev\">Prev page</a><br class=\"clear\" />";
      }
      
      ?>
    </div>
  </div>
<?php } ?>
