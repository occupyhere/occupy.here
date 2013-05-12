<h2 class="user">Posts by <?php echo get_username($params['id']); ?></h2>
<?php

$this->partial('sequence', array(
  'items' => $posts
));

?>
