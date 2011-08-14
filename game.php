<?php
define('TRACK_FILE', './track.json');
define('TRACK_SIZE', 20);

define('DEVICE', '/dev/input/js0');
define('INPUT_WAIT', 0.3); 

define('RED_PAD',    2);
define('YELLOW_PAD', 3);
define('BLUE_PAD',   0);
define('GREEN_PAD',  1);

$drums = fopen(DEVICE, 'rb');
if (!$drums) die("Could not open device.\n");
stream_set_blocking($drums, 0);

$track = json_decode(file_get_contents(TRACK_FILE));
$points = 0;
while (true){
  system('clear'); 
  if (sizeOf($track) == 0) break;
  $activeLine = array_pop($track);
  drawTrack($activeLine, $track, TRACK_SIZE);
  echo "Score: {$points}\n";

  $pressed = array(
    RED_PAD    => false,
    YELLOW_PAD => false,
    BLUE_PAD   => false,
    GREEN_PAD  => false
  );
  
  $finish = microtime(true) + INPUT_WAIT; 
  while(microtime(true) <= $finish){
    //Wait for some input
    $input = fread($drums, 8);
    if (!$input){
      usleep(1);
      continue;
    }
    //C8 == 8 unsigned chars
    $input = unpack('C8', $input);
    $pressed[$input[8]] = true;
  }
  
  if ($pressed[RED_PAD]    && $activeLine[0]) $points++;
  if ($pressed[YELLOW_PAD] && $activeLine[1]) $points++;
  if ($pressed[BLUE_PAD]   && $activeLine[2]) $points++;
  if ($pressed[GREEN_PAD]  && $activeLine[3]) $points++;
  
  usleep(5000);
}
echo "Game over! Score: {$points}\n";



function drawTrack($activeLine, $track, $size){
  $track = array_reverse(
    array_pad(array_reverse($track), $size, array(0,0,0,0))
  );
  $track = array_slice($track, -$size, $size);
  $count = 0;

  echo "┏━┳━┳━┳━┓\n";
  foreach ($track as $line){
    $count++;
    if ($count > $size) break;
    drawLine($line);
  }
  echo "┣━╋━╋━╋━┫\n";
  drawLine($activeLine);
  echo "┗━┻━┻━┻━┛\n";
}

function drawLine($line){
    list($r, $y, $b, $g) = $line;
    $r = $r? r('◉'):'┄';
    $y = $y? y('◉'):'┄';
    $b = $b? b('◉'):'┄';
    $g = $g? g('◉'):'┄';
    echo "┃{$r}┃{$y}┃{$b}┃{$g}┃\n";
}

function color($str, $color){
  return "\033[{$color}m{$str}\033[37m";
}
function r($str){ return color($str, '31'); }
function g($str){ return color($str, '32'); }
function b($str){ return color($str, '34'); }
function y($str){ return color($str, '33'); }

