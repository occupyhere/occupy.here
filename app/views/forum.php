<?php

if (wispr_pong() == 'show-intro' && !empty($params['intro'])) {
  $this->partial('wispr_intro');
} else {
  ?>
  <form action="/api/post_topic" method="post" id="topic-form">
    <?php $this->partial('post_form'); ?>
  </form>
  <?php $this->partial('sequence', array(
    'where' => 'parent_id = ?',
    'value' => 0
  )); ?>
  <?php $this->partial('upload_form'); ?>
<?php } ?>
