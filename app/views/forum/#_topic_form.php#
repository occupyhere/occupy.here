<div id="new_message">
  <strong><a href="#new_message_form" class="toggle new_message">Post a new message</a></strong>
  <form action="api/post_topic" method="post" id="new_message_form" class="message_form">
    <div class="username hidden">
      <?php $this->partial('../username_input'); ?>
    </div>
    <textarea name="content" class="content" placeholder="Your message"></textarea>
    <input type="submit" name="task" value="post" />
    <input type="submit" name="task" value="preview" class="preview" />
    <?php if (!empty($grid->user->name)) { ?>
      <div class="posting_as">
        Posting as <a href="/account"><?php echo get_username($grid->user); ?></a>
      </div>
    <?php } ?>
    <a href="#new_message_form" class="cancel toggle">Cancel</a>
    <br class="clear" />
  </form>
  <script>
  slide($('new_message_form'));
  </script>
</div>
