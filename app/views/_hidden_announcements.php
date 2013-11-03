<?php

if (!empty($_SESSION['hidden_announcements'])) {
  $selector = '#' . implode(', #', $_SESSION['hidden_announcements']);
}

echo "<style>$selector { display: none; }</style>";

?>
