<?php

$allowed_types = array('message', 'file', 'user');
if (in_array($params['type'], $allowed_types)) {
  $id = "{$params['type']}.{$params['digits']}";
  $item = $grid->db->record($params['type'], $id);
  if ($params['type'] == 'user') {
    $user = get_user($id);
    $messages_query = array(
      'where' => "user_id = ?",
      'values' => array($id),
      'order' => 'created DESC',
      'limit' => $items_per_page
    );
    $files_query = array(
      'where' => "user_id = ?",
      'values' => array($id),
      'order' => 'created DESC',
      'limit' => $items_per_page
    );
    if (!empty($params['posted_before'])) {
      $messages_query['where'] .= " AND created < ?";
      $messages_query['values'][] = $params['posted_before'];
      $files_query['where'] .= " AND created < ?";
      $files_query['values'][] = $params['posted_before'];
    }
    $messages = $grid->db->select('message', $messages_query);
    $files = $grid->db->select('file', $files_query);
    $items = array_merge($messages, $files);
    usort($items, 'sort_by_created');
    $items = array_slice($items, 0, $items_per_page);
  } else {
    $replies = $grid->db->select('message', array(
      'where' => 'parent_id = ?',
      'values' => array($id),
      'order' => 'created'
    ));
  }
}

?>
