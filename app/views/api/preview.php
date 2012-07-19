<?php

$message = (object) array(
  'user_id' => $grid->user->id,
  'content' => $params['content'],
  'created' => time()
);
$vars = array(
  'message' => $message
);

$this->partial('message', $vars);

?>
