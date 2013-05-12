<div id="content">
  <ul>
    <?php foreach ($files as $file) { ?>
      <?php $topic = $topics[$file->message_id]; ?>
      <li id="file_<?php echo $file->id; ?>" class="file user_<?php echo $file->user_id; ?>" data-colors="<?php echo get_colors($file->user_id); ?>">
        <a href="<?php echo "p/$file->message_id"; ?>" class="filename"><?php echo $file->name; ?></a>
        <div class="meta">
          <span class="author"><a href="u/<?php echo $file->user_id; ?>" class="id"><span class="color"></span><?php echo get_username($file->user_id); ?></a></span>
          <span class="when"><?php echo elapsed_time($file->created); ?></span>
        </div>
      </li>
    <?php } ?>
  </ul>
</div>
