<div id="admin-config">
  <header>
    <h2><?php echo _('Occupy.here administration'); ?></h2>
    <?php echo _('You are logged in as an administrator.'); ?> <a href="logout"><?php echo _('Logout'); ?></a>
  </header>
  <section id="backup">
    <h2 class="top"><?php echo _('Backup'); ?></h2>
    <p><?php echo _('Download an archive of the database, uploaded files, and diagnostic logs.'); ?></p>
    <iframe name="backup_iframe" border="0" class="hidden" id="backup_iframe"></iframe>
    <form action="admin/backup_download" id="backup_form" method="post" target="backup_iframe">
      <input type="hidden" name="file" value="" />
      <input type="submit" class="button" value="<?php echo _('Download backup'); ?>" ontouchstart="" />
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
      <h2 class="top"><?php echo _('Wifi network name'); ?></h2>
      <input type="text" value="<?php echo get_current_ssid(); ?>" name="ssid" id="ssid">
      <input type="submit" class="button" value="<?php echo _('Update'); ?>">
    </form>
  </section>
  <section id="update_hostname">
    <form action="admin/update_hostname" method="post">
      <h2 class="top"><?php echo _('Base URL'); ?></h2>
      <input type="text" value="<?php echo get_current_hostname(); ?>" name="hostname" id="hostname">
      <input type="submit" class="button" value="<?php echo _('Update'); ?>">
    </form>
  </section>
  <section id="network_mode">
    <form action="admin/update_network" method="post">
      <h2 class="top"><?php echo _('Network configuration'); ?></h2>
      <label>
        <input type="radio" name="network_mode" value="lan"<?php checked($network_mode, 'lan'); ?>> <?php echo _('LAN mode (captive portal)'); ?><br>
        <span class="help"><?php echo _('Ethernet is bridged to wifi'); ?></span>
      </label>
      <label>
        <input type="radio" name="network_mode" value="wan"<?php checked($network_mode, 'wan'); ?>> <?php echo _('WAN mode (internet ready)'); ?><br>
        <span class="help"><?php echo ('Ethernet connects to upstream internet'); ?></span>
      </label>
      <dl>
        <dt><?php echo _('Ethernet'); ?>:</dt>
        <dd><?php echo $ethernet_ip_address; ?></dd>
        <br class="clear">
        <dt><?php echo _('Wifi'); ?>:</dt>
        <dd><?php echo $wifi_ip_address; ?></dd>
        <br class="clear">
      </dl>
      <input type="submit" class="button" value="<?php echo _('Update'); ?>">
    </form>
  </section>
</div>
