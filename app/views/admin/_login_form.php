<form action="admin" method="post" id="auth-login" class="post">
  <?php if (!empty($feedback)) { ?>
    <p><strong><?php echo $feedback; ?></strong></p>
  <?php } ?>
  <label>
    Username
    <input type="text" name="admin_username" value="<?php echo $username ?>" />
  </label>
  <label>
    Password
    <input type="password" name="admin_password" />
  </label>
  <input type="submit" value="Login" />
  <br class="clear" />
</form>
