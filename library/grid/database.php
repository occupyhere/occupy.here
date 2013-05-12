<?php

class Grid_Database {
  
  function __construct($filename = 'app.db') {
    global $grid;
    $filename = GRID_DIR . "/data/$filename";
    if (!file_exists($filename)) {
      $this->setup($filename);
      $fire_setup_event = true;
    }
    $this->pdo = new PDO("sqlite:$filename");
    $grid->db = $this;
    if (!empty($fire_setup_event)) {
      $grid->fire_event('database_setup', $this);
    }
  }
  
  function setup($filename) {
    global $grid;
    if (!defined('SQLITE_BIN')) {
      define('SQLITE_BIN', '/usr/bin/sqlite3');
    }
    $dir = dirname($filename);
    if (!is_writable($dir)) {
      die("Database: could not write to $dir");
    }
    $setup_sql = GRID_DIR . '/app/setup.sql';
    if (!file_exists($setup_sql)) {
      die("Database: no setup SQL file found");
    }
    if (!file_exists(SQLITE_BIN)) {
      die("Database: can't setup because SQLITE_BIN (" . SQLITE_BIN . ") does not exist.");
    }
    $grid->log("Database: setting up $filename with $setup_sql");
    exec(SQLITE_BIN . " $filename < $setup_sql", $output);
    foreach ($output as $line) {
      $grid->log("  $line");
    }
  }
  
  function query($sql, $values = null) {
    global $grid;
    if (empty($values)) {
      $values = array();
    }
    $results = null;
    $query = $this->pdo->prepare($sql);
    if (empty($query)) {
      $error = $this->pdo->errorInfo();
      $error = implode(" ", $error);
      $grid->log("Database: $error ($sql)");
    } else {
      $values = array_values($values);
      $query->execute($values);
    }
    return $query;
  }
  
  function insert($table, $values) {
    $keys = array_keys($values);
    $columns = implode(', ', $keys);
    $placeholders = str_repeat('?, ', count($values));
    $placeholders = substr($placeholders, 0, -2);
    $values = array_values($values);
    $query = $this->query("
      INSERT INTO $table
      ($columns)
      VALUES ($placeholders)
    ", $values);
    if (!empty($query)) {
      return $this->pdo->lastInsertId();
    } else {
      return null;
    }
  }
  
  function select($table, $options = null) {
    if (!empty($options)) {
      extract($options);
    }
    if (empty($columns)) {
      $columns = '*';
    }
    if (!empty($where)) {
      $where = "WHERE $where";
    }
    if (!empty($count) || !empty($limit)) {
      $limit = @"LIMIT $count$limit";
      if (!empty($offset)) {
        $limit .= ", $offset";
      }
    }
    if (!empty($order)) {
      $order = "ORDER BY $order";
    }
    if (empty($values)) {
      $values = array();
    }
    $results = array();
    $query = $this->query(@"
      SELECT $columns
      FROM $table
      $where
      $order
      $limit
    ", $values);
    if (!empty($query) && $query->columnCount() > 0) {
      $results = $query->fetchAll(PDO::FETCH_CLASS, "Grid_Record");
      foreach ($results as $result) {
        $result->set_table($table);
      }
    }
    return $results;
  }
  
  function update($table, $content, $where, $where_values = null) {
    $assignments = array();
    $values = array();
    foreach ($content as $key => $value) {
      $assignments[] = "$key = ?";
      $values[] = $value;
    }
    if (!empty($where_values) && is_array($where_values)) {
      foreach ($where_values as $value) {
        $values[] = $value;
      }
    } else if (empty($where_values)) {
      $values[] = $where;
      $where = 'id = ?';
    }
    $assignments = implode(', ', $assignments);
    return $this->query("
      UPDATE $table
      SET $assignments
      WHERE $where
    ", $values);
  }
  
  function delete($table, $where, $values = null) {
    if (empty($values) && !empty($where)) {
      $values = array(
        'id' => $where
      );
      $where = "id = ?";
    }
    return $this->query("
      DELETE FROM $table
      WHERE $where
    ", $values);
  }
  
  function record($table, $where, $values = null) {
    if (empty($values) && !empty($where)) {
      $values = array($where);
      $where = "id = ?";
    }
    $results = $this->select($table, array(
      'where' => $where,
      'values' => $values
    ));
    if (count($results) > 0) {
      return $results[0];
    } else {
      return null;
    }
  }
  
}

?>
