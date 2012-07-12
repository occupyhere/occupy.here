<?php

class Grid_Routes extends Grid_Config {
  
  var $_defaults = array(
    'routes' => array()
  );
  
  function __construct() {
    $this->setup_config('routes.json');
  }
  
  
  
}

?>
