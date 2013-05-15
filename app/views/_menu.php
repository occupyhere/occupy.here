<div id="menu" class="hidden">
  <div class="container">
    <h1>occupy.here</h1>
    <nav>
      <ul>
        <li><a href="./">Home</a></li>
        <li><a href="about">About</a></li>
        <?php
        
        foreach ($containers as $container) {
          $name = htmlentities($container->name,  ENT_COMPAT, 'UTF-8');
          echo "<li><a href=\"c/$container->id\">$name</a></li>\n";
        }
        
        ?>
        <li><a href="files">All Files</a></li>
      </ul>
    </nav>
  </div>
</div>
