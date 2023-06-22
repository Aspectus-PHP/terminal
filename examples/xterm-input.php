<?php

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\Xterm;
use Aspectus\Terminal\Xterm\Event\SpecialKeyEvent;
use Revolt\EventLoop;

require \dirname(__DIR__) . '/vendor/autoload.php';

// setup stty
exec('stty -echo -icanon min 1 time 0 < /dev/tty');

$xterm = new Xterm();

$xterm->subscribe(
    InputEvent::class,
    function (InputEvent $event) use ($xterm) {
        $xterm->write("Input received: $event->data\n")->flush();
    }
);

$xterm->subscribe(
    SpecialKeyEvent::class,
    function (SpecialKeyEvent $event) use ($xterm) {
        if ($event->data === SpecialKeyEvent::ESC) {
            $xterm
                ->write("Goodbye!\n")
                ->flush();

            exit();
        }

        $xterm->write("Input received: $event->data\n")->flush();
    }
);

$xterm->write("Feel free to type something (press ESC to quit) \n")->flush();

EventLoop::run();
