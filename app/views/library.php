<?php if (!empty($_SESSION['is_admin']) && defined('READABILITY_API_KEY')) { ?>
  <form action="/api/import_library" method="post" id="topic-form">
    <?php $this->partial('import_form'); ?>
  </form>
<?php } ?>
<div id="content">
  <h2><?php echo _('Library'); ?></h2>
  <?php if (empty($posts)) { ?>
     <p class="pagination"><em><?php echo ('There is nothing here yet'); ?></em></p>
  <?php } else { ?>
    <ul>
      <?php foreach ($posts as $post) { ?>
        <li id="post_<?php echo $post->id; ?>" class="file user_<?php echo $post->user_id; ?>" data-colors="<?php echo get_colors($post->user_id); ?>">
          <span class="meta"><?php echo elapsed_time($post->created); ?></span><br>
          <a href="<?php echo "p/$post->id"; ?>" class="filename"><?php echo esc($post->content); ?></a>
          <a href="<?php echo "p/$post->id/edit"; ?>" class="edit"><?php echo _('Edit'); ?></a>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
</div>
<?php $this->partial('upload_form'); ?>
