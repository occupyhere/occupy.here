<div id="reply" class="post">
  <h2 class="new_message">Post a reply</h2>
  <form action="api/post_reply" method="post" id="new_message_form" class="message_form">
    <input type="hidden" name="parent_id" value="<?php echo $item->id; ?>" />
    <?php if (empty($grid->user->name)) { ?>
      <?php $this->partial('../username_input'); ?>
    <?php } ?>
    <textarea name="content" class="content" placeholder="Your message"></textarea>
    <input type="submit" value="post" />
    <input type="button" name="task" value="preview" class="preview" />
    <?php if (!empty($grid->user->name)) { ?>
      <div class="posting_as">
        Posting as <a href="/account"><?php echo get_username($grid->user); ?></a>
      </div>
    <?php } ?>
    <br class="clear" />
  </form>
</div>
