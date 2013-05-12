<div id="replies">
  <?php foreach ($replies as $reply) { ?>
    <div class="post">
      <?php $this->partial('post', array(
        'post' => $reply,
        'class' => 'reply'
      )); ?>
    </div>
  <?php } ?>
</div>
