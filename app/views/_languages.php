<form id="language" href="<?php echo $_SERVER['REQUEST_URI']; ?>">
  <label for="lang"><?php echo _('Language'); ?>:</label>
  <select name="lang" id="lang">
    <option value="en"<?php if ($grid->lang == 'en') { echo ' selected'; } ?>>English</option>
    <option value="es"<?php if ($grid->lang == 'es') { echo ' selected'; } ?>>Español</option>
  </select>
</form>
