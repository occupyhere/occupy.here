<?php

if (!empty($params['last_updated'])) {
  $objects = array();
  $server_id = $grid->meta['server_id'];
  
  $known_servers = json_decode($params['last_updated']);
  $known_server_ids = array_keys(get_object_vars($known_servers));
  $known_server_objects = get_updated_objects_by_server($known_servers);
  
  $all_servers = json_decode($grid->meta['last_updated']);
  $all_servers->$server_id = 0;
  $all_server_ids = array_keys(get_object_vars($all_servers));
  
  $unknown_servers = array();
  $unknown_server_ids = array_diff($all_server_ids, $known_server_ids);
  foreach ($unknown_server_ids as $server_id) {
    $unknown_servers[$server_id] = $all_servers->$server_id;
  }
  $unknown_server_objects = get_updated_objects_by_server($unknown_servers);
  
  $objects = array_merge($known_server_objects, $unknown_server_objects);
  unset($all_servers->$server_id);
  
  $response = (object) array(
    'server_id' => $server_id,
    'last_updated' => $all_servers,
    'objects' => $objects
  );
  header('Content-Type: application/json');
  echo json_encode($response);
} else if (isset($params['objects'])) {
  $objects = json_decode($params['objects']);
  foreach ($objects as $object) {
    if (preg_match('/^([a-z]+)\./', $object->id, $matches)) {
      $table = $matches[1];
      if ($table == 'file') {
        $path = GRID_DIR . "/public/uploads/$object->path";
        $dir = dirname($path);
        $data = base64_decode($object->data);
        if (!file_exists($dir)) {
          mkdir($dir);
        }
        file_put_contents($path, $data);
        unset($object->data);
      }
      $record = new Grid_Record($table, get_object_vars($object));
      $record->save(true);
    }
  }
  
  $now = time();
  $last_updated = json_decode($grid->meta['last_updated']);
  $servers = json_decode($params['servers']);
  foreach ($servers as $server_id) {
    $last_updated->$server_id = $now;
  }
  save_meta(array(
    'last_updated' => json_encode($last_updated)
  ));
}

function get_updated_objects_by_server($servers) {
  $objects = array();
  if (is_object($servers)) {
    $servers = get_object_vars($servers);
  }
  foreach ($servers as $server_id => $last_updated) {
    $objects = array_merge(
      $objects,
      get_updated_objects_by_table('message', $server_id, $last_updated),
      get_updated_objects_by_table('user', $server_id, $last_updated),
      get_updated_objects_by_table('file', $server_id, $last_updated)
    );
  }
  return $objects;
}

function get_updated_objects_by_table($table, $server_id, $last_modified) {
  global $grid;
  $objects = $grid->db->select($table, array(
    'where' => 'server_id = ? AND updated > ?',
    'values' => array($server_id, $last_modified)
  ));
  if ($table == 'file') {
    foreach ($objects as $object) {
      $path = GRID_DIR . "/public/uploads/$object->path";
      $data = file_get_contents($path);
      $object->data = base64_encode($data);
    }
  }
  return $objects;
}

function filter_message_ids($id) {
  return (substr($id, 0, 8) == 'message.');
}

?>
