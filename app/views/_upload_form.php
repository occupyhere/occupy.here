<div id="upload_file">
  <a href="#upload_form" class="upload_file">Upload a file</a>
  <br class="clear" />
  <div id="upload_details">
    <form action="api/upload_file" method="post" enctype="multipart/form-data" target="upload">
      <input type="hidden" name="max_file_size" value="<?php
      
      $upload_max_filesize = parse_size(ini_get('upload_max_filesize'));
      $post_max_size = parse_size(ini_get('post_max_size'));
      echo min($upload_max_filesize, $post_max_size);
      
      ?>" />
      <label class="file_label">
        Choose a file to upload
        <input type="file" name="file" />
      </label>
    </form>
    <form action="api/post_file" method="post" class="details">
      <input type="hidden" name="filename" value="" />
      <input type="hidden" name="original" value="" />
      <input type="hidden" name="type" value="" />
      <?php if (empty($grid->user->name)) { ?>
        <?php $this->partial('../username_input'); ?>
      <?php } ?>
      <label class="name">
        Title
        <input type="text" id="upload_title" name="name" />
      </label>
      <input type="submit" name="task" value="post" />
      <?php if (!empty($grid->user->name)) { ?>
        <div class="posting_as">
          Posting as <a href="/account"><?php echo get_username($grid->user); ?></a>
        </div>
      <?php } ?>
      <a href="#upload_details" class="cancel toggle">Cancel</a>
      <br class="clear" />
    </form>
  </div>
  <iframe name="upload" class="upload" border="0"></iframe>
  <script>
  slide($('upload_details'));
  </script>
</div>
