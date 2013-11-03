<?php

function generate_id() {
  if (function_exists('openssl_random_pseudo_bytes')) {
    $id_bin = openssl_random_pseudo_bytes(8);
    return bin2hex($id_bin);
  } else {
    return uniqid();
  }
}

function create_user() {
  global $grid;
  $now = time();
  $user_id = generate_id();
  $color1 = rand(0, 359);
  $color2 = rand(0, 359);
  $grid->db->insert('user', array(
    'id' => $user_id,
    'server_id' => $grid->meta['server_id'],
    'color1' => $color1,
    'color2' => $color2,
    'created' => $now,
    'updated' => $now
  ));
  $_SESSION['user_id'] = $user_id;
  return $grid->db->record('user', $user_id);
}

function setup_user() {
  global $grid;
  if (!empty($grid->user)) {
    return;
  }
  if (!empty($grid->request)) {
    $view = $grid->request->params['view'];
    if (strpos($view, 'wispr') !== false ||
        strpos($view, '404') !== false) {
      return;
    }
  }
  ini_set('session.name', 'SESSION');
  ini_set('session.use_cookies', true);
  ini_set('session.cookie_lifetime', time() + 60 * 60 * 24 * 365);
  ini_set('session.gc_maxlifetime', time() + 60 * 60 * 24 * 365);
  session_set_save_handler('session_open', 'session_close', 'session_read', 'session_write', 'session_delete', 'session_gc');
  register_shutdown_function('session_write_close');
  session_start();
  if (!empty($_SESSION['user_id'])) {
    $user = $grid->db->record('user', $_SESSION['user_id']);
  }
  if (empty($user)) {
    $user = create_user();
  }
  $grid->user = $user;
  $grid->users = array(
    $user->id => $user
  );
}

function setup_meta() {
  global $grid;
  $grid->meta = array();
  $result = $grid->db->select('meta');
  foreach ($result as $record) {
    $grid->meta[$record->name] = $record->value;
  }
  if (empty($grid->meta['server_id'])) {
    save_meta(array(
      'server_id' => generate_id(),
      'last_updated' => '{}'
    ));
  }
}

function setup_library() {
  global $grid;
  $now = time();
  $library = $grid->db->record('container', 'library');
  if (empty($library)) {
    $grid->db->insert('container', array(
      'id' => 'library',
      'name' => 'Library',
      'created' => $now,
      'updated' => $now
    ));
  }
}

function setup_uploads() {
  $public_dir = GRID_DIR . "/public";
  $uploads_dir = GRID_DIR . "/public/uploads";
  $tmp_dir = GRID_DIR . "/public/uploads/tmp";
  if (!file_exists($uploads_dir)) {
    if (!is_writable($public_dir)) {
      return false;
    }
    mkdir($uploads_dir);
  }
  if (!file_exists($tmp_dir)) {
    mkdir($tmp_dir);
  }
  return true;
}

function save_meta($meta) {
  global $grid;
  foreach ($meta as $key => $value) {
    if (isset($grid->meta[$key])) {
      $values = array(
        'value' => $value
      );
      $grid->db->update('meta', $values, 'name = ?', array($key));
    } else {
      $values = array(
        'name' => $key,
        'value' => $value
      );
      $grid->db->insert('meta', $values);
    }
    $grid->meta[$key] = $value;
  }
}

function get_user($target) {
  global $grid;
  if (is_string($target)) {
    if (isset($grid->users[$target])) {
      return $grid->users[$target];
    } else {
      $target = $grid->db->record('user', $target);
      if (!empty($target)) {
        $grid->users[$target->id] = $target;
        return $target;
      }
    }
  }
  return $target;
}

function get_username($target) {
  global $grid;
  if (!empty($grid->request->params['username'])) {
    return $grid->request->params['username'];
  }
  $user = get_user($target);
  if (empty($user->name)) {
    return _('Anonymous');
  } else {
    return $user->name;
  }
}

function get_colors($target = null) {
  global $grid;
  if (empty($target)) {
    $user = $grid->user;
  } else {
    $user = get_user($target);
  }
  return "$user->color1,$user->color2";
}

