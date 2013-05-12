<?php

class Grid_Response {
  
  function __construct() {
    $this->content = array();
  }
  
  function setup($request) {
    global $grid;
    $grid->fire_event('setup_response');
    $this->namespace = array(
      'grid' => $grid,
      'params' => $grid->request->params
    );
    if (!empty($request->params['layout'])) {
      $this->setup_target('layout', $request->params['layout']);
    } else {
      $this->layout = null;
    }
    $this->setup_target('view', $request->params['view']);
    if (!empty($this->layout)) {
      $layout_setup = GRID_DIR . '/app/layouts/' . preg_replace('/\.php$/', '.setup.php', $this->layout);
      $this->namespace_setup($layout_setup);
    }
    
    $view_dir = GRID_DIR . '/app/views';
    if (dirname($this->view) != '.') {
      $view_dir .= '/' . dirname($this->view);
    }
    
    $this->namespace_setup("$view_dir/setup.php");
    $view_setup = preg_replace('/\.php$/', '.setup.php', basename($this->view));
    $this->namespace_setup("$view_dir/$view_setup");
  }
  
  function setup_target($name, $target) {
    if (substr($target, -4, 4) != '.php') {
      $target = "$target.php";
    }
    $this->$name = $target;
  }
  
  function namespace_setup($setup) {
    if (file_exists($setup)) {
      extract($this->namespace);
      require_once $setup;
      $namespace = get_defined_vars();
      unset($namespace['setup']);
      $this->namespace = array_merge($this->namespace, $namespace);
    }
  }
  
  function respond() {
    extract($this->namespace);
    chdir(GRID_DIR . '/app/views');
    if (!empty($this->layout)) {
      include GRID_DIR . "/app/layouts/$this->layout";
    } else {
      include $this->view;
    }
  }
  
  function yield($name = '') {
    if (empty($name)) {
      $this->include_view($this->view);
    } else if (isset($this->content[$name])) {
      foreach ($this->content[$name] as $content) {
        $index = strpos($content, ':');
        $type = substr($content, 0, $index);
        $value = substr($content, $index + 1);
        if ($type == 'content') {
          echo $value;
        } else if ($type == 'partial') {
          $this->partial($value);
        }
      }
    }
  }
  
  function partial($partial, $vars = null) {
    if (substr($partial, -4, 4) != '.php') {
      $partial = "$partial.php";
    }
    if (substr(basename($partial), 0, 1) != '_') {
      if (basename($partial) != $partial) {
        $partial = dirname($partial) . '/_' . basename($partial);
      } else {
        $partial = "_$partial";
      }
    }
    $this->include_view($partial, $vars);
  }
  
  function include_view($__file, $vars = null) {
    global $grid;
    if (file_exists($__file)) {
      $prev_cwd = getcwd();
      chdir(dirname($__file));
      extract($this->namespace);
      if (!empty($vars) && is_array($vars)) {
        extract($vars);
      }
      $setup = preg_replace('/\.php$/', '.setup.php', $__file);
      if (file_exists($setup)) {
        require_once $setup;
      }
      include "$prev_cwd/$__file";
      chdir($prev_cwd);
      return true;
    } else {
      $grid->log("include_view '$__file' not found");
      return false;
    }
  }
  
  function redirect($path) {
    header("Location: $path");
  }
  
  function content_for($name, $content, $mode = 'append') {
    $this->defer_content($name, "content:$content", $mode);
  }
  
  function partial_for($name, $partial, $mode = 'append') {
    $this->defer_content($name, "partial:$partial", $mode);
  }
  
  function defer_content($name, $value, $mode) {
    if (!isset($this->content[$name])) {
      $this->content[$name] = array();
    }
    if ($mode == 'replace') {
      $this->content[$name] = array($value);
    } else if ($mode == 'append') {
      array_push($this->content[$name], $value);
    } else if ($mode == 'prepend') {
      array_unshift($this->content[$name], $value);
    }
  }
  
  function stylesheet($name, $media = 'all') {
    if (substr($name, -4, 4) != '.css') {
      $name = "$name.css";
    }
    if (substr($name, 0, 1) == '/' ||
        substr($name, 0, 5) == 'http:' ||
        substr($name, 0, 6) == 'https:') {
      $path = $name;
    } else {
      $path = "css/$name";
    }
    $content = "<link rel=\"stylesheet\" href=\"$path\" media=\"$media\" />\n";
    $this->content_for('header', $content, 'append');
  }
  
  function javascript($name, $where = 'footer') {
    if (substr($name, -3, 3) != '.js') {
      $name = "$name.js";
    }
    if (substr($name, 0, 1) == '/' ||
        substr($name, 0, 5) == 'http:' ||
        substr($name, 0, 6) == 'https:') {
      $path = $name;
    } else {
      $path = "js/$name";
    }
    $content = "<script src=\"$path\"></script>\n";
    $this->content_for($where, $content, 'append');
  }
  
  function link($rel, $href) {
    $content = "<link rel=\"$rel\" href=\"$href\" />\n";
    $this->content_for('header', $content, 'append');
  }
  
  function meta($name, $content) {
    $content = "<meta name=\"$name\" content=\"$content\" />\n";
    $this->content_for('header', $content, 'append');
  }
  
}

?>
