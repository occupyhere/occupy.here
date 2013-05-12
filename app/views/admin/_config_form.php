<div id="admin-config">
  <div class="post">
    <h2 class="top">Backup</h2>
    <p>Download an archive of the database, uploaded files, and diagnostic logs.</p>
    <form action="admin/backup_download" id="backup_form" method="post" target="backup_iframe">
      <input type="hidden" name="file" value="" />
      <input type="button" value="download backup" />
      <br class="clear" />
    </form>
  </div>
  <form action="admin/upgrade" method="post" enctype="multipart/form-data" id="upgrade" target="upgrade_iframe" class="post">
    <h2 class="top">Upgrade or restore</h2>
    <p class="help">You are currently running revision <?php echo REVISION; ?>.</p>
    <label>
      Select occupy.here software or backup zip file.
      <input type="file" name="file" />
    </label>
  </form>
  <div class="post">
    <h2 class="top">More options</h2>
    <p><a href="/cgi-bin/luci">Login to LuCI</a> for more admin options.</p>
  </div>
</div>
<iframe name="upgrade_iframe" width="1" height="1" border="0" class="hidden"></iframe>
<iframe name="backup_iframe" width="1" height="1" border="0" class="hidden"></iframe>
