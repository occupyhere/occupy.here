<form action="api/update_account" method="post" class="post" id="account">
  <h2 class="top">Edit your account</h2>
  <?php $this->partial('username_input'); ?>
  <label class="bio">
    Write something about yourself
    <textarea name="bio" id="bio"><?php echo get_bio($grid->user); ?></textarea>
  </label>
  <input type="submit" value="update" />
  <br class="clear" />
</form>
<div id="many-copies" class="post">
  <h2 class="top">Many Copies</h2>
  <label>
    <input type="checkbox" checked="checked" class="toggle" />
    Enable browser storage
  </label>
  <p class="help">Your browser can store a backup of this site's contents. When you connect to another occupy.here router, your copy will be merged into that node’s database. This helps content move between locations without requiring direct network infrastructure.</p>
  <div class="size"></div>
  <input type="button" class="clear" value="clear stored content" />
  <br class="clear" />
</div>
