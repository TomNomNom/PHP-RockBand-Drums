<?php
define('DEVICE', '/dev/input/js0');
define('INPUT_WAIT', 0.02); 

define('RED_PAD',    2);
define('YELLOW_PAD', 3);
define('BLUE_PAD',   0);
define('GREEN_PAD',  1);

$colourCodes = array(
  RED_PAD    => 41,
  YELLOW_PAD => 43,
  BLUE_PAD   => 44,
  GREEN_PAD  => 42,
  'black'    => 40
);

$drums = fopen(DEVICE, 'rb');
if (!$drums) die("Could not open device.\n");
stream_set_blocking($drums, 0);

//Main loop
while (true){
  $button = null;
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
    $button = $input[8];
  }

  system('clear'); 
  colourScreen($colourCodes['black']);
  if (isset($colourCodes[$button])){
    colourScreen($colourCodes[$button]);
  }

  usleep(5000);
}

function colourScreen($color){
  echo "\033[{$color}m\033[37m";
}
