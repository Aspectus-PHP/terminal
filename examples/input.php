<?php

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\TerminalDevice;
use Revolt\EventLoop;

require \dirname(__DIR__) . '/vendor/autoload.php';

// setup stty
exec('stty -echo -icanon min 1 time 0 < /dev/tty');

$device = new TerminalDevice();

$device->subscribe(
    InputEvent::class,
    function (InputEvent $event) use ($device)
    {
        $input = strtolower($event->data);

        if (trim($input) === 'q') {
            $device->write("\ec");  // xterm reset
            $device->write("Goodbye!\n");

            exit();
        }

        $device->write("Input received: $event->data\n");
    }
);

$device->write("Feel free to type something (press Q to quit) \n");

EventLoop::run();