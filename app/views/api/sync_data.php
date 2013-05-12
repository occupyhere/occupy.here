<?php

$file_chunk_size = 128 * 1024; // 128kb

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
  foreach ($unknown_server_ids as $unknown_server_id) {
    $unknown_servers[$unknown_server_id] = $all_servers->$server_id;
  }
  $unknown_server_objects = get_updated_objects_by_server($unknown_servers);
  
  $objects = array_merge($known_server_objects, $unknown_server_objects);
  unset($all_servers->$server_id);
  
  $response = (object) array(
    'revision' => REVISION,
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
      $record = new Grid_Record($table, get_object_vars($object));
      $record->save(true);
      if ($table == 'file') {
        $now = time();
        $file_sync_upload = new Grid_Record('file_sync_upload', array(
          'id' => $object->id,
          'created' => $now
        ));
        $file_sync_upload->save(true);
      }
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
} else if (!empty($params['download'])) {
  $id = $params['download'];
  $offset = (int) $params['offset'];
  $file = $grid->db->record('file', $id);
  $path = GRID_DIR . "/public/uploads/$file->path";
  $binary_data = file_get_contents($path);
  $base64_data = base64_encode($binary_data);
  $data_chunk = substr($base64_data, $offset, $file_chunk_size);
  $complete = ($offset + $file_chunk_size >= strlen($base64_data));
  $response = (object) array(
    'revision' => REVISION,
    'id' => $id,
    'data' => $data_chunk,
    'complete' => $complete
  );
  header('Content-Type: application/json');
  echo json_encode($response);
} else if (!empty($params['files'])) {
  $files = json_decode($params['files']);
  $ids = array();
  for ($i = 0; $i < count($files); $i++) {
    $ids[] = '?';
  }
  $ids = join(', ', $ids);
  $uploads = $grid->db->select('file_sync_upload', array(
    'where' => "id IN ($ids)",
    'values' => $files,
    'order' => 'created'
  ));
  $id = 0;
  $offset = 0;
  if (count($uploads) > 0) {
    $upload = $uploads[0];
    $id = $upload->id;
    $offset = 0;
    $chunks = $grid->db->select('file_sync_upload_chunk', array(
      'where' => 'file_id = ?',
      'order' => 'offset DESC'
    ));
    if (!empty($chunks)) {
      $first_chunk = $chunks[0];
      $offset = (int) $first_chunk->offset;
    }
  }
  $response = (object) array(
    'revision' => REVISION,
    'id' => $id,
    'offset' => $offset,
    'size' => $file_chunk_size
  );
  header('Content-Type: application/json');
  echo json_encode($response);
} else if (!empty($params['upload'])) {
  $offset = (int) $params['offset'];
  $file_id = $params['upload'];
  $id = "$file_id.$offset";
  $chunk = new Grid_Record('file_sync_upload_chunk', array(
    'id' => $id,
    'file_id' => $file_id,
    'data' => $params['data']
  ));
  $chunk->save(true);
  if (!empty($params['complete'])) {
    $file = $grid->db->record('file', $file_id);
    $chunks = $grid->db->select('file_sync_upload_chunk', array(
      'where' => 'file_id = ?',
      'values' => array($file_id),
      'order' => 'id'
    ));
    $encoded_data = '';
    foreach ($chunks as $chunk) {
      $grid->log("Appending chunk $chunk->id");
      $encoded_data .= $chunk->data;
    }
    $length = strlen($encoded_data);
    $grid->log("Encoded data length: " . $length);
    $data = base64_decode($encoded_data);
    $path = GRID_DIR . "/public/uploads/$file->path";
    $dir = dirname($path);
    if (!file_exists($dir)) {
      mkdir($dir);
    }
    file_put_contents($path, $data);
    $grid->db->delete('file_sync_upload', $file_id);
    $grid->db->query("
      DELETE
      FROM file_sync_upload_chunk
      WHERE file_id = ?
    ", array($file_id));
  }
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
  $count = count($objects);
  return $objects;
}

function filter_message_ids($id) {
  return (substr($id, 0, 8) == 'message.');
}

?>