function get_posts($query) {
  global $grid;
  $posts = $grid->db->select('message', $query);
  $lookup = array();
  $attachment_ids = array();
  foreach ($posts as $post) {
    $post->reply_count = 0;
    $lookup[$post->id] = $post;
    if (!empty($post->file_id)) {
      $attachment_ids[] = $post->file_id;
    }
  }
  if (!empty($posts)) {
    $ids = array_keys($lookup);
    $ids = "'" . implode("','", $ids) . "'";
    $reply_query = $grid->db->query("
      SELECT parent_id, COUNT(id) AS reply_count
      FROM message
      WHERE parent_id IN ($ids)
      GROUP BY parent_id
    ");
    $replies = $reply_query->fetchAll(PDO::FETCH_OBJ);
    foreach ($replies as $messages) {
      $post = $lookup[$messages->parent_id];
      $post->reply_count = $messages->reply_count;
    }
  }
  
  find_attachments($lookup, $attachment_ids);
  
  return $posts;
}

function get_filename($filename) {
  preg_match('/(.+)(\.\w+)$/', $filename, $matches);
  if (strlen($matches[1]) > 24) {
    $filename = substr($matches[1], 0, 16) . '...' . substr($matches[1], -10, 8) . $matches[2];
  }
  return $filename;
}

function get_bio($target) {
  $user = get_user($target);
  if (empty($user->bio)) {
    return '';
  } else {
    return $user->bio;
  }
}

function update_user() {
  global $grid;
  if ($grid->user->name != $_POST['username'] ||
      $grid->user->color1 != $_POST['color1'] ||
      $grid->user->color2 != $_POST['color2']) {
    $update = array(
      'name' => $_POST['username'],
      'color1' => $_POST['color1'],
      'color2' => $_POST['color2'],
      'updated' => $now
    );
    $grid->db->update('user', $update, $grid->user->id);
  }
}

function attach_file($id, $attachment) {
  global $grid;
  $message = $grid->db->record('message', $id);
  $file = $grid->db->record('file', $attachment);
  $dir = GRID_DIR . "/public";
  $subdir = 'uploads/' . substr($id, 0, 1);
  if (!file_exists("$dir/$subdir")) {
    mkdir("$dir/$subdir");
  }
  $subdir = "$subdir/" . substr($id, 1, 1);
  if (!file_exists("$dir/$subdir")) {
    mkdir("$dir/$subdir");
  }
  $subdir = "$subdir/$id";
  mkdir("$dir/$subdir");
  rename(GRID_DIR . "/public/$file->path", "$dir/$subdir/$file->name");
  $grid->db->update('file', array(
    'message_id' => $id,
    'path' => "$subdir/$file->name",
    'expires' => $message->expires
  ), $attachment);
}

function get_container() {
  global $grid;
  if (empty($_POST['container'])) {
    return null;
  }
  $id = $_POST['container'];
  $container = $grid->db->record('container', $id);
  if (!empty($container)) {
    return $container;
  } else {
    $name = $id;
    $id = generate_id();
    $now = time();
    $grid->db->insert('container', array(
      'id' => $id,
      'user_id' => $grid->user->id,
      'name' => $name,
      'server_id' => $grid->meta['server_id'],
      'created' => $now,
      'updated' => $now
    ));
    return $grid->db->record('container', $id);
  }
}

function find_attachments($lookup, $attachment_ids) {
  global $grid;
  if (!empty($attachment_ids)) {
    $attachment_ids = "'" . implode("','", $attachment_ids) . "'";
    $attachment_query = $grid->db->query("
      SELECT *
      FROM file
      WHERE id IN ($attachment_ids)
    ");
    $attachments = $attachment_query->fetchAll(PDO::FETCH_OBJ);
    foreach ($attachments as $attachment) {
      $message = $lookup[$attachment->message_id];
      $message->attachment = $attachment;
    }
  }
}

function show_attachment($post) {
  global $grid;
  $attachment = $post->attachment;
  if (preg_match('/\.(jpe?g|gif|png|bmp|tiff?)$/i', $attachment->path)) {
    echo '<div id="inline-attachment">';
    show_attachment_link($attachment);
    echo '<div class="frame">';
    echo "<img src=\"$attachment->path\" alt=\"\">\n";
    echo '</div></div>';
    return true;
  } else if (preg_match('/\.pdf$/i', $attachment->path)) {
    $path = str_replace("'", "\'", htmlentities($attachment->path));
    echo '<div id="inline-attachment">';
    show_attachment_link($attachment);
    $loading = _('Loading')  . '...';
    echo '<div class="frame">';
    echo "<div id=\"pdf-loading\">$loading</div>";
    echo "<canvas id=\"pdf\"></canvas>\n";
    echo '</div></div>';
    echo '<script src="js/pdfjs.js"></script>';
    echo '<script src="js/pdf-compatibility.js"></script>';
    echo "<script>PDFJS.workerSrc = 'js/pdfjs.js';</script>";
    return true;
  } else if (preg_match('/\.json$/i', $attachment->path)) {
    $json = file_get_contents(GRID_DIR . "/public/$attachment->path");
    $article = json_decode($json);
    if (!empty($article->url)) {
      $url = parse_url($article->url);
      $domain = $url['host'];
      $domain = str_replace('www.', '', $domain);
    }
    $meta = '';
    $by = _('By');
    $author = empty($article->author) ? '' : "$by $article->author / ";
    if (!empty($author) || !empty($domain)) {
      $meta = "<div class=\"meta\">
        $author<a href=\"$article->url\">$domain</a>
      </div>";
    }
    $title = (!empty($article->title)) ? "<h2>$article->title</h2>" : '';
    if (!empty($article->title) && $article->title != $post->content) {
      $grid->db->update('message', array(
        'content' => $article->title
      ), $post->id);
    }
    $content = $article->content;
    if (!empty($article->images)) {
      $dir = dirname($attachment->path);
      foreach ($article->images as $image) {
        $content = str_replace($image->url, "$dir/$image->filename", $content);
      }
    }
    echo "<div id=\"article\">
      $title
      $meta
      $content
    </div>";
    return true;
  } else if (preg_match('/\.mp3$/i', $attachment->path)) {
    echo "<div id=\"mp3\">
      <audio controls>
        <source src=\"$attachment->path\" type=\"audio/mpeg\">
      </audio>
      <a href=\"$attachment->path\" class=\"button\">Download</a>
      <div class=\"clear\"></div>
    </div>";
    return true;
  }
  return false;
}

function show_attachment_link($attachment) {
  $name = get_filename($attachment->name);
  $name = htmlentities($name);
  $path = htmlentities($attachment->path);
  echo "<a href=\"$path\" target=\"_blank\" class=\"attachment\">$name</a>\n";
}

function elapsed_time($time) {
  $time = time() - $time;
  $tokens = array (
    31536000 => 'year',
    2592000 => 'month',
    604800 => 'week',
    86400 => 'day',
    3600 => 'hour',
    60 => 'minute',
    1 => 'second'
  );
  foreach ($tokens as $seconds => $unit) {
    if ($time < $seconds) {
      continue;
    }
    $number = floor($time / $seconds);
    $labels = array(
      'year' =>   ngettext('year', 'years', $number),
      'month' =>  ngettext('month', 'months', $number),
      'week' =>   ngettext('week', 'weeks', $number),
      'day' =>    ngettext('day', 'days', $number),
      'hour' =>   ngettext('hour', 'hours', $number),
      'minute' => ngettext('minute', 'minutes', $number),
      'second' => ngettext('second', 'seconds', $number)
    );
    $label = $labels[$unit];
    return sprintf(_('%s ago'), "$number $label");
  }
  return _('moments ago');
}

function admin_password_set() {
  global $grid;
  $password = $grid->db->record('meta', 'name = ?', 'admin_password');
  return !empty($password);
}

function wispr_ping() {
  global $grid;
  $now = time();
  $record = $grid->db->record('wispr', $_SERVER['REMOTE_ADDR']);
  if (empty($record)) {
    $grid->db->insert('wispr', array(
      'id' => $_SERVER['REMOTE_ADDR'],
      'status' => 'show-intro',
      'created' => $now
    ));
  }
}

function wispr_pong() {
  global $grid;
  $record = $grid->db->record('wispr', $_SERVER['REMOTE_ADDR']);
  if (empty($record)) {
    return false;
  } else {
    return $record->status;
  }
}

function parse_size($size) {
  if (preg_match('/(\d+).*K/i', $size, $matches)) {
    return 1024 * $matches[1];
  } else if (preg_match('/(\d+).*M/i', $size, $matches)) {
    return 1024 * 1024 * $matches[1];
  } else if (preg_match('/(\d+).*G/i', $size, $matches)) {
    return 1024 * 1024 * 1024 * $matches[1];
  } else {
    return $size;
  }
}

function sort_by_created($a, $b) {
  if ($a->created == $b->created) {
    return 0;
  }
  return ($a->created < $b->created) ? 1 : -1;
}

function check_for_ssl() {
  $ssl_request = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ||
                  $_SERVER['SERVER_PORT'] == 443);
  if (!empty($_SESSION['always_use_ssl']) && !$ssl_request) {
    $url = 'https://' . HOSTNAME . $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit;
  }
}

function session_open() {
}

function session_close() {
}

function session_read($id) {
  global $grid;
  $record = $grid->db->record('session', $id);
  $now = time();
  if (!empty($record)) {
    $grid->db->update('session', array(
      'accessed' => $now
    ), $id);
    return $record->data;
  } else {
    $grid->db->insert('session', array(
      'id' => $id,
      'accessed' => $now,
      'created' => $now
    ));
  }
  return '';
}

function session_write($id, $data) {
  global $grid;
  if (empty($id) || empty($data)) {
    return false;
  }
  $now = time();
  $query = $grid->db->update('session', array(
    'data' => $data,
    'accessed' => $now
  ), $id);
  return ($query->rowCount() == 1);
}

function session_delete($id) {
  global $grid;
  $query = $grid->db->delete('session', $id);
  return ($query->rowCount() == 1);
}

function session_gc($max_lifetime = 600) {
  global $grid;
  $now = time();
  $where = 'accessed < ?';
  $values = array($now - $max_lifetime);
  $grid->db->delete('session', $where, $values);
  return true;
}

function check_for_expired_content() {
  global $grid;
  $now = time();
  if (empty($grid->meta['last_expired_check']) ||
      $now - $grid->meta['last_expired_check'] > 60) {
    $grid->db->delete('message', 'expires < ?', array($now));
    $grid->db->delete('file', 'expires < ?', array($now));
    save_meta(array(
      'last_expired_check' => $now
    ));
  }
}

function check_for_import_content() {
  global $grid;
  if (!file_exists(GRID_DIR . '/import')) {
    return;
  }
  setup_user();
  $dh = opendir(GRID_DIR . '/import');
  $now = time();
  $expires = $now + 31536000;
  while ($filename = readdir($dh)) {
    if (substr($filename, 0, 1) == '.') {
      continue;
    }
    
    $message_id = generate_id();
    $file_id = generate_id();
    $created = $now;
    
    if (substr($filename, -5, 5) == '.json') {
      $json = file_get_contents(GRID_DIR . "/import/$filename");
      $article = json_decode($json);
      $content = $article->title;
      if (!empty($article->date_published)) {
        $date_published = strtotime($article->date_published);
        if (!empty($date_published)) {
          $created = $date_published;
        }
      }
    } else {
      $content = preg_replace('#\.\w+$#', '', $filename);
    }
    
    $grid->db->insert('message', array(
      'id' => $message_id,
      'user_id' => $grid->user->id,
      'content' => $content,
      'parent_id' => 'c/library',
      'server_id' => $grid->meta['server_id'],
      'file_id' => $file_id,
      'expires' => $expires,
      'created' => $created,
      'updated' => $now
    ));
    
    $path = "../import/$filename";
    $grid->db->insert('file', array(
      'id' => $file_id,
      'user_id' => $grid->user->id,
      'server_id' => $grid->meta['server_id'],
      'name' => $filename,
      'path' => $path,
      'created' => $now,
      'updated' => $now
    ));
    attach_file($message_id, $file_id);
  }
}

function get_current_ssid() {
  if (file_exists('/etc/config/wireless')) {
    $wireless = file_get_contents('/etc/config/wireless');
    if (preg_match('/option ssid\s*(.+)$/m', $wireless, $matches)) {
      $ssid = trim($matches[1]);
      if (substr($ssid, 0, 1) == "'" && substr($ssid, -1, 1) == "'") {
        $ssid = substr($ssid, 1, -1);
      }
      return $ssid;
    }
  }
  return 'OCCUPY.HERE';
}

function get_current_hostname() {
  return HOSTNAME;
}

function esc($text) {
  return htmlentities($text, ENT_COMPAT, 'UTF-8');
}

?>
