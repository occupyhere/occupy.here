<form action="admin" method="post" id="auth-login" class="post">
  <p class="help">The admin page offers utilities for backing up, upgrading, and monitoring the wifi router.</p>
  <?php if (!empty($feedback)) { ?>
    <p><strong><?php echo $feedback; ?></strong></p>
  <?php } ?>
  <label>
    Username
    <input type="text" name="admin_username" />
  </label>
  <label>
    Password
    <input type="password" name="admin_password" />
  </label>
  <input type="submit" value="login" />
  <br class="clear" />
</form>
