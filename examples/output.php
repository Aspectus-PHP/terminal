<?php

use Aspectus\Terminal\TerminalDevice;

require \dirname(__DIR__) . '/vendor/autoload.php';

$device = new TerminalDevice();

foreach (range(1,10) as $i) {
    $device->write("This is iteration #$i\n");
}
