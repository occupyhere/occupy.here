<form action="admin/login" method="post" id="auth-login" class="post">
  <?php if (!empty($_GET['error'])) { ?>
    <p><strong><?php echo _('Sorry that login didn’t match any known account'); ?>.</strong></p>
  <?php } ?>
  <label>
    <?php echo _('Username'); ?>
    <input type="text" name="admin_username" value="<?php echo $username ?>" />
  </label>
  <label>
    <?php echo _('Password'); ?>
    <input type="password" name="admin_password" />
  </label>
  <input type="submit" value="<?php echo _('Login'); ?>" />
  <br class="clear" />
</form>
