<form action="admin/login" method="post" id="auth-login" class="post">
  <?php if (!empty($_GET['error'])) { ?>
    <p><strong>Sorry that login didn’t match any known account.</strong></p>
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
