<article id="post-form" class="<?php echo "user_{$grid->user->id}"; ?>" data-colors="<?php echo get_colors(); ?>">
  <form action="/api/post_topic" method="post" class="spacer">
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
  </form>
</article>
