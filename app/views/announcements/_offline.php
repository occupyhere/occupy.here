<div class="announcement<?php if (!empty($has_seen)) { echo ' minimized'; } ?>" id="offline_announcement">
  <a href="#" class="close top-right">&times;</a>
  <h2><?php echo _('Welcome to occupy.here!'); ?></h2>
  <a href="#" class="more"><?php echo _('Expand'); ?></a>
  <p><?php echo _('You are not connected to the Internet. This wifi network is a kind of LAN island where you can share messages and files with those nearby. It is part of an archipelago of afiliated occupy.here nodes, so be aware that things you post here will persist and be copied onto other nodes.'); ?></p>
  <div class="buttons">
    <a href="/intro/" class="button"><?php echo _('About') . ' Occupy.here'; ?></a><a href="#" class="close button"><?php echo _('Hide'); ?></a>
  </div>
</div>
