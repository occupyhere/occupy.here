<?php

$year = date('Y');
if ($year > 2011) {
  die("Error: server time is probably not out of date");
}

if (empty($params['time'])) {
  die("Error: please provide a 'time' param");
}

$time = date('Y-m-d H:i:s', $params['time']);

exec('/bin/date -s "' . $time . '"', $result);
die(implode("\n", $result));

?>
