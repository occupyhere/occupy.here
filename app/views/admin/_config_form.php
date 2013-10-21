<div id="admin-config">
  <header>
    <h2>Occupy.here administration</h2>
    You are logged in as an administrator. <a href="logout">Logout</a>
  </header>
  <section id="backup">
    <h2 class="top">Backup</h2>
    <p>Download an archive of the database, uploaded files, and diagnostic logs.</p>
    <iframe name="backup_iframe" border="0" class="hidden" id="backup_iframe"></iframe>
    <form action="admin/backup_download" id="backup_form" method="post" target="backup_iframe">
      <input type="hidden" name="file" value="" />
      <input type="submit" class="button" value="Download backup" ontouchstart="" />
      <br class="clear" />
    </form>
  </section>
  <!--<section id="restore">
    <iframe name="upgrade_iframe" width="1" height="1" border="0" class="hidden"></iframe>
    <form action="admin/upgrade" method="post" enctype="multipart/form-data" id="upgrade" target="upgrade_iframe" class="post">
      <h2 class="top">Upgrade or restore</h2>
      <p>You are currently running revision <?php echo REVISION; ?>.</p>
      <label>
        Choose a zip file to upload.
        <input type="file" name="file" />
      </label>
    </form>
  </section>-->
  <section id="update_ssid">
    <form action="admin/update_ssid">
      <h2 class="top">Wifi network name</h2>
      <input type="text" value="<?php echo get_current_ssid(); ?>" name="ssid" id="ssid">
      <input type="submit" class="button" value="Update">
    </form>
  </section>
  <section id="update_hostname">
    <form action="admin/update_hostname" method="post">
      <h2 class="top">Base URL</h2>
      <input type="text" value="<?php echo get_current_hostname(); ?>" name="hostname" id="hostname">
      <input type="submit" class="button" value="Update">
    </form>
  </section>
</div>
