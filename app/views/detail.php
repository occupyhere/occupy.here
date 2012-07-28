<?php if ($params['type'] == 'user') { ?>
  <div class="subhead">
    <div id="<?php echo $item->id; ?>" class="post">
      <h2>
        <span class="icon user"></span>
        <?php echo get_username($user); ?>
      </h2>
      <?php if (!empty($user->bio)) { ?>
        <p><?php echo get_bio($user); ?></p>
      <?php } ?>
    </div>
  </div>
  <?php $this->partial('sequence'); ?>
<?php } else { ?>
  <div id="<?php echo $item->id; ?>" class="post">
    <?php $this->partial($params['type'], array($params['type'] => $item)); ?>
  </div>
  <?php
  
  if (!empty($replies)) {
    $this->partial('replies');
  }
  
  $this->partial('reply_form');

}
?>

