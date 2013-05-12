<article id="post-form" class="reply <?php echo "user_{$grid->user->id}"; ?>" data-colors="<?php echo get_colors(); ?>">
  <h2 class="new_message"><span class="message icon"></span> Post a reply</h2>
  <div class="container">
    <div id="post-preview"></div>
    <textarea name="content" rows="1" cols="40" class="content" placeholder="Type words here"></textarea>
  </div>
  <div class="author">
    <a href="user" class="id"><span class="color"></span><?php echo get_username($_SESSION['user_id']); ?></a>
    <a href="#" id="edit-username">edit name/colors</a>
  </div>
  <div id="username-form" class="hidden">
    <span class="color"></span>
    <input type="text" name="username" placeholder="Anonymous" value="<?php echo get_username($_SESSION['user_id']); ?>"><br>
    <a href="#" id="edit-colors">edit colors</a>
    <div id="color-form" class="hidden">
      <div class="color-editor" id="inner-color">
        <input type="hidden" name="color1" value="<?php echo $grid->user->color1; ?>">
        <div class="label">Swipe to adjust</div>
        <div class="handle"></div>
      </div>
      <div class="color-editor" id="outer-color">
      <input type="hidden" name="color2" value="<?php echo $grid->user->color2; ?>">
        <div class="label">Swipe to adjust</div>
        <div class="handle"></div>
      </div>
    </div>
  </div>
  <div class="buttons">
    <button id="submit-button" type="submit" name="task" value="post" class="post button" ontouchstart=""><span class="icon"></span> SUBMIT</button>
    <button id="attach-button" class="attach button" ontouchstart=""><span class="icon"></span> ATTACH</button>
    <div class="clear"></div>
  </div>
</article>

<!--<div id="reply" class="post">
  <h2 class="new_message"><span class="message icon"></span>Post a reply</h2>
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
</div>-->
