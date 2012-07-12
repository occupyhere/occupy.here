<?php

class Grid_Record {
  
  protected $_table;
  
  function __construct($table = null, $values = null) {
    if (!empty($table)) {
      $this->_table = $table;
    }
    if (!empty($values) && is_array($values)) {
      foreach ($values as $key => $value) {
        $this->$key = $value;
      }
    }
  }
  
  function save($force_insert = false) {
    global $grid;
    if (empty($this->id) || $force_insert) {
      $id = $grid->db->insert(
        $this->_table,
        $this->get_values()
      );
      if (!empty($id)) {
        $this->id = $id;
      } else if (!empty($this->id) && $force_insert) {
        // Record exists, overwrite it
        echo "updating $this->id";
        $grid->db->update(
          $this->_table,
          $this->get_values(),
          "id = ?",
          array($this->id)
        );
      }
    } else {
      $grid->db->update(
        $this->_table,
        $this->get_values(),
        "id = ?",
        array($this->id)
      );
    }
  }
  
  function get_values() {
    $vars = get_object_vars($this);
    $values = array();
    foreach ($vars as $key => $value) {
      if (substr($key, 0, 1) != '_') {
        $values[$key] = $value;
      }
    }
    return $values;
  }
  
  function set_table($table) {
    $this->_table = $table;
  }
  
  function get_table() {
    return $this->_table;
  }
  
}

?>
