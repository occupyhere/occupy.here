<?php

$ext = '';
if (preg_match('/\.([^.]+)$/', $file->path, $matches)) {
  $ext = $matches[1];
}
echo "<a href=\"uploads/$file->path\" class=\"download\">download $ext</a>&nbsp;&nbsp;";

?>
