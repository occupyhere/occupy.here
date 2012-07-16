<form action="api/update_account" method="post" class="post" id="account">
  <h2 class="top">Update your account</h2>
  <?php $this->partial('username_input'); ?>
  <label class="bio">
    Write something about yourself
    <textarea name="bio" id="bio"><?php echo get_bio($grid->user); ?></textarea>
  </label>
  <input type="submit" value="update" />
</form>
