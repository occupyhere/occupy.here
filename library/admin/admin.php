<?php

class Admin {
  
  function __construct() {
    global $grid;
    $grid->add_event('page_load', array($this, 'page_load'));
  }
  
  function page_load($response) {
    //$response->javascript('admin.js');
  }
  
}

?>
