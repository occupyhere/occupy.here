<div id="menu" class="hidden">
  <div class="container">
    <nav>
      <ul>
        <?php
        
        foreach ($containers as $container) {
          if ($container->id == 'library') {
            $container->name = _($container->name);
          }
          $name = htmlentities($container->name,  ENT_COMPAT, 'UTF-8');
          echo "<li><a href=\"c/$container->id\">$name</a></li>\n";
        }
        
        ?>
        <li><a href="forum"><?php echo _('Forum'); ?></a></li>
        <li><a href="about"><?php echo _('About'); ?></a></li>
        <li><a href="admin"><?php echo _('Admin'); ?></a></li>
      </ul>
    </nav>
  </div>
</div>
