<div class="post">
  <h2 class="top">Backup</h2>
  <p>Download an archive of the database, uploaded files, and diagnostic logs.</p>
  <form action="api/backup" id="backup_form" method="post" target="backup">
    <input type="hidden" name="file" value="" />
    <input type="button" value="download backup" />
    <br class="clear" />
  </form>
</div>
<iframe name="backup" class="hidden" border="0"></iframe>
