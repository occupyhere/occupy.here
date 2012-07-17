<div id="upload_file">
  <form action="api/upload_file" method="post" enctype="multipart/form-data" target="upload">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php
    
    $upload_max_filesize = parse_size(ini_get('upload_max_filesize'));
    $post_max_size = parse_size(ini_get('post_max_size'));
    
    echo min($upload_max_filesize, $post_max_size);
    
    ?>" />
    <a href="#upload_form" class="upload_file">Upload a new file <input type="file" name="file" /></a>
    <br class="clear" />
  </form>
  <form action="api/post_file" method="post" class="details">
    <div id="upload_details">
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
    </div>
  </form>
  <iframe name="upload" class="upload" border="0"></iframe>
  <script>
  slide($('upload_details'));
  </script>
</div>
