<div id="message-<?php echo $topic->id; ?>" class="post">
  <?php $this->partial('message', array('message' => $topic)); ?>
</div>
<?php

if (!empty($replies)) {
  $this->partial('replies');
}

$this->partial('reply_form');

?>
