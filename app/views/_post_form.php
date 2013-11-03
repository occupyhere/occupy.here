<?php

if (empty($content)) {
  $content = '';
}
$placeholder = _('Type your message here');
if (empty($form_class)) {
  $form_class = "user_{$grid->user->id}";
}

?>
<article id="post-form" class="<?php echo $form_class; ?>" data-colors="<?php echo get_colors(); ?>">
  <input type="hidden" id="attachment" name="attachment">
  <div class="container">
    <div id="post-preview"></div>
    <textarea name="content" rows="1" cols="40" class="content" placeholder="<?php echo $placeholder; ?>"><?php echo esc($content); ?></textarea>
  </div>
  <div class="author">
    <a href="<?php echo "u/{$_SESSION['user_id']}"; ?>" class="id"><span class="color"></span><?php echo get_username($_SESSION['user_id']); ?></a>
    <a href="#" id="edit-username"><?php echo _('edit name/colors'); ?></a>
  </div>
  <div id="username-form" class="hidden">
    <span class="color"></span>
    <input type="text" name="username" placeholder="Anonymous" value="<?php echo htmlentities($grid->user->name); ?>"><br>
    <a href="#" id="edit-colors"><?php echo _('edit colors'); ?></a>
    <div id="color-form" class="hidden">
      <div class="color-editor" id="inner-color">
        <input type="hidden" name="color1" value="<?php echo $grid->user->color1; ?>">
        <div class="label"><?php echo _('Swipe to adjust'); ?></div>
        <div class="handle"></div>
      </div>
      <div class="color-editor" id="outer-color">
      <input type="hidden" name="color2" value="<?php echo $grid->user->color2; ?>">
        <div class="label"><?php echo _('Swipe to adjust'); ?></div>
        <div class="handle"></div>
      </div>
    </div>
  </div>
  <?php if ($params['view'] != 'topic') { ?>
    <a href="#" id="edit-options"><?php echo _('options'); ?></a>
  <?php } ?>
  <div class="clear"></div>
  <?php if ($params['view'] != 'topic') { ?>
    <div id="options-form" class="hidden">
      <label>
        <span class="text"><?php echo _('File under'); ?>:</span>
        <select name="container">
          <option value="">[<?php echo _('Top level'); ?>]</option>
          <?php
          
          foreach ($containers as $container) {
            if ($container->id == 'library') {
              continue;
            }
            $name = esc($container->name);
            $selected = ($params['view'] == 'container' && $params['id'] == $container->id ||
                         $params['view'] == $container->id) ? ' selected="selected"' : '';
            echo "<option value=\"$container->id\"$selected>$name</option>\n";
          }
          
          ?>
          <option value="new">[<?php echo _('New container'); ?>...]</option>
        </select>
      </label>
      <label>
        <span class="text"><?php echo _('Expires in'); ?>:</span>
        <select name="ttl">
          <option value="31536000">1 <?php echo _('year'); ?></option>
          <option value="2592000">1 <?php echo _('month'); ?></option>
          <option value="604800">1 <?php echo _('week'); ?></option>
          <option value="86400">1 <?php echo _('day'); ?></option>
          <option value="3600">1 <?php echo _('hour'); ?></option>
          <option value="60">1 <?php echo _('minute'); ?></option>
        </select>
      </label>
    </div>
  <?php } ?>
  <div class="buttons">
    <button id="submit-button" type="submit" name="task" value="post" class="post button" ontouchstart=""><span class="icon"></span> <?php echo _('Submit'); ?></button>
    <button id="attach-button" class="attach button" ontouchstart=""><span class="icon"></span> <?php echo _('Attach'); ?></button>
    <div class="clear"></div>
  </div>
</article>
