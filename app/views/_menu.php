<div id="menu" class="hidden">
  <div class="container">
    <nav>
      <ul>
        <?php
        
        foreach ($containers as $container) {
          $name = htmlentities($container->name,  ENT_COMPAT, 'UTF-8');
          echo "<li><a href=\"c/$container->id\">$name</a></li>\n";
        }
        
        ?>
        <li><a href="forum">Forum</a></li>
        <li><a href="about">About</a></li>
      </ul>
    </nav>
  </div>
</div>
