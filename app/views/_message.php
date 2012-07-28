<h3>
  <a href="<?php echo $message->user_id; ?>" class="user"><?php echo get_username($message->user_id); ?></a>
  <span class="when"><?php echo elapsed_time($message->created); ?></span>
</h3>
<?php echo nl2br(htmlentities($message->content)); ?>
