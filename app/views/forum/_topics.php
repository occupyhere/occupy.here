<?php foreach ($topics as $topic) { ?>
  <div class="post">
    <?php $this->partial('message', array('message' => $topic)); ?>
    <p class="reply"><a href="forum/<?php echo $topic->id; ?><?php
      if ($topic->reply_count == 0) {
        echo '#reply">post a reply';
      } else if ($topic->reply_count == 1) {
        echo '">1 reply';
      } else {
        echo "\">$topic->reply_count replies";
      }
    ?></a></p>
  </div>
<?php } ?>
<div class="post">
  <?php
  
  if (empty($topics)) {
    echo 'There are no messages here yet';
  } else if (!empty($end_of_messages)) {
    echo 'End of messages';
  }
  if (!empty($next_page)) {
    echo "<a href=\"$next_page\" class=\"go\">Next page</a>";
  }
  
  if (!empty($prev_page)) {
    echo "<a href=\"$prev_page\" class=\"prev\">Prev page</a><br class=\"clear\" />";
  }

  ?>
</div>
