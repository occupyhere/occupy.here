<form action="/api/post_topic" method="post" id="topic-form">
  <?php $this->partial('post_form'); ?>
</form>
<div id="container-info">
  <h2><?php echo esc($container->name); ?></h2>
</div>
<?php $this->partial('sequence', array(
  'where' => 'parent_id = ?',
  'value' => $container->id
)); ?>
<?php $this->partial('upload_form'); ?>
