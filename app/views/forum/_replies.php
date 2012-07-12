<div id="replies">
  <?php foreach ($replies as $reply) { ?>
    <div class="post">
      <?php $this->partial('message', array('message' => $reply)); ?>
    </div>
  <?php } ?>
</div>
