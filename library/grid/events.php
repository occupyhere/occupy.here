<?php

class Grid_Events {
  
  function setup_events($event = null) {
    if (!isset($this->_events)) {
      $this->_events = array();
    }
    if (!empty($event)) {
      if (!isset($this->_events[$event])) {
        $this->_events[$event] = array();
      }
      return $this->_events[$event];
    }
  }
  
  function add_event($event, $callback) {
    $events = $this->setup_events($event);
    $this->_events[$event][] = $callback;
    return true;
  }
  
  function remove_event($event, $callback) {
    $events = $this->setup_events($event);
    $new_events = array();
    foreach ($events as $handler) {
      if ($handler != $callback) {
        $new_events[] = $handler;
      }
    }
    $this->_events[$event] = $new_events;
  }
  
  function fire_event($event) {
    $args = func_get_args();
    array_shift($args);
    $events = $this->setup_events($event);
    foreach ($events as $callback) {
      call_user_func_array($callback, $args);
    }
  }
  
}

?>
