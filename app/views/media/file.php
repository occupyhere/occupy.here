<div id="file-<?php echo $file->id; ?>" class="post">
  <?php $this->partial('file', array('file' => $file)); ?>
  <div class="utilities">
    <?php $this->partial('download_file', array('file' => $file)); ?>
  </div>
</div>
<?php

if (!empty($replies)) {
  $this->partial('../forum/replies');
}

$this->partial('../forum/reply_form');

?>
