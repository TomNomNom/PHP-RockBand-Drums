<?php
$d = fopen('/dev/input/js0', 'rb');
while (true){
  $datas = fread($d, 8);
  $datas = unpack('C8', $datas);
  var_dump($datas);
}
