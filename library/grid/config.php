<?php

class Grid_Config extends Grid_Events {
  
  function setup_config($filename) {
    if (!$this->load_config($filename)) {
      $this->generate_config($filename);
    }
  }
  
  function load_config($filename) {
    $this->_filename = $filename;
    $filename = GRID_DIR . '/config/' . $this->_filename;
    if (file_exists($filename)) {
      $contents = file_get_contents($filename);
      $config = json_decode($contents);
      $this->apply_config($config);
      return true;
    }
    return false;
  }
  
  function save_config($filename) {
    $vars = get_object_vars($this);
    $output = array();
    foreach ($vars as $key => $value) {
      if (!substr($key, 0, 1) == '_') {
        $output[$key] = $value;
      }
    }
    $filename = GRID_DIR . '/config/' . $this->_filename;
    $json = json_encode($output);
    file_put_contents($filename, $json);
  }
  
  function generate_config($filename) {
    $this->fire_event('generate_config');
    if (isset($this->_default_config)) {
      $this->apply_config($this->_default_config);
    }
    $this->save_config($filename);
  }
  
  function apply_config($config) {
    foreach ($config as $key => $value) {
      $this->$key = $value;
    }
  }
  
  function filter_config($value, $key) {
    echo "$value $key";
  }
  
}

?>
