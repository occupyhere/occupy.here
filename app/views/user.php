<h2 class="user"><?php echo _('Posts by'); ?> <?php echo get_username($params['id']); ?></h2>
<?php

$this->partial('sequence', array(
  'where' => 'user_id = ?',
  'value' => $params['id']
));

?>
