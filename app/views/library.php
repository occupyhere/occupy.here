<?php if (!empty($_SESSION['is_admin']) && defined('READABILITY_API_KEY')) { ?>
  <form action="/api/import_library" method="post" id="topic-form">
    <?php $this->partial('import_form'); ?>
  </form>
<?php } ?>
<div id="content">
  <h2>Library</h2>
  <?php if (empty($posts)) { ?>
     <p class="pagination"><em>There is nothing here yet</em></p>
  <?php } else { ?>
    <ul>
      <?php foreach ($posts as $post) { ?>
        <li id="post_<?php echo $post->id; ?>" class="file user_<?php echo $post->user_id; ?>" data-colors="<?php echo get_colors($post->user_id); ?>">
          <span class="meta"><?php echo date('F j, Y', $post->created); ?></span><br>
          <a href="<?php echo "p/$post->id"; ?>" class="filename"><?php echo $post->content; ?></a>
          <a href="<?php echo "p/$post->id/edit"; ?>" class="edit">Edit</a>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
</div>
<?php $this->partial('upload_form'); ?>
