<?php

if (wispr_pong() == 'show-intro' && !empty($params['intro'])) {
  $this->partial('wispr_intro');
} else {
  ?>
  <div class="post">
    <?php $this->partial('topic_form'); ?>
    <?php $this->partial('upload_form'); ?>
  </div>
  <?php $this->partial('sequence'); ?>
<?php } ?>
