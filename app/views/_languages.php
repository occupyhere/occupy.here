<form id="language" href="<?php echo $_SERVER['REQUEST_URI']; ?>">
  <label for="locale"><?php echo _('Language'); ?>:</label>
  <select name="locale" id="locale">
    <option value="en"<?php if ($grid->locale == 'en') { echo ' selected'; } ?>>English</option>
    <option value="es"<?php if ($grid->locale == 'es') { echo ' selected'; } ?>>Español</option>
  </select>
</form>
