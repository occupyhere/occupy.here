<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,maximum-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <base href="<?php echo $base_path; ?>/" />
    <?php $this->yield('header'); ?>
  </head>
  <body class="loading <?php echo $body_class; ?>" data-servertime="<?php echo time(); ?>">
    <div id="page">
      <header>
        <a href=".">
          <h1>occupy.here</h1>
          distributed wifi occupation
        </a>
      </header>
      <div id="announcements">
        <?php $this->yield('announcements'); ?>
      </div>
      <?php $this->yield(); ?>
      <nav>
        <div id="user" data-username="<?php echo $grid->user->name; ?>">
          You are <a href="account"><?php echo $username; ?></a>
          <?php if (!empty($grid->user->name)) { ?>
            <a href="logout" class="logout">Logout</a>
          <?php } ?>
        </div>
        <ul>
          <li><a href=".">Home</a></li>
          <li><a href="about">About</a></li>
          <li><a href="backup">Backup</a></li>
        </ul>
      </nav>
    </div>
    <?php $this->yield('footer'); ?>
  </body>
</html>
