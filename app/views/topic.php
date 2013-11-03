<?php

$this->partial('post', array(
  'post' => $post,
  'class' => "topic user_$post->user_id"
));

if (!empty($replies)) {
  $this->partial('replies');
}

?>
<form action="/api/post_reply" method="post" id="reply">
  <h2><?php echo _('Post a reply'); ?></h2>
  <input type="hidden" name="parent_id" value="<?php echo $post->id; ?>" />
  <?php $this->partial('post_form'); ?>
</form>
<?php $this->partial('upload_form'); ?>
