<?php

if (wispr_pong() == 'show-intro' && !empty($params['intro'])) {
  $this->partial('wispr_intro');
} else {
  
  ?>
  <div class="post">
    <a href="forum"><h2 class="forum heading">forum</h2></a>
    <?php $this->partial('forum/topic_form'); ?>
  </div>
  <?php $this->partial('forum/topics'); ?>
<?php } ?>
