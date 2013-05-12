<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $page_title; ?></title>
    <base href="<?php echo $base_path; ?>/">
    <?php $this->yield('header'); ?>
  </head>
  <body class="<?php echo $body_class; ?>" data-servertime="<?php echo time(); ?>" data-colors="<?php echo get_colors(); ?>">
    <div id="page">
      <?php
      
      if ($params['view'] != 'intro') {
        $this->partial('menu');
        
      ?>
      <header>
        <div id="announcements">
          <?php $this->yield('announcements'); ?>
        </div>
        <div id="top">
          <a href="#" id="menu-button" class="menu button" ontouchstart=""><span class="icon"></span> MENU</a>
          <a href="./#post" id="post-button" class="post button" ontouchstart=""><span class="icon"></span> POST</a>
          <div class="clear"></div>
          <?php if (!empty($back_url)) { ?>
            <a href="<?php echo $back_url; ?>" id="back"><span class="icon"></span><?php echo $back_title; ?></a>
          <?php } ?>
        </div>
      </header>
      <?php } ?>
      <?php $this->yield(); ?>
    </div>
    <?php $this->yield('footer'); ?>
  </body>
</html>
