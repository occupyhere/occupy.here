<?php

class Grid_Request {
  
  function __construct($path, $method = 'get') {
    $this->path = $path;
    $this->method = $method;
    $this->params = array();
  }
  
  function setup($routes, $offset = 0) {
    $params = array(
      'view' => '404',
      'layout' => 'layout'
    );
    if (count($routes) >= $offset) {
      $routes = array_slice($routes, $offset);
    } else {
      return;
    }
    foreach ($routes as $index => $route) {
      if ($route['method'] != $this->method) {
        continue;
      }
      $path = $route['path'] == '/' ? $route['path'] : substr($route['path'], 1);
      $pattern = str_replace('/', '\/', $path);
      $pattern = str_replace('.', '\.', $pattern);
      preg_match_all('/\$(\w+)/', $pattern, $matches);
      if (!empty($matches) && !empty($matches[1])) {
        $vars = $matches[1];
      } else if (!empty($route['vars'])) {
        $vars = $route['vars'];
      }
      $pattern = preg_replace('/\$(\w+)/', '([^\/]+?)', $pattern);
      $regex = "/^$pattern$/";
      
      if (preg_match($regex, $this->path, $values)) {
        $this->route_index = $index;
        array_shift($values);
        //print_r($vars);
        foreach ($values as $index => $value) {
          $key = $vars[$index];
          $params[$key] = $value;
        }
        $params['view'] = $route['view'];
        if (isset($route['layout'])) {
          $params['layout'] = $route['layout'];
        }
        break;
      }
    }
    $this->set_params($this->check_magic_quotes($_REQUEST));
    $this->set_params($params);
  }
  
  function check_magic_quotes($params) {
    if (get_magic_quotes_gpc()) {
      $cleaned = array();
      foreach ($params as $key => $value) {
        $key = stripslashes($key);
        $cleaned[$key] = stripslashes($value);
      }
      return $cleaned;
    } else {
      return $params;
    }
  }
  
  function set_params($params) {
    $this->params = array_merge($this->params, $params);
  }
  
}

?>
