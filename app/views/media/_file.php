<h3>
  <?php echo get_username($file->user_id); ?>
  <span class="when"><?php echo elapsed_time($file->created); ?></span>
</h3>
<a href="uploads/<?php echo $file->path ?>"><?php echo htmlentities($file->name); ?></a>
<?php if (preg_match('/^image/', $file->type)) { ?>
  <!--<a href="uploads/<?php echo $file->path ?>"><img src="<?php echo "uploads/$file->path"; ?>" alt="<?php echo htmlentities($file->name); ?>" class="upload" /></a>-->
<?php } ?>
