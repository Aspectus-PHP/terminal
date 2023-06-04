<?php

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\Xterm;
use Revolt\EventLoop;

require \dirname(__DIR__) . '/vendor/autoload.php';

// setup stty
exec('stty -echo -icanon min 1 time 0 < /dev/tty');

$xterm = new Xterm();

register_shutdown_function(function () use ($xterm) {
    // will cancel the tracking mode
    $xterm
        ->write("\e[?1003l")
        ->eraseAll()
        ->flush();
});


$xterm->subscribe(
    Xterm\Event\MouseInputEvent::class,
    function (Xterm\Event\MouseInputEvent $event) use ($xterm) {
        $xterm
            ->moveCursorTo($event->y, 1)
            ->eraseBelow()      // quick workaround
            ->brightBlue()
            ->write('Y')

            ->moveCursorTo(1, $event->x)
            ->eraseLine()       // quick workaround
            ->brightMagenta()
            ->write('X')

            // both
            ->moveCursorTo(5, 1)
            ->eraseLine()
            ->moveCursorTo(5, 5)
            ->brightYellow()
            ->write("X: $event->x Y: $event->y")

            // cursor to follow mouse
            ->moveCursorTo($event->y, $event->x)

            // execute
            ->flush();
    }
);

$xterm->subscribe(
    InputEvent::class,
    function (InputEvent $event) {
        if (strtolower($event->data) === 'q') {
            exit();
        }
    }
);

$xterm
    ->setPrivateModeTrackMouseOnPressAndRelease()
    ->setPrivateModeTrackMouseAll()
    ->flush();

EventLoop::repeat(0.5, function () use ($xterm) {
    $xterm->eraseAll()->flush();
});

EventLoop::run();