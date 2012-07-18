<div id="<?php echo $item->id; ?>" class="post">
  <?php $this->partial($params['type'], array($params['type'] => $item)); ?>
</div>
<?php

if (!empty($replies)) {
  $this->partial('replies');
}

$this->partial('reply_form');

?>
